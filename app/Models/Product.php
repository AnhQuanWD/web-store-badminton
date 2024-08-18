<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * @OA\Schema(
 *     schema="Product",
 *     type="object",
 *     title="Product",
 *     description="Product model",
 *     required={"name", "price", "category_id", "brand_id"},
 *     @OA\Property(
 *          property="id",
 *          description="ID of the product",
 *          type="integer",
 *          example=1
 *     ),
 *     @OA\Property(
 *          property="category_id",
 *          description="Category ID",
 *          type="integer",
 *          example=1
 *     ),
 *     @OA\Property(
 *          property="brand_id",
 *          description="Brand ID",
 *          type="integer",
 *          example=1
 *     ),
 *     @OA\Property(
 *          property="name",
 *          description="Product name",
 *          type="string",
 *          example="Product name"
 *     ),
 *     @OA\Property(
 *          property="slug",
 *          description="Product slug",
 *          type="string",
 *          example="product-name"
 *     ),
 *     @OA\Property(
 *          property="images",
 *          description="Product images",
 *          type="array",
 *          @OA\Items(type="string"),
 *          example={"image1.jpg", "image2.jpg"}
 *     ),
 *     @OA\Property(
 *          property="description",
 *          description="Product description",
 *          type="string",
 *          example="Product description"
 *     ),
 *     @OA\Property(
 *          property="price",
 *          description="Product price",
 *          type="number",
 *          format="float",
 *          example=100.0
 *     ),
 *     @OA\Property(
 *          property="is_active",
 *          description="Is product active",
 *          type="boolean",
 *          example=true
 *     ),
 *     @OA\Property(
 *          property="is_featured",
 *          description="Is product featured",
 *          type="boolean",
 *          example=false
 *     ),
 *     @OA\Property(
 *          property="in_stock",
 *          description="Is product in stock",
 *          type="boolean",
 *          example=true
 *     ),
 *     @OA\Property(
 *          property="on_sale",
 *          description="Is product on sale",
 *          type="boolean",
 *          example=false
 *     ),
 *     @OA\Property(
 *          property="created_at",
 *          description="Created at timestamp",
 *          type="string",
 *          format="date-time",
 *          example="2023-01-01T00:00:00.000000Z"
 *     ),
 *     @OA\Property(
 *          property="updated_at",
 *          description="Updated at timestamp",
 *          type="string",
 *          format="date-time",
 *          example="2023-01-01T00:00:00.000000Z"
 *     )
 * )
 */

class Product extends Model
{
    use HasFactory;

    protected $fillable = [
      'category_id',
      'brand_id',
      'name', 
      'slug', 
      'images', 
      'description', 
      'price', 
      'is_active', 
      'is_featured', 
      'in_stock', 
      'on_sale'
    ];

    protected $casts = [
      'images' => 'array'
    ];

    public function category(){
      return $this->belongsTo(Category::class);
    }

    public function brand(){
      return $this->belongsTo(Brand::class);
    }

    public function orderItems(){
      return $this->hasMany(OrderItem::class);
    }
}
