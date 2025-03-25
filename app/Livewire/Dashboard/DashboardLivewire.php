<?php

namespace App\Livewire\Dashboard;

use App\Exports\MothersSampleExport;
use App\Imports\MothersImport;
use App\Models\MessageHistory;
use App\Models\Tip;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
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
    public $file;
    public $previewData = [];
    public $previewTitleData = [];

    #[Computed]
    function convertDate($value)
    {
        if (is_numeric($value)) {
            return Date::excelToDateTimeObject($value)->format('Y-m-d');
        }
        return $value;
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

    public function cancel()
    {
        $this->reset(['modal', 'file', 'previewData']);
    }

    public function export()
    {
        return Excel::download(new MothersSampleExport, 'mothers.xlsx');
    }
    public function render()
    {


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
            ->with('tips', $tips);
    }
}
