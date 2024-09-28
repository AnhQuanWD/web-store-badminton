<?php

namespace App\Livewire;

use Livewire\Component;

class Breadcrumb extends Component
{
    public $breadcrumbs = [];

    public function mount($breadcrumbs)
    {
        $this->breadcrumbs = $breadcrumbs;
    }

    public function render()
    {
        return view('livewire.breadcrumb');
    }
}
