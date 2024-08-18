<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * @OA\Schema(
 *     schema="Order",
 *     type="object",
 *     title="Order",
 *     required={"user_id", "grand_total", "payment_method", "currency", "shipping_amount", "shipping_method"},
 *     properties={
 *         @OA\Property(property="id", type="integer", description="Order ID"),
 *         @OA\Property(property="user_id", type="integer", description="User ID"),
 *         @OA\Property(property="grand_total", type="number", format="float", description="Order total amount"),
 *         @OA\Property(property="payment_method", type="string", description="Payment method"),
 *         @OA\Property(property="payment_status", type="string", description="Payment status"),
 *         @OA\Property(property="status", type="string", description="Order status"),
 *         @OA\Property(property="currency", type="string", description="Currency"),
 *         @OA\Property(property="shipping_amount", type="number", format="float", description="Shipping amount"),
 *         @OA\Property(property="shipping_method", type="string", description="Shipping method"),
 *         @OA\Property(property="notes", type="string", description="Additional notes"),
 *         @OA\Property(property="created_at", type="string", format="date-time", description="Creation timestamp"),
 *         @OA\Property(property="updated_at", type="string", format="date-time", description="Update timestamp")
 *     }
 * )
 */

class Order extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'grand_total',
        'payment_method',
        'payment_status',
        'status',
        'currency',
        'shippimg_amount',
        'shippimg_method',
        'notes'
    ];

    public function user(){
       return $this->belongsTo(User::class);
    }

    public function items(){
       return $this->hasMany(OrderItem::class);
    }

    public function address(){
       return $this->hasOne(Address::class);
    }
}
