<?php

namespace App\Livewire;

use App\Helpers\CartManagement;
use App\Livewire\Partials\Navbar;
use App\Models\Brand;
use App\Models\Category;
use App\Models\Product;
use Livewire\Attributes\Title;
use Livewire\Attributes\Url;
use Livewire\Component;
use Livewire\WithPagination;
use Jantinnerezo\LivewireAlert\LivewireAlert;

#[Title('Products Page')]
class ProductsPage extends Component
{
    use WithPagination;
    use LivewireAlert;

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

    // Add product to cart
    public function addToCart($product_id)
    {
        $total_count = CartManagement::addItemsToCart($product_id);

        $this->dispatch('update-cart-count', total_count: $total_count)->to(Navbar::class);

        $this->alert('success', 'Add product successfull!', [
            'position' => 'top-end',
            'timer' => 3000,
            'toast' => true,
        ]);
    }
    public function render()
    {
        $productsQuery = Product::query()->where('is_active', 1,);

        if (!empty($this->selected_categories)) {
            $productsQuery->whereIn('category_id', $this->selected_categories);
        }

        if (!empty($this->selected_brands)) {
            $productsQuery->whereIn('brand_id', $this->selected_brands);
        }

        if ($this->featured) {
            $productsQuery->where('is_featured', 1);
        }

        if ($this->on_sale) {
            $productsQuery->where('on_sale', 1);
        }

        if ($this->price_range) {
            $productsQuery->whereBetween('price', [0, 1000000000]);
        }

        if ($this->sort == 'latest') {
            $productsQuery->latest();
        }

        if ($this->sort == 'price') {
            $productsQuery->orderBy('price');
        }

        return view('livewire.products-page', [
            'products' => $productsQuery->paginate(8),
            'brands' => Brand::where('is_active', 1,)->get(['id', 'name', 'slug']),
            'categories' => Category::where('is_active', 1)->get(['id', 'name', 'slug']),
        ]);
    }
}
