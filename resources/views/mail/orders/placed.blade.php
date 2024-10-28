<x-mail::message>
# Order Placed Successfully!

Thank you for your order. Your order number is: {{ $order->id }}.

## Invoice Details:

**Customer:** {{ $order->address->first_name }} {{ $order->address->last_name }}  
**Order Date:** {{ $order->created_at->format('d/m/Y') }}  
**Order Number:** {{ $order->id }}

### Products:
@foreach ($products as $product)
- **{{ $product['name'] }}**  
  Quantity: {{ $product['quantity'] }}  
  Price: {{ number_format($product['total_amount'], 0, ',', '.') }} VNĐ  
@endforeach

<hr>
### Total Summary:
- **Discount:** 0 VNĐ  
- **Shipping:** 0 VNĐ  
- **Total Amount:** {{ number_format($order->grand_total, 0, ',', '.') }} VNĐ

<x-mail::button :url="$url">
    View Order
</x-mail::button>

Thanks,<br>
{{ config('app.name') }}
</x-mail::message>
