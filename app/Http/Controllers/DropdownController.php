<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Services\FileStorage;
use App\Models\Role;
use App\Models\School;
use Illuminate\Support\Carbon;

class DropdownController extends Controller
{
    public function getDropdownData(Request $request)
    {
        $tokenData = app('token_data');
        $organizationId = $tokenData['organization_id'];
        $requestedTables = explode(',', $request->query('tables'));
        $response = [];

        foreach ($requestedTables as $table) {
            $response[$table] = $this->getDataFor($table, $organizationId);
        }

        return response()->json($response);
    }

    private function getDataFor($table, $organizationId)
    {
        return match ($table) {
            'role' => $this->getRoles($organizationId),           
            'school' => $this->getSchools($organizationId),          
            default => [],
        };
    }

    private function getRoles($orgId){
        return Role::when($orgId !== 1, function ($query) {                
                $query->where('id', '!=', 1);
            })
            ->orderBy('id')
            ->get(['id', 'role_name'])
            ->map(fn($item) => [
                'id' => $item->id,
                'name' => $item->role_name,
            ]);
    }

    private function getSchools($orgId){
        return School::where('organization_id', $orgId)
            ->where('status', 1)
            ->orderBy('id')
            ->get(['id', 'name', 'alias'])
            ->map(fn($item) => [
                'id' => $item->id,
                'name' => $item->alias,
                'title' => $item->name
            ]);
    } 
    

    // private function getTechnicians($orgId){
    //     return Staff::where('status', 'Working')
    //         ->where('organization_id', $orgId)
    //         ->whereHas('user', fn($q) => $q->where('role_id', 8))
    //         ->orderBy('id')
    //         ->get(['id', 'name', 'photo_url'])
    //         ->map(fn($item) => [
    //             'id' => $item->id,
    //             'name' => $item->name,
    //             'avatar' => FileStorage::getUrl('staff', $item->photo_url),
    //         ]);
    // }     
}


