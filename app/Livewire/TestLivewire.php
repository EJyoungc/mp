<?php

namespace App\Livewire;

use App\Models\User;
use Livewire\Component;


class TestLivewire extends Component
{

    public $data = 0;
    public $users = [];

    public $modal = false;

    public function open(){
        $this->modal = true;
    }

    public function cancel(){
        $this->reset(['modal']);
    }


    public function getusers(){
        $this->users = User::all();
    }

    public function add(){
        $this->data++;
    }

    public function subtract(){
        $this->data--;
    }


    public function render()
    {


        return view('livewire.test-livewire');
    }
}
