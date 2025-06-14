<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\PortalPage;
use App\Models\RoleDetails;
use Illuminate\Http\Request;

class PermissionController extends Controller
{
    // View all
    public function index()
    {
        $tokenData = app('token_data');
        $organizationId = $tokenData['organization_id'];

        $pages = PortalPage::all(['id', 'name', 'type']);

        foreach ($pages as $page) {
            $permissions = RoleDetails::where('portal_page_id', $page->id)
                        ->get();

            foreach ($permissions as $permission) {
                $pagesWithPermissions[] = [
                    'id' => $permission->id,
                    'page_id' => $page->id,
                    'role_id' => $permission->role_id,
                    'type' => $page->type,
                    'name' => $page->name,
                    'create' => $permission->create == 1,
                    'view' => $permission->view == 1,
                    'edit' => $permission->edit == 1,
                    'remove' => $permission->remove == 1,
                    'export' => $permission->export == 1,
                    'print' => $permission->print == 1,
                    'send' => $permission->send == 1,
                ];
            }
        }

        return response()->json([
            'permissions' => $pagesWithPermissions,
            'pages' => $pages
        ]);
    }

    // Create
    public function store(Request $request)
    {
        $roleId = $request->input('role_id');
        $permissions = $request->input('permissions');
        $userId = auth()->id();

        foreach ($permissions as $permission) {
            $roleDetails = RoleDetails::where([
                'role_id' => $roleId,
                'portal_page_id' => $permission['id']
            ])->first();

            if ($roleDetails) {
                $roleDetails->update([
                    'create' => $permission['create'] ?? false,
                    'view' => $permission['view'] ?? false,
                    'edit' => $permission['edit'] ?? false,
                    'remove' => $permission['remove'] ?? false,
                    'export' => $permission['export'] ?? false,
                    'print' => $permission['print'] ?? false,
                    'send' => $permission['send'] ?? false,
                    'updated_by' => $userId
                ]);
            } else {
                RoleDetails::create([
                    'role_id' => $roleId,
                    'portal_page_id' => $permission['id'],
                    'create' => $permission['create'] ?? false,
                    'view' => $permission['view'] ?? false,
                    'edit' => $permission['edit'] ?? false,
                    'remove' => $permission['remove'] ?? false,
                    'export' => $permission['export'] ?? false,
                    'print' => $permission['print'] ?? false,
                    'send' => $permission['send'] ?? false,
                    'created_by' => $userId
                ]);
            }
        }

        return response()->json(['message' => 'Permissions processed successfully']);
    }
}
