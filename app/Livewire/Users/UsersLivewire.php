<?php

namespace App\Livewire\Users;

use App\Helper\StandardData;
use App\Mail\Reset;
use App\Models\Role;
use App\Models\User;
use Exception;
use Illuminate\Support\Facades\Auth;
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

        // Start building the query
        $users = User::query();

        // Example condition: Check if the logged-in user is an admin
        if (Auth::user()->role->name === 'admin') {
            $users->where('role_id', '!=', 1); // Exclude system-admin users
        }

        // Example condition: Check if the logged-in user is a doctor
        if (Auth::user()->role->name === 'doctor') {
            $users->whereNot('role_id', 1)->whereNot('role_id', 2); // Only include doctors
        }

        if (Auth::user()->role->name === 'practitioner') {
            $users->whereNot('role_id', 1)->whereNot('role_id', 2)->whereNot('role_id', 3)->whereNot('role_id', 5)->whereNot('role_id',5); // Only include doctors
        }

        


        $users = $users->get();
        $roles = Role::query();

        // Example condition: Check if the logged-in user is an admin
        if (Auth::user()->role->name === 'admin') {
            $roles->whereNot('name', 'system-admin'); // Exclude system-admin role
        }

        // Example condition: Check if the logged-in user is a doctor
        if (Auth::user()->role->name === 'doctor') {
            $roles->whereNotIn('name', ['system-admin', 'admin']); // Exclude system-admin and admin roles
        }

        if (Auth::user()->role->name === 'practitioner') {
            $roles->whereNotIn('name', ['system-admin', 'admin','doctor','practitioner']); // Exclude system-admin and admin roles
        }

        $roles = $roles->get();
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
