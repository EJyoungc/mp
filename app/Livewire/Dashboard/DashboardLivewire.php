<?php

namespace App\Livewire\Dashboard;

use App\Exports\MothersSampleExport;
use App\Imports\MothersImport;
use App\Models\MessageHistory;
use App\Models\Organization;
use App\Models\Tip;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Jantinnerezo\LivewireAlert\LivewireAlert;
use Livewire\Component;
use Livewire\WithFileUploads;
use Maatwebsite\Excel\Facades\Excel;
use PhpOffice\PhpSpreadsheet\Shared\Date;

class DashboardLivewire extends Component
{

    use WithFileUploads;
    use LivewireAlert;
    public $modal = false;
    public $modal2 = false;
    public $file;
    public $previewData = [];
    public $previewTitleData = [];

    public $search = '';
    public $liststatus = false;
    public $user;
    public $organization;
    public $organizations =  [];

    public function save(){

        $this->user->organization_id = $this->organization->id;
        $this->user->save();
        $this->alert('success', 'Organization successfully selected');
        $this->cancel();
    }

    public function remove_organization($id){
        $user = User::findOrFail($id);
        $user->organization_id = null;
        $user->save();
        $this->alert('success', 'Organization successfully removed');
    }

    public function select_org($id){
        $this->organization = Organization::findOrFail($id);
        $this->search = "";
        $this->organizations = [];
        $this->liststatus = false;

    }

    public function remove_org(){
            $this->organization = null;
    }

    public function updatedSearch()
    {
        if (strlen($this->search) > 1) {
            $this->liststatus = false;
            $this->organizations = Organization::where('name', 'like', '%' . $this->search . '%')->limit(10)->get();
        } else {
            $this->organizations = [];
        }
    }


    #[Computed]
    function convertDate($value)
    {
        if (is_numeric($value)) {
            return Date::excelToDateTimeObject($value)->format('Y-m-d');
        }
        return $value;
    }


    public function add_organization($id){


        $this->user = User::findOrFail($id);
        $this->modal2 = true;



    }



    public function addMothers()
    {
        $this->modal = true;
    }
    /**
     * Validate and load a preview of the Excel file data.
     *
     * Reads the file and sets $previewData to rows starting from row 5.
     *
     * @return void
     */
    public function preview()
    {
        $this->validate([
            'file' => 'required|file|mimes:xlsx,xls',
        ]);

        $this->modal = true;

        // Read the entire Excel file as an array
        $data = Excel::toArray([], $this->file);

        if (count($data) > 0) {
            // Get data from the first sheet
            $sheetData = $data[0];

            // Get the first row as header
            $this->previewTitleData = array_slice($sheetData, 0, 1);

            // Remove the first 4 rows (indexes 0-3) so that we start at row 5.
            $this->previewData = array_slice($sheetData, 4);
        } else {
            $this->previewData = [];
            $this->previewTitleData = [];
        }
    }

    /**
     * Confirm the import and store the users in the database.
     *
     * @return void
     */
    public function confirmImport()
    {
        Excel::import(new MothersImport, $this->file);
        $this->alert('success', 'Users imported successfully.');

        // Reset file and preview data
        $this->previewData = [];
        $this->file = null;
    }

    public function accept($id)
    {
        $user = User::findOrFail($id);
        if (!empty($user->organization_id)) {
            if ($user->organization_verify == "pending" || $user->organization_verify == "declined") {
                $user->organization_verify = "accepted";
                $user->save();
                $this->alert('success', 'User Accepted');
            } else {
                $user->organization_verify = "declined";
                $user->save();
                $this->alert('warning', 'User Declined');
            }
        } else {
            $this->alert('warning', 'User Has No Organization');
        }
    }

    public function cancel()
    {
        $this->reset(['modal', 'file', 'modal2',  'previewData']);
    }

    public function export()
    {
        return Excel::download(new MothersSampleExport, 'mothers.xlsx');
    }
    public function render()
    {

        $requests = User::whereIn('role_id', [2, 3, 5])->orderby('organization_verify', 'desc')->get();



        if (Auth::user()->role->name === 'mother') {
            $mothers->where('role_id', 4)->where('id', Auth::user()->id)->get();
        } else {
            $mothers = User::where('role_id', 4)->get();
        }
        $users = User::all();
        $messages = MessageHistory::all();
        $tips = Tip::all();
        return view('livewire.dashboard.dashboard-livewire')
            ->with('mothers', $mothers)
            ->with('users', $users)
            ->with('messages', $messages)
            ->with('tips', $tips)
            ->with('requests', $requests);
    }
}
