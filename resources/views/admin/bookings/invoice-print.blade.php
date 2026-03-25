<!DOCTYPE html>
<html lang="vi">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Hoa don #UL-{{ $booking->id }}</title>
    <style>
        :root {
            --ink: #0f172a;
            --muted: #64748b;
            --line: #e2e8f0;
            --accent: #ea580c;
            --paper: #ffffff;
            --bg: #f1f5f9;
        }

        * {
            box-sizing: border-box;
        }

        body {
            margin: 0;
            background: var(--bg);
            color: var(--ink);
            font-family: "Segoe UI", Tahoma, Geneva, Verdana, sans-serif;
            line-height: 1.4;
        }

        .sheet-wrap {
            max-width: 980px;
            margin: 28px auto;
            padding: 0 12px;
        }

        .sheet {
            background: var(--paper);
            border: 1px solid var(--line);
            border-radius: 16px;
            box-shadow: 0 16px 40px rgba(15, 23, 42, 0.14);
            overflow: hidden;
        }

        .top {
            display: flex;
            justify-content: space-between;
            gap: 18px;
            padding: 32px 34px 20px;
            border-bottom: 1px solid var(--line);
        }

        .hotel h1 {
            margin: 0;
            font-size: 28px;
            letter-spacing: 0.6px;
        }

        .hotel p {
            margin: 4px 0 0;
            color: var(--muted);
            font-size: 13px;
        }

        .invoice-meta {
            text-align: right;
        }

        .invoice-meta h2 {
            margin: 0;
            font-size: 30px;
            letter-spacing: 2px;
            font-weight: 300;
            color: #475569;
        }

        .invoice-meta p {
            margin: 6px 0 0;
            font-size: 14px;
            color: #334155;
            font-weight: 600;
        }

        .block {
            padding: 18px 34px 0;
        }

        .two-col {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 14px;
            margin-top: 8px;
            padding-bottom: 12px;
            border-bottom: 1px solid var(--line);
        }

        .cap {
            font-size: 11px;
            letter-spacing: 1px;
            color: var(--muted);
            text-transform: uppercase;
            font-weight: 700;
            margin: 0 0 6px;
        }

        .value {
            margin: 0;
            font-size: 14px;
            color: #0f172a;
            font-weight: 600;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
        }

        thead th {
            text-align: left;
            font-size: 11px;
            color: #64748b;
            letter-spacing: 0.8px;
            text-transform: uppercase;
            font-weight: 700;
            padding: 10px 0;
            border-bottom: 2px solid #cbd5e1;
        }

        tbody td {
            padding: 12px 0;
            border-bottom: 1px solid #eef2f7;
            vertical-align: top;
            font-size: 14px;
        }

        .desc-title {
            font-weight: 700;
        }

        .sub-lines {
            margin-top: 6px;
            font-size: 12px;
            color: #64748b;
        }

        .sub-lines div {
            margin-top: 3px;
        }

        .num,
        .money {
            text-align: right;
            white-space: nowrap;
        }

        .money {
            font-weight: 700;
        }

        .totals {
            width: 360px;
            margin-left: auto;
            margin-top: 24px;
            border-top: 1px solid var(--line);
            padding-top: 10px;
        }

        .totals .row {
            display: flex;
            justify-content: space-between;
            gap: 12px;
            padding: 6px 0;
            font-size: 14px;
            color: #334155;
        }

        .totals .label {
            color: #64748b;
        }

        .totals .grand {
            margin-top: 6px;
            padding-top: 10px;
            border-top: 2px solid #cbd5e1;
            font-weight: 800;
            font-size: 24px;
            color: #0f172a;
        }

        .bottom {
            margin-top: 26px;
            padding: 16px 34px 24px;
            border-top: 1px solid var(--line);
            color: #64748b;
            font-size: 13px;
            text-align: center;
        }

        .print-tools {
            position: sticky;
            top: 0;
            z-index: 5;
            background: rgba(241, 245, 249, 0.9);
            backdrop-filter: blur(4px);
            display: flex;
            justify-content: center;
            gap: 10px;
            padding: 10px;
            border-bottom: 1px solid #e2e8f0;
        }

        .btn {
            border: 1px solid #cbd5e1;
            border-radius: 10px;
            padding: 8px 14px;
            background: white;
            color: #0f172a;
            font-weight: 700;
            cursor: pointer;
        }

        .btn.primary {
            background: var(--accent);
            border-color: var(--accent);
            color: white;
        }

        @media print {
            @page {
                size: A4;
                margin: 12mm;
            }

            body {
                background: white;
            }

            .print-tools {
                display: none;
            }

            .sheet-wrap {
                margin: 0;
                padding: 0;
                max-width: none;
            }

            .sheet {
                border: none;
                border-radius: 0;
                box-shadow: none;
            }
        }

        @media (max-width: 720px) {
            .top {
                flex-direction: column;
            }

            .invoice-meta {
                text-align: left;
            }

            .two-col {
                grid-template-columns: 1fr;
            }

            .totals {
                width: 100%;
            }
        }
    </style>
