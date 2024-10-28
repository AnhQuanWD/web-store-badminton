<?php

namespace App\Livewire;

use App\Models\Order;
use Livewire\Attributes\Title;
use Livewire\Attributes\Url;
use Livewire\Component;
use Stripe\Checkout\Session;
use Stripe\Stripe;

#[Title('Success')]
class SuccessPage extends Component
{
    #[Url]
    public $session_id;

    public function render()
    {
        // $latest_order = Order::with('address')->where('user_id', auth()->user()->id)->latest()->first();
        $latest_order = Order::with('orderItems.product')->where('user_id', auth()->user()->id)->latest()->first();

        if($this->session_id){
            Stripe::setApiKey(env('STRIPE_SECRET'));
            $session_info = Session::retrieve($this->session_id);

            if($session_info->payment_status != 'paid'){
                $latest_order->payment_status = 'failed';
                $latest_order->save();
                return redirect()->route('cancel');
            } else if($session_info->payment_status  == 'paid'){
                $latest_order->payment_status = 'paid';
                $latest_order->save();
            }
        }

        $request = request(); // Lấy request để xử lý từ VNPay
        $orderId = $request->input('vnp_TxnRef'); // Mã đơn hàng
        $responseCode = $request->input('vnp_ResponseCode'); // Mã phản hồi từ VNPay
        $paymentDate = $request->input('vnp_PayDate'); // Ngày thanh toán

        if ($orderId) {
            // Tìm đơn hàng dựa trên mã đơn hàng
            $latest_order = Order::where('id', $orderId)->first();

            if ($latest_order) {
                // Kiểm tra mã phản hồi từ VNPay
                if ($responseCode == '00') { // Mã 00 có nghĩa là thanh toán thành công
                    $latest_order->payment_status = 'paid';
                    // $latest_order->payment_date = $paymentDate; // Lưu ngày thanh toán nếu cần
                } else {
                    $latest_order->payment_status = 'failed'; // Nếu không thành công
                }

                $latest_order->save(); // Lưu thay đổi vào cơ sở dữ liệu
            }
        }

        $products = $latest_order->orderItems->map(function ($item) {
            return [
                'name' => $item->product->name,
                'quantity' => $item->quantity,
                'price' => $item->unit_amount,
                'total_amount' => $item->total_amount,
            ];
        });

        return view('livewire.success-page', [
            'order' => $latest_order,
            'products' => $products,
        ]);
    }
}
