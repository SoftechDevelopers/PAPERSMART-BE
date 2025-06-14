<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Vendor;
use Illuminate\Http\Request;
use App\Services\FileStorage;
use App\Models\Role;
use App\Models\Staff;
use App\Models\Partner;
use App\Models\Client;
use App\Models\TicketCategory;
use App\Models\TicketType;
use App\Models\Designation;
use App\Models\ProposalType;
use App\Models\Item;
use App\Models\ProposalNote;
use App\Models\BankAccount;
use App\Models\Location;
use App\Models\ExpenseHead;
use App\Models\Recipient;
use App\Models\Head;
use App\Models\OrganizationBusiness;
use App\Models\Category;
use App\Models\Warehouse;
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
            'role' => $this->getRoles(),
            'staff' => $this->getStaff($organizationId),
            'partner' => $this->getPartners($organizationId),
            'client' => $this->getClients($organizationId),
            'ticket_category' => TicketCategory::orderBy('id')->get(['id', 'name']),
            'ticket_type' => TicketType::orderBy('id')->get(['id', 'name']),
            'technician' => $this->getTechnicians($organizationId),
            'designation' => Designation::orderBy('id')->get(['id', 'name']),
            'proposal_type' => ProposalType::orderBy('id')->get(['id', 'name']),
            'item' => $this->getItems($organizationId),
            'note' => ProposalNote::orderBy('id')->get(['id', 'name']),
            'bank' => $this->getBankAccounts($organizationId),
            'location' => $this->getLocations($organizationId),
            'expense_head' => $this->getExpenseHeads($organizationId),
            'recipient' => $this->getRecipients($organizationId),
            'organization_business' => $this->getOrganizationBusiness($organizationId),
            'agreemented_client' =>$this->getAgreementedClients($organizationId),
            'contracted_vendor' =>$this->getContractedVendors($organizationId),
            'vendor' => $this->getVendors($organizationId),
            'head' => $this->getHeads($organizationId),
            'all_staff' => $this->getAllStaff($organizationId),
            'all_partner' => $this->getAllPartner($organizationId),
            'category' => Category::orderBy('id')->get(['id', 'name']),
            'warehouse' => $this->getWarehouse($organizationId),
            default => [],
        };
    }

    private function getRoles(){
        return Role::orderBy('id')->get(['id', 'role_name'])->map(fn($r) => [
            'id' => $r->id,
            'name' => $r->role_name,
        ]);
    }

    private function getStaff($orgId){
        return Staff::where('status', 'Working')
            ->where('organization_id', $orgId)
            ->orderBy('id')
            ->get(['id', 'name', 'docs_url'])
            ->map(function ($item) {
                $item->docs_url = $item->docs_url ? FileStorage::getUrl('staff', $item->docs_url) : null;
                return $item;
            });
    }

    private function getPartners($orgId){
        return Partner::where('status', 'Working')
            ->where('organization_id', $orgId)
            ->get(['id', 'name']);
    }

    private function getClients($orgId){
        return Client::where('organization_id', $orgId)
            ->orderBy('id')
            ->get(['id', 'address2'])
            ->map(fn($c) => [
                'id' => $c->id,
                'name' => $c->address2
            ]);
    }

    private function getTicketCategories(){
        return TicketCategory::get(['id', 'name']);
    }

    private function getTicketTypes(){
        return TicketType::orderBy('id')->get(['id', 'name']);
    }

    private function getTechnicians($orgId){
        return Staff::where('status', 'Working')
            ->where('organization_id', $orgId)
            ->whereHas('user', fn($q) => $q->where('role_id', 8))
            ->orderBy('id')
            ->get(['id', 'name', 'photo_url'])
            ->map(fn($item) => [
                'id' => $item->id,
                'name' => $item->name,
                'avatar' => FileStorage::getUrl('staff', $item->photo_url),
            ]);
    }

    private function getDesignations(){
        return Designation::orderBy('id')->get(['id', 'name']);
    }

    private function getProposalTypes(){
        return ProposalType::orderBy('id')->get(['id', 'name']);
    }

    private function getItems($orgId){
        return Item::orderBy('id')
            ->where('organization_id', $orgId)
            ->get(['id', 'category_id', 'name', 'model', 'manufacturer', 'unit', 'cgst', 'sgst', 'igst']);
    }

    private function getProposalNotes(){
        return ProposalNote::orderBy('id')->get(['id', 'name']);
    }

    private function getBankAccounts($orgId){
        return BankAccount::orderBy('id')
            ->where('organization_id', $orgId)
            ->get(['id', 'account_no'])
            ->map(fn($b) => [
                'id' => $b->id,
                'name' => $b->account_no,
            ]);
    }

    private function getLocations($orgId){
        return Location::orderBy('id')
            ->where('organization_id', $orgId)
            ->get(['id', 'address'])
            ->map(fn($loc) => [
                'id' => $loc->id,
                'name' => $loc->address,
            ]);
    }

    private function getExpenseHeads($orgId){
        return ExpenseHead::orderBy('id')
            ->where('organization_id', $orgId)
            ->get(['id', 'name', 'particulars', 'type']);
    }

    private function getRecipients($orgId){
        return Recipient::orderBy('id')
            ->where('status', 1)
            ->where('organization_id', $orgId)
            ->where('type', 'Beneficiary')
            ->get(['id', 'name']);
    }

    private function getOrganizationBusiness($orgId){
        return OrganizationBusiness::with(['business_type:id,name'])
            ->where('organization_id', $orgId)
            ->where('status', 1)
            ->get()
            ->map(function ($business) {
                $business->idExists = false;
                return $business;
            });
    }

    private function getAgreementedClients($orgId) {
        return Client::whereHas('agreements', function ($query) use ($orgId) {
            $query->where('organization_id', $orgId)
                  ->where('status', 'Active')
                  ->where('end_date', '>=', Carbon::now('Asia/Kolkata')->toDateString());
        })
        ->orderBy('id')
        ->get(['id', 'address2'])
        ->map(fn($client) => [
            'id' => $client->id,
            'name' => $client->address2,
        ]);
    }

    private function getContractedVendors($orgId) {
        return Vendor::whereHas('vendors', function ($query) use ($orgId) {
            $query->where('organization_id', $orgId)
                  ->where('status', 'Active')
                  ->where('end_date', '>=', Carbon::now('Asia/Kolkata')->toDateString());
        })
        ->orderBy('id')
        ->get(['id', 'name'])
        ->map(fn($vendor) => [
            'id' => $vendor->id,
            'name' => $vendor->name,
        ]);
    }

    private function getVendors($orgId) {
        return Vendor::where('organization_id', $orgId)
            ->where('status', 1)
            ->orderBy('id')
            ->get(['id', 'name'])
            ->map(fn($vendor) => [
                'id' => $vendor->id,
                'name' => $vendor->name
            ]);
    }

    private function getHeads($orgId) {
        return Head::where('organization_id', $orgId)
            ->where('status', 1)
            ->orderBy('id')
            ->get(['id', 'name', 'ledger_group_id'])
            ->map(fn($head) => [
                'id' => $head->id,
                'name' => $head->name,
                'ledger_group_id' => $head->ledger_group_id
            ]);
    }

    private function getAllStaff($orgId){
        return Staff::where('organization_id', $orgId)
            ->orderBy('id')
            ->get(['id', 'name'])
            ->map(fn($staff) => [
                'id' => $staff->id,
                'name' => $staff->name,
            ]);
    }

    private function getAllPartner($orgId){
        return Partner::where('organization_id', $orgId)
            ->orderBy('id')
            ->get(['id', 'name'])
            ->map(fn($partner) => [
                'id' => $partner->id,
                'name' => $partner->name,
            ]);
    }

    private function getWarehouse($orgId){
        return Warehouse::where('organization_id', $orgId)
            ->orderBy('id')
            ->get(['id', 'alias'])
            ->map(fn($w) => [
                'id' => $w->id,
                'name' => $w->alias
            ]);
    }

}


