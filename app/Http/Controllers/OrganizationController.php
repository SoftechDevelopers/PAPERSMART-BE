<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Organization;
use App\Services\FileStorage;

class OrganizationController extends Controller
{
    // Create
    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'name' => 'required',
            'address2' => 'required',
            'district' => 'required',
            'state' => 'required',
            'country' => 'required',
            'pincode' => 'required|numeric',
            'contact' => 'required|numeric',
            'email' => 'required|email',
            'file' => 'required|file|mimes:png,jpg',
        ]);

        $createdBy = auth()->id();

        try {
            $fileUrl = null;

            if ($request->hasFile('file')) {
                $file = $request->file('file');
                $fileUrl = FileStorage::storeFileInAssets('organizations', $file);
            }

            Organization::create([
                'name' => $validatedData['name'],
                'address1' => $request->address1 ?? null,
                'address2' => $validatedData['address2'],
                'district' => $validatedData['district'],
                'state' => $validatedData['state'],
                'country' => $validatedData['country'],
                'pincode' => $validatedData['pincode'],
                'contact' => $validatedData['contact'],
                'email' => $validatedData['email'],
                'logo' => $fileUrl ?? '',
                'status' => 1,
                'created_by' => $createdBy
            ]);

            return response()->json([
                'message' => 'Organization created successfully'
            ], 201);
        } catch (\Illuminate\Database\QueryException $exception) {
            if ($exception->getCode() === '23000') {
                return response()->json([
                    'message' => 'Email exists',
                    'error' => $exception->getMessage()
                ], 409);
            }

            return response()->json([
                'message' => 'An error occurred while adding the organization',
                'error' => $exception->getMessage()
            ], 500);
        }
    }

    // View all
    public function index() {

        $organizations = Organization::where('status', 1)
                    ->get(['id', 'name', 'address1', 'address2', 'district', 'state', 'country', 'pincode', 'contact', 'email', 'logo']);


        $formattedOrganizations = $organizations->map(function ($organization) {
        return [
            'id' => $organization->id,
            'name' => $organization->name,
            'address1' => $organization->address1 ?? null,
            'address2' => $organization->address2,
            'district' => $organization->district,
            'state' => $organization->state,
            'country' => $organization->country,
            'pincode' => $organization->pincode,
            'contact' => $organization->contact,
            'email' => $organization->email,
            'logo' => $organization->logo ? FileStorage::getUrl('organizations', $organization->logo) : null,
            'filename' => $organization->logo ? $organization->logo : null
        ];
    });

        return response()->json( $formattedOrganizations);
    }

    // Update
    public function update(Request $request, $id) {

        $validatedData = $request->validate([
            'name' => 'required',
            'address2' => 'required',
            'district' => 'required',
            'state' => 'required',
            'country' => 'required',
            'pincode' => 'required|numeric',
            'contact' => 'required|numeric',
            'email' => 'required|email',
            'file' => 'nullable|file|mimes:png,jpg',
        ]);

        $organization = Organization::findOrFail($id);
        $updatedBy = auth()->id();

        try {
            $fileUrl = null;

            if ($request->hasFile('file')) {
                $file = $request->file('file');
                $fileUrl = FileStorage::storeFileInAssets('organizations', $file);

                if (!empty($organization->logo)) {
                    FileStorage::deleteFile('organizations', $organization->logo);
                }
            }

            $organizationData = [
                'name' => $validatedData['name'],
                'address1' => $request->address1 ?? null,
                'address2' => $validatedData['address2'],
                'district' => $validatedData['district'],
                'state' => $validatedData['state'],
                'country' => $validatedData['country'],
                'pincode' => $validatedData['pincode'],
                'contact' => $validatedData['contact'],
                'email' => $validatedData['email'],
                'status' => 1,
                'updated_by' => $updatedBy
            ];

            if ($fileUrl !== null) {
                $organizationData['logo'] = $fileUrl;
            }

            $organization->update($organizationData);

            return response()->json([
                'message' => 'Organization updated successfully'
            ], 201);
        } catch (\Illuminate\Database\QueryException $exception) {
            if ($exception->getCode() === '23000') {
                return response()->json([
                    'message' => 'Email exists',
                    'error' => $exception->getMessage()
                ], 409);
            }

            return response()->json([
                'message' => 'An error occurred while adding the organization',
                'error' => $exception->getMessage()
            ], 500);
        }

    }
}
