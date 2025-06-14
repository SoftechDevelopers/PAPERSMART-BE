<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Organization;
use Laravel\Passport\Token;
use Laravel\Passport\RefreshToken;
use Carbon\Carbon;
use App\Services\FileStorage;

class AuthController extends Controller
{
    // Login
    public function login(Request $request)
    {
        $request->validate([
            'username' => 'required',
            'password' => 'required',
            'device_id' => 'sometimes|string',
        ]);

        $user = User::where('username', $request->username)->first();

        if ($user && $user->status && md5($request->password) === $user->password) {
            // Get role information
            $role = \DB::table('role')
                        ->where('id', $user->role_id)
                        ->first();

            // Get permissions for this role from role_details
            $permissions = \DB::table('role_details')
                            ->where('role_id', $user->role_id)
                            ->get();

            // Get organization details
            $organization = Organization::where('id', $user->organization_id)
                            ->select(['id', 'name', 'address2', 'district', 'state', 'pincode', 'logo'])
                            ->first(); // Retrieve a single object

            if ($organization) {
                $organization->logo = FileStorage::getUrl('organizations',  $organization->logo);
            }

            // Prepare the userData object
            $userData = [
                'name' => $user->name,
                'avatar'=> null,
                'organization' => $organization,
                'fiscal' => [],
                'currentFiscal' => null,
                'fiscalRange' =>[],
                'roleName' => $role->role_name,
                'role' => []
            ];

            foreach ($permissions as $permission) {
                $page = \DB::table('portal_page')
                        ->where('id', $permission->page_id)
                        ->first();

                if ($page) {
                    $actions = [];

                    if ($permission->create == 1) {
                        $actions[] = 'create';
                    }
                    if ($permission->view == 1) {
                        $actions[] = 'view';
                    }
                    if ($permission->edit == 1) {
                        $actions[] = 'edit';
                    }
                    if ($permission->remove == 1) {
                        $actions[] = 'delete';
                    }
                    if ($permission->export == 1) {
                        $actions[] = 'export';
                    }
                    if ($permission->print == 1) {
                        $actions[] = 'print';
                    }
                    if ($permission->send == 1) {
                        $actions[] = 'send';
                    }

                    $userData['role'][] = [
                        'subject' => $page->endpoint,
                        'actions' => $actions
                    ];
                }
            }

            if ($user->staff_id) {
                $staff = \DB::table('staff')
                            ->where('id', $user->staff_id)
                            ->first();

                if ($staff) {
                    $userData['name'] = $staff->name;
                    $userData['avatar'] = FileStorage::getUrl('staff', $staff->photo_url);
                }
            }

            $fiscalNames = \DB::table('fiscal')->pluck('fiscal_name');
            $userData['fiscal'] = $fiscalNames;

            $fiscalRange = \DB::table('fiscal')->get(['fiscal_name', 'start_date', 'end_date']);
            $userData['fiscalRange'] = $fiscalRange->map(function($item) {
                return[
                    'fiscal' => $item->fiscal_name,
                    'start_date' => Carbon::parse($item->start_date)->format('d-m-Y'),
                    'end_date' => Carbon::parse($item->end_date)->format('d-m-Y'),
                ];
            });

            $currentFiscal = \DB::table('fiscal')
                                ->where('current_fiscal', 1)
                                ->first();
            $userData['currentFiscal'] = $currentFiscal->fiscal_name;

            // Generate access and refresh tokens
            $tokenResult = $user->createToken('Personal Access Token');

            // Store the generated access token in the 'token' column
            $tokenRecord = $tokenResult->token;
            $tokenRecord->token = $tokenResult->accessToken;
            $tokenRecord->organization_id = $user->organization_id;
            $tokenRecord->fiscal = $currentFiscal->fiscal_name;
            $tokenRecord->save();

            $refreshToken = $this->createRefreshToken($tokenRecord->id, $request->device_id);

            // Return the response with userData
            return response()->json([
                'accessToken' => $tokenResult->accessToken,
                'refreshToken' => $refreshToken,
                'userData' => $userData
            ]);
        }

        return response()->json(['error' => 'Unauthorized'], 401);
    }

    // Logout
    public function logout(Request $request)
    {
        $accessToken = $request->bearerToken();

        // if ($accessToken) {
            $token = Token::where('token', $accessToken)->first();

            if ($token) {
                RefreshToken::where('access_token_id', $token->id)->delete();
                $token->delete();

                return response()->json([
                    'message' => 'Logged out successfully!',
                    'status_code' => 200
                ], 200);
            }
        // }

        // return response()->json(['error' => 'Token not provided or invalid'], 401);
    }

    // Validate
    public function validateAccessToken(Request $request)
    {
        $accessToken = $request->bearerToken();

        if (!$accessToken) {
            return response()->json(['error' => 'Access token not provided'], 401);
        }

        $token = Token::where('token', $accessToken)->first();

        if (!$token || $token->revoked) {
            return response()->json(['error' => 'Invalid or revoked access token'], 401);
        }

        return response()->json([
            'message' => 'Valid token!',
            'status_code' => 200
        ], 200);
    }

    private function createRefreshToken($accessTokenId, $deviceId)
    {
        $refreshToken = RefreshToken::create([
            'id' => \Str::random(40),
            'access_token_id' => $accessTokenId,
            'revoked' => false,
            'expires_at' => $deviceId ? Carbon::now()->addDays(30) : Carbon::now()->addHours(12),
        ]);

        return $refreshToken->id;
    }

    public function refresh(Request $request)
    {
        $fiscal = $request->query('fiscal');
        $refreshToken = $request->bearerToken();

        $validRefreshToken = RefreshToken::where('id', $refreshToken)
                                 ->where('revoked', false)
                                 ->where('expires_at', '>', now())
                                 ->first();

        if ($validRefreshToken) {
            $accessToken = Token::find($validRefreshToken->access_token_id);
            if (!$accessToken) {
                return response()->json(['error' => 'Invalid access token'], 401);
            }

            $accessToken->delete();

            $user = $accessToken->user;
            $tokenResult = $user->createToken('Personal Access Token');
            $tokenRecord = $tokenResult->token;
            $tokenRecord->token = $tokenResult->accessToken;
            $tokenRecord->organization_id = $user->organization_id;
            $tokenRecord->fiscal = $fiscal ?: $accessToken->fiscal;

            $tokenRecord->save();

            $validRefreshToken->access_token_id = $tokenRecord->id;
            $validRefreshToken->save();

            return response()->json([
                'accessToken' => $tokenResult->accessToken,
                'refreshToken' => $validRefreshToken->id
            ]);
        }

        return response()->json(['error' => 'Invalid refresh token'], 401);
    }
}
