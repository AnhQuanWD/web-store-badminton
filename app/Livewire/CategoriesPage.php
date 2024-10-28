<?php

namespace App\Livewire;

use App\Models\Category;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Title('Category Page')]

class CategoriesPage extends Component
{
    public function render()
    {
        $categories = Category::where('is_active', 1)->get();
        $breadcrumbs = [
            ['name' => 'Home', 'url' => url('/')],
            ['name' => 'Categories', 'url' => url('/categories')],
        ];
        return view('livewire.categories-page', [
            'categories' => $categories,
            'breadcrumbs' => $breadcrumbs,
        ]);
    }
}
