<?php

namespace App\Imports;

use App\Helper\StandardData;
use App\Models\Area;
use App\Models\District;
use App\Models\History;
use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithStartRow;
use PhpOffice\PhpSpreadsheet\Shared\Date;

class MothersImport implements ToModel, WithHeadingRow, WithStartRow
{
    /**
     * @param  array  $row
     * @return Model|null
     */
    protected function convertExcelDate($value)
    {
        if (is_numeric($value)) {
            return Date::excelToDateTimeObject($value)->format('Y-m-d');
        }

        return $value;
    }

    public function model(array $row)
    {
        $districtId = null;
        $areaId = null;

        if (! empty($row['district'])) {
            $district = District::where('name', 'like', trim($row['district']))->first();
            if ($district) {
                $districtId = $district->id;
                if (! empty($row['area'])) {
                    $area = Area::where('district_id', $districtId)
                        ->where('name', 'like', trim($row['area']))
                        ->first();
                    if ($area) {
                        $areaId = $area->id;
                    }
                }
            }
        }

        $user = User::create([
            'role_id' => $row['role_id'] ?? 4, // Use a default if not provided
            'name' => $row['name'],
            'email' => empty($row['email']) ? $this->generateUniqueEmail($row['name']) : $row['email'],
            'password' => Hash::make(StandardData::generatePassword()),
            'date_of_birth' => $this->convertExcelDate($row['date_of_birth']),
            'marital_status' => $row['marital_status'],
            'religion' => $row['religion'],
            'level_of_education' => $row['level_of_education'],
            'occupation' => $row['occupation'],
            'phone' => $row['phone'],
            'address' => $row['address'],
            'district_id' => $districtId,
            'area_id' => $areaId,
            'traditional_authority' => $row['traditional_authority'],
            'next_of_kin' => $row['next_of_kin'],
            'next_of_kin_mobile' => $row['next_of_kin_mobile'],
            'height' => $row['height'],
            'leg_or_spine' => $row['leg_or_spine'],
            'deformity' => $row['deformity'],
            'deliveries' => $row['deliveries'],
            'abortions' => $row['abortions'],
            'still_births' => $row['still_births'],
            'c_section' => $row['c_section'],
            'vacum' => $row['vacuum'],
            'multiple' => $row['multiple_deliveries'],
            'tuberculosis' => $row['tuberculosis'],
            'asthma' => $row['asthma'],
            'menstrual_cycle' => $row['menstrual_cycle'],
            'organization_id' => auth()->user()->organization_id,
            'organization_verify' => 'verified',
        ]);

        // Create the session record associated with the newly created user.
        History::create([
            'mother_id' => $user->id,
            'last_menstrual_cycle' => $this->convertExcelDate($row['last_menstrual_cycle']),
            'organization_id' => auth()->user()->organization_id,
        ]);

        return $user;
    }

    /**
     * Generate a unique email based on the user's name.
     *
     * @param  string  $name
     * @return string
     */
    public function generateUniqueEmail($name, $domain = 'mother.com')
    {
        // Format the name to be used as part of the email address (e.g., "John Doe" becomes "john.doe")
        $username = Str::slug($name, '.');
        // Generate a unique string (you could use timestamp or a random string)
        $uniquePart = Str::random(6); // generates a random 6-character string
        // Combine to form the email address
        $email = "{$username}.{$uniquePart}@{$domain}";

        return $email;
    }

    /**
     * Specify the starting row for the import.
     */
    public function startRow(): int
    {
        return 5;
    }
}
