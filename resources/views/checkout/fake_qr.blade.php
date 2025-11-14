<!-- QR Code Component - Được include vào checkout.blade.php -->
<div class="alert alert-info">
    <h6 class="alert-heading">Mã QR Chuyển Khoản Ngân Hàng</h6>
    <p class="mb-3">Vui lòng quét mã QR để thực hiện chuyển khoản:</p>
    
    <div class="text-center mb-3">
        @php
            try {
                $qrData = isset($fakeQrData) && !empty($fakeQrData) ? $fakeQrData : 'Ngan hang ABC - STK 123456789';
                $qrCode = \SimpleSoftwareIO\QrCode\Facades\QrCode::size(250)->generate($qrData);
            } catch (\Exception $e) {
                $qrCode = '<div class="alert alert-warning">QR Code tạm thời không khả dụng. Vui lòng chuyển khoản theo thông tin bên dưới.</div>';
            }
        @endphp
        {!! $qrCode !!}
    </div>

    <p class="mb-2"><small><strong>Nội dung chuyển:</strong> Ngân hàng ABC - STK 123456789</small></p>
    <p class="mb-0"><small><strong>Số tiền:</strong> {{ isset($total) ? number_format($total) : '0' }}₫</small></p>
    <p class="text-muted mt-2 mb-0"><small>Dùng app ngân hàng để quét QR và hoàn tất thanh toán</small></p>
</div>
