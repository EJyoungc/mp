<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Events\AfterSheet;

class MothersSampleExport implements FromArray, WithEvents, WithHeadings
{
    /**
     * Define the Excel header row.
     */
    public function headings(): array
    {
        return [
            'Name',
            'Email',
            'Date of Birth',
            'Last Menstrual_Cycle',
            'Phone',
            'Marital Status',
            'Religion',
            'Level of Education',
            'Occupation',
            'Address',
            'District',
            'Area',
            'Traditional Authority',
            'Next of Kin',
            'Next of Kin Mobile',
            'Height',
            'Leg or Spine',
            'Deformity',
            'Deliveries',
            'Abortions',
            'Still Births',
            'C-Section',
            'Vacuum',
            'Multiple Deliveries',
            'Tuberculosis',
            'Asthma',
            'Menstrual Cycle',
        ];
    }

    /**
     * Return the array of sample data.
     */
    public function array(): array
    {
        return [
            // Row 1: Sample Data
            [
                'Jane Doe', 'YhV3P@example.com', '1990-05-15', '1990-05-15', '+265991234567', 'Married', 'Christianity', "Bachelor's Degree",
                'Software Engineer', 'Lilongwe, Malawi', 'Lilongwe', 'Area 1', 'TA Mwambo', 'John Doe', '+265998765432', '168',
                'No', 'No', '2', '1', 'Yes', 'No', 'No', 'No', 'No', 'No', 'Regular',
            ],
            // Row 2: Comments (Expected Value Descriptions)
            [
                'Full Name',
                'Email Address',
                'YYYY-MM-DD',
                'YYYY-MM-DD',
                'Include country code (e.g., +265...)',
                'e.g., Single, Married, Divorced, Widowed',
                'e.g., Christianity, Islam, Hinduism, etc.',
                'e.g., Primary, Secondary, Diploma, Bachelor, etc.',
                'Job Title',
                'City, District, etc.',
                'e.g., Lilongwe, Blantyre, etc.',
                'e.g., Area 1, Limbe, etc.',
                'Traditional Authority name',
                'Relative\'s full name',
                'Relative\'s phone number',
                'Numeric value (cm)',
                'Yes/No',
                'Yes/No',
                '0-10 deliveries',
                '0-3 abortions',
                'Yes/No',
                'Yes/No',
                'Yes/No',
                'Yes/No',
                'Yes/No',
                'Yes/No',
                'regular/abnormal',
            ],
        ];
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function (AfterSheet $event) {
                // Auto-size columns from A to Z (or adjust range as needed)
                foreach (range('A', 'Z') as $columnID) {
                    $event->sheet->getDelegate()->getColumnDimension($columnID)->setAutoSize(true);
                }
                // Also AA column if needed
                $event->sheet->getDelegate()->getColumnDimension('AA')->setAutoSize(true);
            },
        ];
    }
}
