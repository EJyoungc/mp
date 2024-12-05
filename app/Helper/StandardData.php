<?php

namespace App\Helper;

use Hashids\Hashids;

class StandardData
{
    /**
     * Get the list of all districts in Malawi.
     *
     * @return array
     */

     public static function generatePassword($length = 8) {
        // Characters to include in the password
        $characters = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789!@#$%^&*()';
        
        // Get the total number of characters available
        $charactersLength = strlen($characters);
        
        // Initialize the password variable
        $randomPassword = '';
        
        // Loop to generate a password of the specified length
        for ($i = 0; $i < $length; $i++) {
            // Randomly select a character and append it to the password
            $randomPassword .= $characters[rand(0, $charactersLength - 1)];
        }
        
        return $randomPassword;
    }

    public static function encrypt($value){
            $hash = new Hashids();
        return $hash->encode($value);
    }

    public static function decrypt($value){
        $hash = new Hashids();
    return $hash->decode($value)[0];
}






    public static function getDistricts()
    {
        return [
            'Blantyre', 'Lilongwe', 'Mzuzu', 'Zomba', 'Mangochi', 
            'Dedza', 'Nkhotakota', 'Mchinji', 'Ntcheu', 'Salima', 
            'Mulanje', 'Chiradzulu', 'Chikwawa', 'Nsanje', 
            'Karonga', 'Rumphi', 'Chitipa', 'Kasungu', 'Machinga', 
            'Balaka', 'Thyolo', 'Phalombe', 'Neno', 'Dowa', 
            'Ntchisi', 'Likoma'
        ];
    }

    /**
     * Get the list of all religions.
     *
     * @return array
     */
    public static function getReligions()
    {
        return [
            'Christianity', 'Islam', 'Hinduism', 'Judaism', 
            'Buddhism', 'Traditional African Religion', 'Atheism', 
            'Other'
        ];
    }


    public static function getMaritalStatuses()
    {
        return [
            'Single', 'Married', 'Divorced', 'Widowed', 'Separated'
        ];
    }

  
    

    /**
     * Get the list of levels of education.
     *
     * @return array
     */
    public static function getEducationLevels()
    {
        return [
            'Primary', 'Secondary', 'Certificate', 'Diploma', 
            'Bachelor’s Degree', 'Master’s Degree', 'PhD'
        ];
    }
}
