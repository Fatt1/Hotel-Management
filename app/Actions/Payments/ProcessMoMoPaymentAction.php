<?php

namespace App\Actions\Payments;

use App\Models\Booking;
use Illuminate\Support\Facades\Log;

class ProcessMoMoPaymentAction
{
    /**
     * @param Booking $booking
     * @param float $amount
     * @return string $payUrl (redirect URL) or empty string on error
     */
    public function execute(Booking $booking, float $amount): string
    {
        $partnerCode = env('MOMO_PARTNER_CODE', 'MOMOBKUN20180529');
        $accessKey   = env('MOMO_ACCESS_KEY', 'klm05TvNBzhg7h7j');
        $secretKey   = env('MOMO_SECRET_KEY', 'at67qH6mk8w5Y1nAyMoYKMWACiEi2bsa');
        $endpoint    = env('MOMO_API_ENDPOINT', 'https://test-payment.momo.vn/v2/gateway/api/create');
        
        $ngrokUrl    = rtrim(env('NGROK_URL', url('/')), '/');

        $orderInfo   = "Thanh toan don dat phong " . $booking->id;

        $amountToCharge = (int) $amount;
        $testOverride = (int) env('MOMO_TEST_AMOUNT_OVERRIDE', 0);
        if (app()->environment('local') && $testOverride > 0) {
            $amountToCharge = $testOverride;
            Log::info('MoMo amount override is active', [
                'booking_id' => $booking->id,
                'original_amount' => (int) $amount,
                'override_amount' => $amountToCharge,
            ]);
        }

        $amountStr   = (string) $amountToCharge; // Khong co so thap phan
        $orderId     = (string) $booking->id . '_' . time(); // Tránh lỗi OrderId is duplicated
        $redirectUrl = rtrim((string) env('MOMO_RETURN_URL', $ngrokUrl . '/booking/momo-return'), '/');
        $ipnUrl      = rtrim((string) env('MOMO_IPN_URL', $ngrokUrl . '/api/payment/momo-ipn'), '/');
        $extraData   = "";
        
        $requestId   = time() . "";
        $requestType = "captureWallet";

        // Bước tính chữ ký rawHash
        $rawHash = "accessKey=" . $accessKey .
            "&amount=" . $amountStr .
            "&extraData=" . $extraData .
            "&ipnUrl=" . $ipnUrl .
            "&orderId=" . $orderId .
            "&orderInfo=" . $orderInfo .
            "&partnerCode=" . $partnerCode .
            "&redirectUrl=" . $redirectUrl .
            "&requestId=" . $requestId .
            "&requestType=" . $requestType;

        $signature = hash_hmac("sha256", $rawHash, $secretKey);

        $data = [
            'partnerCode' => $partnerCode,
            'partnerName' => "Test MoMo",
            "storeId"     => "MomoTestStore",
            'requestId'   => $requestId,
            'amount'      => $amountStr,
            'orderId'     => $orderId,
            'orderInfo'   => $orderInfo,
            'redirectUrl' => $redirectUrl,
            'ipnUrl'      => $ipnUrl,
            'lang'        => 'vi',
            'extraData'   => $extraData,
            'requestType' => $requestType,
            'signature'   => $signature
        ];

        Log::info('MoMo create request', [
            'booking_id' => $booking->id,
            'partner_code' => $partnerCode,
            'endpoint' => $endpoint,
            'order_id' => $orderId,
            'amount' => $amountStr,
            'redirect_url' => $redirectUrl,
            'ipn_url' => $ipnUrl,
        ]);

        // Gửi cURL POST
        $ch = curl_init($endpoint);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            'Content-Type: application/json',
            'Content-Length: ' . strlen(json_encode($data))
        ]);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        
        // Disabled SSL verification for local dev sandbox
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);

        $response = curl_exec($ch);
        $err = curl_error($ch);
        curl_close($ch);

        if ($err) {
            Log::error('MoMo cURL Error: ' . $err);
            return '';
        } else {
            $json = json_decode($response, true);
            Log::info('MoMo create response', [
                'booking_id' => $booking->id,
                'response' => $json,
            ]);
            if (isset($json['payUrl'])) {
                return $json['payUrl'];
            }
            Log::error('MoMo API Error Response: ' . $response);
        }

        return '';
    }
}