</head>

<body>
    @php
        $invoiceCode = 'INV-' . str_pad((string) $booking->id, 6, '0', STR_PAD_LEFT);
        $checkinAt = optional($booking->bookingDetails->min('checkin_date'));
        $checkoutAt = optional($booking->bookingDetails->max('checkout_date'));

        $roomTotal = (float) $booking->bookingDetails->sum('room_amount');
        $serviceTotal = (float) $booking->bookingDetails->sum(function ($detail) {
            return $detail->serviceUsages->sum(fn($usage) => $usage->quantity * $usage->unit_price);
        });
        $surchargeTotal = (float) $booking->bookingDetails->sum('surcharge_amount');
        $grandTotal = $roomTotal + $serviceTotal + $surchargeTotal;
    @endphp

    <div class="print-tools">
        <button class="btn" onclick="window.close()">Dong</button>
        <button class="btn primary" onclick="window.print()">In hoa don</button>
    </div>

    <div class="sheet-wrap">
        <div class="sheet">
            <div class="top">
                <div class="hotel">
                    <h1>URBAN LUXE HOTEL</h1>
                    <p>123 a Vong Ven Bien, Quan Son Tra</p>
                    <p>TP. Da Nang, Viet Nam</p>
                    <p>Tel: +84 236 123 4567</p>
                </div>
                <div class="invoice-meta">
                    <h2>HOA DON</h2>
                    <p>#{{ $invoiceCode }}</p>
                    <p>Ngay: {{ now()->format('d/m/Y') }}</p>
                </div>
            </div>

            <div class="block">
                <div class="two-col">
                    <div>
                        <p class="cap">Khach hang</p>
                        <p class="value">{{ $booking->customer->first_name }} {{ $booking->customer->last_name }}</p>
                        <p class="value">SDT: {{ $booking->customer->phone_number ?? 'N/A' }}</p>
                        <p class="value">Email: {{ $booking->customer->email ?? 'N/A' }}</p>
                    </div>
                    <div style="text-align:right;">
                        <p class="cap">Thong tin luu tru</p>
                        <p class="value">Check-in: {{ $checkinAt ? $checkinAt->format('d/m/Y H:i') : 'N/A' }}</p>
                        <p class="value">Check-out: {{ $checkoutAt ? $checkoutAt->format('d/m/Y H:i') : 'N/A' }}</p>
                        <p class="value">So phong: {{ $booking->bookingDetails->count() }}</p>
                    </div>
                </div>

                <table>
                    <thead>
                        <tr>
                            <th>Mo ta / Dich vu</th>
                            <th class="money">Don gia</th>
                            <th class="num">SL</th>
                            <th class="money">Thanh tien</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($booking->bookingDetails as $detail)
                            @php
                                $serviceLines = $detail->serviceUsages;
                                $serviceAmount = (float) $serviceLines->sum(fn($usage) => $usage->quantity * $usage->unit_price);
                                $lineTotal = (float) $detail->room_amount + $serviceAmount;
                            @endphp
                            <tr>
                                <td>
                                    <div class="desc-title">Phong {{ $detail->room->name }} - {{ $detail->room->roomType->name }}</div>
                                    <div class="sub-lines">
                                        @forelse ($serviceLines as $usage)
                                            <div>- {{ $usage->service->name }} ({{ $usage->quantity }} x {{ number_format($usage->unit_price, 0, ',', '.') }}d)</div>
                                        @empty
                                            <div>- Khong co dich vu su dung</div>
                                        @endforelse
                                    </div>
                                </td>
                                <td class="money">{{ number_format($detail->daily_price, 0, ',', '.') }}d</td>
                                <td class="num">1</td>
                                <td class="money">{{ number_format($lineTotal, 0, ',', '.') }}d</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>

                <div class="totals">
                    <div class="row">
                        <span class="label">Cong tien phong:</span>
                        <strong>{{ number_format($roomTotal, 0, ',', '.') }}d</strong>
                    </div>
                    <div class="row">
                        <span class="label">Phi dich vu:</span>
                        <strong>{{ number_format($serviceTotal, 0, ',', '.') }}d</strong>
                    </div>
                    <div class="row">
                        <span class="label">Phu phi:</span>
                        <strong>{{ number_format($surchargeTotal, 0, ',', '.') }}d</strong>
                    </div>
                    <div class="row grand">
                        <span>TONG CONG:</span>
                        <span>{{ number_format($grandTotal, 0, ',', '.') }}d</span>
                    </div>
                </div>
            </div>

            <div class="bottom">
                Cam on quy khach da su dung dich vu tai Urban Luxe Hotel.
            </div>
        </div>
    </div>

    <script>
        window.addEventListener('load', function() {
            setTimeout(function() {
                window.print();
            }, 200);
        });
    </script>
</body>

</html>
