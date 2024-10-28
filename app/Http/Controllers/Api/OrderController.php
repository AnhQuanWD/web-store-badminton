<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\OrderItem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;

class OrderController extends Controller
{
/**
 * @OA\Post(
 *     path="/api/orders",
 *     summary="Create a new order",
 *     tags={"Orders"},
 *     @OA\RequestBody(
 *         required=true,
 *         @OA\JsonContent(
 *             required={
 *                 "user_id", "first_name", "last_name", "phone", "street_address", 
 *                 "city", "state", "zip_code", "payment_method", "currency", 
 *                 "shipping_amount", "items"
 *             },
 *             @OA\Property(property="user_id", type="integer", example=1),
 *             @OA\Property(property="first_name", type="string", example="John"),
 *             @OA\Property(property="last_name", type="string", example="Doe"),
 *             @OA\Property(property="phone", type="string", example="+123456789"),
 *             @OA\Property(property="street_address", type="string", example="123 Main St"),
 *             @OA\Property(property="city", type="string", example="New York"),
 *             @OA\Property(property="state", type="string", example="NY"),
 *             @OA\Property(property="zip_code", type="string", example="10001"),
 *             @OA\Property(property="payment_method", type="string", example="credit_card"),
 *             @OA\Property(property="payment_status", type="string", example="pending"),
 *             @OA\Property(property="currency", type="string", example="USD"),
 *             @OA\Property(property="shipping_amount", type="number", example=10.50),
 *             @OA\Property(property="shipping_method", type="string", example="UPS"),
 *             @OA\Property(property="notes", type="string", example="Please deliver between 9AM-5PM"),
 *             @OA\Property(
 *                 property="items",
 *                 type="array",
 *                 @OA\Items(
 *                     @OA\Property(property="product_id", type="integer", example=1),
 *                     @OA\Property(property="quantity", type="integer", example=2),
 *                     @OA\Property(property="price", type="number", example=15.50)
 *                 )
 *             )
 *         )
 *     ),
 *     @OA\Response(response=201, description="Order created"),
 *     @OA\Response(response=400, description="Validation error")
 * )
 */

    public function store(Request $request)
    {
        // $this->validate($request, [
        //     'first_name' => 'required|string',
        //     'last_name' => 'required|string',
        //     'phone' => 'required|string',
        //     'street_address' => 'required|string',
        //     'city' => 'required|string',
        //     'state' => 'required|string',
        //     'zip_code' => 'required|string',
        //     'payment_method' => 'required|string',
        // ]);

        $validator = Validator::make($request->all(), [
            'user_id' => 'required|integer|exists:users,id',
            'items' => 'required|array',
            'items.*.product_id' => 'required|integer|exists:products,id',
            'items.*.quantity' => 'required|integer|min:1',
            'items.*.price' => 'required|numeric|min:0',
            'payment_method' => 'required|string',
            'currency' => 'required|string',
            'shipping_amount' => 'required|numeric|min:0',
            'shipping_method' => 'string',
            'first_name' => 'required|string',
            'last_name' => 'required|string',
            'phone' => 'required|string',
            'street_address' => 'required|string',
            'city' => 'required|string',
            'state' => 'required|string',
            'zip_code' => 'required|string',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 400);
        }

        DB::beginTransaction();
        try {
            $order = Order::create([
                'user_id' => $request->user_id,
                'grand_total' => array_reduce($request->items, function($carry, $item) {
                    return $carry + ($item['price'] * $item['quantity']);
                }, 0) + $request->shipping_amount,
                'payment_method' => $request->payment_method,
                'payment_status' => $request->payment_status ?? 'pending',
                'status' => 'new',
                'currency' => $request->currency,
                'shipping_amount' => $request->shipping_amount,
                'shipping_method' => $request->shipping_method,
                'notes' => $request->notes
            ]);

            foreach ($request->items as $item) {
                OrderItem::create([
                    'order_id' => $order->id,
                    'product_id' => $item['product_id'],
                    'quantity' => $item['quantity'],
                    'unit_amount' => $item['price'],
                    'total_amount' => $item['quantity'] * $item['price'],
                ]);
            }

            DB::commit();
            return response()->json(['order' => $order], 201);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }
}
