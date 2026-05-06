<?php

namespace App\Livewire\Dashboard;

use App\Exports\MothersSampleExport;
use App\Imports\MothersImport;
use App\Models\Organization;
use App\Models\User;
use Jantinnerezo\LivewireAlert\LivewireAlert;
use Livewire\Component;
use Livewire\WithFileUploads;
use Maatwebsite\Excel\Facades\Excel;
use PhpOffice\PhpSpreadsheet\Shared\Date;

class MothersManager extends Component
{
    use LivewireAlert;
    use WithFileUploads;

    public $modal = false;

    public $file;

    public $organization_id;

    public $previewData = [];

    public $previewTitleData = [];

    public function addMothers()
    {
        $this->modal = true;
    }

    public function preview()
    {
        $rules = ['file' => 'required|file|mimes:xlsx,xls'];
        if (auth()->user()->isSystemAdmin()) {
            $rules['organization_id'] = 'required|exists:organizations,id';
        }
        $this->validate($rules);
        $this->modal = true;
        $data = Excel::toArray([], $this->file);
        if (count($data) > 0) {
            $sheetData = $data[0];
            $this->previewTitleData = array_slice($sheetData, 0, 1);
            $this->previewData = array_slice($sheetData, 3);
        }
    }

    public function confirmImport()
    {
        Excel::import(new MothersImport($this->organization_id), $this->file);
        $this->alert('success', 'Users imported successfully.');
        $this->previewData = [];
        $this->file = null;
        $this->organization_id = null;
        $this->modal = false;
    }

    public function export()
    {
        return Excel::download(new MothersSampleExport, 'mothers.xlsx');
    }

    public function convertDate($value)
    {
        if (is_numeric($value)) {
            return Date::excelToDateTimeObject($value)->format('Y-m-d');
        }

        return $value;
    }

    public function cancel()
    {
        $this->reset(['modal', 'file', 'previewData', 'organization_id']);
    }

    public function render()
    {
        $user = auth()->user();
        $mothersQuery = User::where('role_id', 4);

        $organizations = [];
        if ($user->isSystemAdmin()) {
            $organizations = Organization::all();
        } else {
            $mothersQuery->where('organization_id', $user->organization_id);
        }

        return view('livewire.dashboard.mothers-manager', [
            'mothers' => $mothersQuery->get(),
            'organizations' => $organizations,
        ]);
    }
}
