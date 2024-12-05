<?php

namespace App\Livewire\Nav;

use App\Models\Trimester;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class SideLivewire extends Component
{
    public function render()
    {


        $trimesters = Trimester::all();
        $name = Auth::user();
        return view('livewire.nav.side-livewire')
        ->with('name',$name)
        ->with('trimesters', $trimesters);
    }
}
