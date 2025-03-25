<?php

namespace App\Imports;

use App\Helper\StandardData;
use App\Models\History;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithStartRow;
use Illuminate\Support\Str;
use PhpOffice\PhpSpreadsheet\Shared\Date;

class MothersImport implements ToModel, WithHeadingRow, WithStartRow
{
    /**
     * @param array $row
     *
     * @return \Illuminate\Database\Eloquent\Model|null
     */

    protected  function convertExcelDate($value)
     {
         if (is_numeric($value)) {
             return Date::excelToDateTimeObject($value)->format('Y-m-d');
         }
         return $value;
     }



    public function model(array $row)
    {


        


        $user = User::create([
            'role_id'              => $row['role_id'] ?? 4, // Use a default if not provided
            'name'                 => $row['name'],
            'email'                => empty($row['email']) ? $this->generateUniqueEmail($row['name']) : $row['email'],
            'password'             => Hash::make(StandardData::generatePassword()),
            'date_of_birth'        => $this->convertExcelDate($row['date_of_birth']),
            'marital_status'       => $row['marital_status'],
            'religion'             => $row['religion'],
            'level_of_education'   => $row['level_of_education'],
            'occupation'           => $row['occupation'],
            'phone'                => $row['phone'],
            'address'              => $row['address'],
            'traditional_authority' => $row['traditional_authority'],
            'next_of_kin'          => $row['next_of_kin'],
            'next_of_kin_mobile'   => $row['next_of_kin_mobile'],
            'height'               => $row['height'],
            'leg_or_spine'         => $row['leg_or_spine'],
            'deformity'            => $row['deformity'],
            'deliveries'           => $row['deliveries'],
            'abortions'            => $row['abortions'],
            'still_births'         => $row['still_births'],
            'c_section'            => $row['c_section'],
            'vacuum'               => $row['vacuum'],
            'multiple'             => $row['multiple_deliveries'],
            'tuberculosis'         => $row['tuberculosis'],
            'asthma'               => $row['asthma'],
            'menstrual_cycle'      => $row['menstrual_cycle'],
        ]);

        // Create the session record associated with the newly created user.
        History::create([
            'mother_id'              => $user->id,
            'last_menstrual_cycle' => $this->convertExcelDate($row['last_menstrual_cycle']),
        ]);

        return $user;
    }

    /**
     * Generate a unique email based on the user's name.
     *
     * @param string $name
     * @return string
     */
    function generateUniqueEmail($name, $domain = 'mother.com')
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
     *
     * @return int
     */
    public function startRow(): int
    {
        return 5;
    }
}
