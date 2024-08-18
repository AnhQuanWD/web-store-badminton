<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Order;
use Illuminate\Http\Request;

/**
 * @OA\Get(
 *     path="/api/orders/{userId}",
 *     operationId="getUserOrders",
 *     tags={"Orders"},
 *     summary="Get all orders for a specific user",
 *     description="Returns all orders and their items for the specified user",
 *     @OA\Parameter(
 *         name="userId",
 *         in="path",
 *         required=true,
 *         @OA\Schema(
 *             type="integer"
 *         )
 *     ),
 *     @OA\Response(
 *         response=200,
 *         description="Successful operation",
 *         @OA\JsonContent(
 *             type="array",
 *             @OA\Items(
 *                 type="object",
 *                 ref="#/components/schemas/Order"
 *             )
 *         )
 *     ),
 *     @OA\Response(response=404, description="User not found"),
 *     @OA\Response(response=500, description="Internal Server Error")
 * )
 */
class OrderItemController extends Controller
{
    /**
     * Get all orders for a specific user.
     *
     * @param int $userId
     * @return \Illuminate\Http\JsonResponse
     */
    public function index($userId)
    {
        // Fetch orders for the user with the given user ID
        $orders = Order::with('items')
            ->where('user_id', $userId)
            ->latest()
            ->get();

        // Return orders and their items
        return response()->json($orders);
    }
}
