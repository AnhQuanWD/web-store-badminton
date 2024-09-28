<?php

namespace App\Livewire;

use App\Helpers\CartManagement;
use App\Livewire\Partials\Navbar;
use App\Models\Product;
use Livewire\Attributes\Title;
use Livewire\Component;
use Jantinnerezo\LivewireAlert\LivewireAlert;


#[Title('Products Detail Page')]
class ProductDetailPage extends Component
{
    use LivewireAlert;

    public $slug;
    public $quantity = 1;
    public function mount($slug){
        $this->slug = $slug;
    }

    public function increaseQty(){
        $this->quantity++;
    }

    public function decreaseQty(){
        if($this->quantity > 1){
            $this->quantity--;
        }
    }

    // Add product to cart
    public function addToCart($product_id)
    {
        $total_count = CartManagement::addItemsToCartWithQty($product_id, $this->quantity);

        $this->dispatch('update-cart-count', total_count: $total_count)->to(Navbar::class);

        $this->alert('success', 'Add product successfull!', [
            'position' => 'top-end',
            'timer' => 3000,
            'toast' => true,
        ]);
    }

    public function render()
    {
        $product = Product::where('slug', $this->slug)->firstOrFail();
        $breadcrumbs = [
            ['name' => 'Home', 'url' => url('/')],
            ['name' => 'Products', 'url' => url('/products')],
            ['name' => $product->name, 'url' => url("/products/{$product->slug}")]
        ];

        $relatedProducts = Product::where('category_id', $product->category_id)
        ->where('id', '!=', $product->id) // Exclude the current product
        ->limit(4) // Adjust the limit as needed
        ->get();

        return view('livewire.product-detail-page', [
            'product' => Product::where('slug', $this->slug)->firstOrFail(),
            'breadcrumbs' => $breadcrumbs,
            'relatedProducts' => $relatedProducts,
        ]);
    }
}
