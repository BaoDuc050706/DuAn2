<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Xác nhận đơn hàng #{{ $order['id'] }}</title>
</head>
<body style="font-family: Arial, sans-serif; background-color: #f5f7fa; margin: 0; padding: 0;">
    <div style="max-width: 600px; margin: 20px auto; background-color: #fff; border-radius: 8px; overflow: hidden; box-shadow: 0 2px 8px rgba(0,0,0,0.1);">
        <div style="background-color: #4f46e5; color: #fff; padding: 20px; text-align: center;">
            <h2>Xác nhận đơn hàng</h2>
            <p>Mã đơn: #{{ $order['id'] }}</p>
        </div>

        <div style="padding: 20px; color: #333;">
            <p>Xin chào <strong>{{ $order['full_name'] }}</strong> 👋</p>
            <p>Cảm ơn bạn đã đặt hàng tại <strong>{{ config('app.name') }}</strong>!</p>

            <h3>Chi tiết đơn hàng</h3>
            <ul>
                @foreach($order['items'] as $item)
                    <li>{{ $item['name'] }} (x{{ $item['qty'] }}) - {{ number_format($item['price']) }}đ</li>
                @endforeach
            </ul>

            <p><strong>Tổng tiền hàng:</strong> {{ number_format($order['subtotal']) }}đ</p>
            <p><strong>Phí vận chuyển:</strong> {{ number_format($order['shipping']) }}đ</p>
            <p><strong>Tổng cộng:</strong> {{ number_format($order['total']) }}đ</p>

            <p>Địa chỉ giao hàng: {{ $order['address'] }}</p>
            <p>Phương thức thanh toán: {{ strtoupper($order['payment_method']) }}</p>

            <br>
            <p>Cảm ơn bạn đã tin tưởng và mua hàng ❤️</p>
        </div>
    </div>
</body>
</html>
