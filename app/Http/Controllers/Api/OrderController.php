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
     *      path="/api/orders",
     *      operationId="storeOrder",
     *      tags={"Orders"},
     *      summary="Place a new order",
     *      description="Creates a new order and returns the order data",
     *      @OA\RequestBody(
     *          required=true,
     *          @OA\JsonContent(
     *              @OA\Property(property="user_id", type="integer"),
     *              @OA\Property(property="items", type="array", @OA\Items(type="object", 
     *                  @OA\Property(property="product_id", type="integer"),
     *                  @OA\Property(property="quantity", type="integer"),
     *                  @OA\Property(property="price", type="number", format="float")
     *              )),
     *              @OA\Property(property="payment_method", type="string"),
     *              @OA\Property(property="payment_status", type="string"),
     *              @OA\Property(property="currency", type="string"),
     *              @OA\Property(property="shipping_amount", type="number", format="float"),
     *              @OA\Property(property="shipping_method", type="string"),
     *              @OA\Property(property="notes", type="string")
     *          )
     *      ),
     *      @OA\Response(
     *          response=201,
     *          description="Order placed successfully",
     *          @OA\JsonContent(ref="#/components/schemas/Order")
     *       ),
     *      @OA\Response(response=400, description="Bad request"),
     *      @OA\Response(response=500, description="Internal server error")
     * )
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'user_id' => 'required|integer|exists:users,id',
            'items' => 'required|array',
            'items.*.product_id' => 'required|integer|exists:products,id',
            'items.*.quantity' => 'required|integer|min:1',
            'items.*.price' => 'required|numeric|min:0',
            'payment_method' => 'required|string',
            'currency' => 'required|string',
            'shipping_amount' => 'required|numeric|min:0',
            'shipping_method' => 'required|string',
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
