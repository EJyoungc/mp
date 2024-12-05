<?php

namespace App\Livewire\Users;

use App\Helper\StandardData;
use App\Mail\Reset;
use App\Models\Role;
use App\Models\User;
use Exception;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Jantinnerezo\LivewireAlert\LivewireAlert;
use Livewire\Component;

class UsersLivewire extends Component
{
    use LivewireAlert;
    public $modal = false;
    public $user;


    public function toggleActive($id)
    {

        $user = User::findOrFail($id);
        if ($user->is_active == 1) {
            $user->is_active = 0;
            $user->save();
            $this->alert('success', 'User status updated successfully');
        } else {
            $user->is_active = 1;
            $user->save();
            $this->alert('success', 'User status updated successfully');
        }
    }


    public function resetPassword($id){


        $user = User::findOrFail($id);
        $password = StandardData::generatePassword();
        

        try{
            // $user = "eliajh";
            DB::table('users')->where('id', $id)->update(['password' => bcrypt($password)]);
            Mail::to($user->email)->send(new Reset($user, $password));   
            $this->alert('success','Password reset successfully');
        }catch(Exception $e){
            $this->alert('error', $e->getMessage());
        }

    }

   
    
   
    


    public function create()
    {
        $this->modal = true;
    }

    public function cancel()
    {
        $this->reset([
            'modal',
            'user'
        ]);
    }


    public function render()
    {
        
        // dd($users2);

        $users = User::whereNot('role_id', 1)->get();
        $roles = Role::whereNot('name', 'system-admin')->get();
        $doctors = User::where('role_id', 3)->get();
        $mothers = User::where('role_id', 4)->get();
        $practioners = User::where('role_id', 5)->get(); 
        return view('livewire.users.users-livewire')
        ->with('users', $users)
        ->with('roles', $roles)
        ->with('doctors', $doctors)
        ->with('mothers', $mothers)
        ->with('practioners', $practioners);
    }
}
