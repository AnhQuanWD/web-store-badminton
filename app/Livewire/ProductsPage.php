<?php

namespace App\Livewire;

use App\Models\Brand;
use App\Models\Category;
use App\Models\Product;
use Livewire\Attributes\Title;
use Livewire\Attributes\Url;
use Livewire\Component;
use Livewire\WithPagination;

#[Title('Products Page')]
class ProductsPage extends Component
{
    use WithPagination;

    #[Url]
    public $selected_categories = []; 

    #[Url]
    public $selected_brands = [];

    #[Url]
    public $featured;

    #[Url]
    public $on_sale;

    #[Url]
    public $price_range = 500;

    #[Url]
    public $sort = 'latest';

    public function render()
    {
        $productsQuery = Product::query()->where('is_active', 1,);

        if(!empty($this->selected_categories)){
            $productsQuery->whereIn('category_id', $this->selected_categories);
        }

        if(!empty($this->selected_brands)){
            $productsQuery->whereIn('brand_id', $this->selected_brands);
        }

        if($this->featured){
            $productsQuery->where('is_featured', 1);
        }

        if($this->on_sale){
            $productsQuery->where('on_sale', 1);
        }

        if($this->price_range){
            $productsQuery->whereBetween('price', [0, $this->price_range]);
        }

        if($this->sort == 'latest'){
            $productsQuery->latest();
        }

        if($this->sort == 'price'){
            $productsQuery->orderBy('price');
        }

        return view('livewire.products-page', [
            'products' => $productsQuery->paginate(6),
            'brands' => Brand::where('is_active', 1,)->get(['id', 'name', 'slug']),
            'categories' => Category::where('is_active', 1)->get(['id', 'name', 'slug']),
        ]);
    }
}
