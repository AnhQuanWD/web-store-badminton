<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class VNPayController extends Controller
{
    public function vnpayReturn(Request $request)
    {
        $vnp_HashSecret = env('VNPAY_HASH_SECRET'); // Chuỗi bí mật từ file .env

        // Lấy các tham số từ URL callback của VNPAY
        $vnp_SecureHash = $request->get('vnp_SecureHash');
        $inputData = $request->only(array_filter($request->all(), function ($key) {
            return substr($key, 0, 4) == "vnp_";
        }, ARRAY_FILTER_USE_KEY));

        unset($inputData['vnp_SecureHash']);
        ksort($inputData);

        // Tạo chuỗi hashData để kiểm tra chữ ký
        $hashData = urldecode(http_build_query($inputData, '', '&'));

        // Tạo chữ ký dựa trên hashData và HashSecret
        $secureHash = hash_hmac('sha512', $hashData, $vnp_HashSecret);

        // Kiểm tra tính hợp lệ của chữ ký
        if ($secureHash === $vnp_SecureHash) {
            if ($request->get('vnp_ResponseCode') === '00') {
                // Giao dịch thành công
                return response()->json(['message' => 'Giao dịch thành công']);
            } else {
                // Giao dịch không thành công
                return response()->json(['message' => 'Giao dịch không thành công'], 400);
            }
        } else {
            // Chữ ký không hợp lệ
            return response()->json(['message' => 'Chữ ký không hợp lệ'], 400);
        }
    }
}
