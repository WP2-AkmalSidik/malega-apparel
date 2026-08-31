<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Shipping Label - {{ $order->order_number }} - {{ $order->shipment->waybill_id ?? $order->address->tracking_number ?? 'AWB' }}</title>
    <style>
        /* Thermal Printer 100mm x 150mm Page Setup */
        @page {
            size: 100mm 150mm;
            margin: 0;
        }

        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
            -webkit-print-color-adjust: exact;
            print-color-adjust: exact;
        }

        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Helvetica, Arial, sans-serif;
            background-color: #f1f5f9;
            color: #000;
            display: flex;
            justify-content: center;
            align-items: flex-start;
            min-height: 100vh;
            padding: 20px;
        }

        .screen-toolbar {
            position: fixed;
            top: 15px;
            right: 15px;
            display: flex;
            gap: 10px;
            z-index: 9999;
        }

        .btn-print {
            background-color: #0284c7;
            color: #fff;
            padding: 10px 18px;
            font-size: 13px;
            font-weight: 700;
            border: none;
            border-radius: 8px;
            cursor: pointer;
            box-shadow: 0 4px 6px -1px rgba(0,0,0,0.1);
        }

        .btn-close {
            background-color: #475569;
            color: #fff;
            padding: 10px 16px;
            font-size: 13px;
            font-weight: 600;
            border: none;
            border-radius: 8px;
            cursor: pointer;
        }

        /* 100mm x 150mm Label Container */
        .label-container {
            width: 100mm;
            min-height: 145mm;
            max-height: 150mm;
            background: #ffffff;
            border: 2px solid #000;
            padding: 4mm;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
            font-size: 11px;
            line-height: 1.25;
        }

        /* Top Header: Courier & Destination Hub */
        .header-row {
            display: flex;
            border-bottom: 2px solid #000;
            padding-bottom: 3mm;
            margin-bottom: 3mm;
        }

        .courier-box {
            flex: 1.2;
            border-right: 2px solid #000;
            padding-right: 2mm;
        }

        .courier-name {
            font-size: 20px;
            font-weight: 900;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .courier-service {
            font-size: 14px;
            font-weight: 800;
            background: #000;
            color: #fff;
            display: inline-block;
            padding: 1px 6px;
            border-radius: 3px;
            margin-top: 2px;
        }

        .hub-box {
            flex: 1;
            padding-left: 3mm;
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: flex-end;
            text-align: right;
        }

        .hub-code {
            font-size: 18px;
            font-weight: 900;
            font-family: monospace;
        }

        .hub-sub {
            font-size: 9px;
            font-weight: 700;
            text-transform: uppercase;
        }

        /* Barcode Section */
        .barcode-section {
            text-align: center;
            border-bottom: 2px solid #000;
            padding-bottom: 3mm;
            margin-bottom: 3mm;
        }

        .barcode-strip {
            display: flex;
            justify-content: center;
            align-items: center;
            gap: 1.5px;
            height: 38px;
            margin: 2px 0;
        }

        .barcode-bar {
            background-color: #000;
            height: 100%;
        }

        .waybill-text {
            font-size: 16px;
            font-weight: 900;
            font-family: 'Courier New', Courier, monospace;
            letter-spacing: 2px;
            margin-top: 2px;
        }

        /* Addresses: Sender & Receiver */
        .address-grid {
            display: flex;
            border-bottom: 2px solid #000;
            padding-bottom: 3mm;
            margin-bottom: 3mm;
            gap: 3mm;
        }

        .receiver-col {
            flex: 1.5;
        }

        .sender-col {
            flex: 1;
            border-left: 1.5px dashed #000;
            padding-left: 3mm;
            font-size: 9.5px;
        }

        .section-tag {
            font-size: 9px;
            font-weight: 900;
            text-transform: uppercase;
            background: #000;
            color: #fff;
            padding: 1px 4px;
            border-radius: 2px;
            display: inline-block;
            margin-bottom: 2px;
        }

        .receiver-name {
            font-size: 13px;
            font-weight: 800;
            margin: 1px 0;
        }

        .phone-num {
            font-weight: 800;
            font-size: 11px;
            margin-bottom: 2px;
        }

        .address-text {
            font-size: 10px;
            line-height: 1.25;
            word-break: break-word;
        }

        .city-tag {
            font-weight: 800;
            text-transform: uppercase;
            margin-top: 2px;
        }

        /* Order & Payment Info Bar */
        .meta-strip {
            display: flex;
            justify-content: space-between;
            border-bottom: 2px solid #000;
            padding-bottom: 2mm;
            margin-bottom: 2mm;
            font-size: 10px;
        }

        .meta-item {
            display: flex;
            flex-direction: column;
        }

        .meta-item-label {
            font-size: 8.5px;
            font-weight: 700;
            text-transform: uppercase;
            color: #333;
        }

        .meta-item-val {
            font-size: 11px;
            font-weight: 800;
        }

        .cashless-badge {
            background: #000;
            color: #fff;
            padding: 2px 6px;
            font-size: 10px;
            font-weight: 900;
            border-radius: 3px;
            text-align: center;
        }

        /* Items Checklist / Packing Slip */
        .items-section {
            flex-grow: 1;
            margin-bottom: 2mm;
        }

        .items-title {
            font-size: 9px;
            font-weight: 900;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            margin-bottom: 2px;
            display: flex;
            justify-content: space-between;
        }

        .items-table {
            width: 100%;
            border-collapse: collapse;
            font-size: 9px;
        }

        .items-table th {
            border-bottom: 1px solid #000;
            text-align: left;
            padding: 1px 2px;
            font-weight: 800;
        }

        .items-table td {
            padding: 2px 2px;
            border-bottom: 1px dashed #ccc;
            vertical-align: top;
        }

        /* Footer Security Notice */
        .footer-strip {
            border-top: 1.5px solid #000;
            padding-top: 1.5mm;
            display: flex;
            justify-content: space-between;
            align-items: center;
            font-size: 8px;
            font-weight: 700;
        }

        @media print {
            body {
                background: transparent;
                padding: 0;
                margin: 0;
            }

            .screen-toolbar {
                display: none !important;
            }

            .label-container {
                border: 2px solid #000;
                width: 100mm;
                height: 150mm;
                margin: 0;
                page-break-after: always;
            }
        }
    </style>
</head>
<body>

    <!-- On-Screen Action Bar -->
    <div class="screen-toolbar">
        <button class="btn-print" onclick="window.print()">🖨️ Cetak Label Thermal</button>
        <button class="btn-close" onclick="window.close()">✕ Tutup</button>
    </div>

    @php
        $courier = $order->shipment->courier_company ?? $order->address->courier_name ?? 'JNE';
        $service = $order->shipment->courier_service_name ?? 'REG';
        $waybill = $order->shipment->waybill_id ?? $order->address->tracking_number ?? 'WYB-1788147864651';
        $city = strtoupper($order->address->city ?? 'JAKARTA');
        $postal = $order->address->postal_code ?? '10000';
        $totalQty = $order->items->sum('quantity');
        $estWeight = $totalQty * 350;
    @endphp

    <!-- 100mm x 150mm Thermal Label Canvas -->
    <div class="label-container">
        
        <!-- 1. Header: Ekspedisi & Kode Hub Sortir -->
        <div class="header-row">
            <div class="courier-box">
                <div class="courier-name">{{ $courier }}</div>
                <span class="courier-service">{{ $service }}</span>
            </div>
            <div class="hub-box">
                <div class="hub-code">{{ substr($city, 0, 3) }}-{{ $postal }}</div>
                <div class="hub-sub">{{ $city }}</div>
            </div>
        </div>

        <!-- 2. Barcode Ekspedisi & No. Resi AWB (100% Real Scannable Code 128) -->
        <div class="barcode-section">
            <div class="barcode-strip" title="{{ $waybill }}">
                {!! \App\Services\Barcode\Code128BarcodeGenerator::generateSvg($waybill, 42, 2) !!}
            </div>
            <div class="waybill-text">{{ $waybill }}</div>
        </div>

        <!-- 3. Alamat Penerima & Pengirim -->
        <div class="address-grid">
            <div class="receiver-col">
                <span class="section-tag">PENERIMA</span>
                <div class="receiver-name">{{ $order->address->recipient_name }}</div>
                <div class="phone-num">{{ $order->address->phone }}</div>
                <div class="address-text">
                    {{ $order->address->address_line1 }}
                    @if($order->address->address_line2)
                        <br>{{ $order->address->address_line2 }}
                    @endif
                </div>
                <div class="city-tag">{{ $order->address->city }}, {{ $order->address->province }} {{ $order->address->postal_code }}</div>
            </div>

            <div class="sender-col">
                <span class="section-tag">PENGIRIM</span>
                <div style="font-weight: 800; font-size: 11px; margin-top: 1px;">MALEGA APPAREL</div>
                <div>0812-3456-7890</div>
                <div style="margin-top: 2px;">Gudang Pusat Malega<br>Jakarta Pusat, DKI Jakarta 10220</div>
            </div>
        </div>

        <!-- 4. Order Metadata & Cashless Badge -->
        <div class="meta-strip">
            <div class="meta-item">
                <span class="meta-item-label">No. Pesanan</span>
                <span class="meta-item-val" style="font-family: monospace;">#{{ $order->order_number }}</span>
            </div>
            <div class="meta-item">
                <span class="meta-item-label">Berat Paket</span>
                <span class="meta-item-val">{{ number_format($estWeight, 0, ',', '.') }} gr</span>
            </div>
            <div class="meta-item">
                <span class="meta-item-label">Total Item</span>
                <span class="meta-item-val">{{ $totalQty }} Pcs</span>
            </div>
            <div class="meta-item" style="justify-content: center;">
                <div class="cashless-badge">LUNAS (NON-COD)</div>
            </div>
        </div>

        <!-- 5. Packing Slip / Item Checklist untuk Gudang -->
        <div class="items-section">
            <div class="items-title">
                <span>Rincian Produk (Packing Checklist)</span>
                <span>Qty</span>
            </div>
            <table class="items-table">
                <tbody>
                    @foreach($order->items as $item)
                        <tr>
                            <td>
                                <strong>{{ $item->product_name }}</strong>
                                <br><span style="color: #444;">{{ $item->variant_title }} [{{ $item->sku }}]</span>
                            </td>
                            <td style="text-align: right; font-weight: 800; width: 25px;">
                                {{ $item->quantity }}
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <!-- 6. Footer: Brand & Verification Notice -->
        <div class="footer-strip">
            <span>MALEGA APPAREL &bull; BESPOKE ATELIER</span>
            <span>QC VERIFIED &bull; SEGEL RESMI</span>
        </div>

    </div>

    @if($autoPrint)
    <script>
        window.addEventListener('DOMContentLoaded', () => {
            // Auto trigger print dialog on thermal printer
            setTimeout(() => {
                window.print();
            }, 300);
        });
    </script>
    @endif

</body>
</html>
