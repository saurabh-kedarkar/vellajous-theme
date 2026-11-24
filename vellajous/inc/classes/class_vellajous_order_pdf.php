<?php
/**
 * Vellajous Luxury Order PDF & Invoice Generator
 *
 * @package vellajous
 * @version 1.0.0
 */

if (!defined('ABSPATH')) {
    exit;
}

if (!class_exists('class_vellajous_order_pdf')) {
    class class_vellajous_order_pdf
    {
        /**
         * Generate and output the order PDF as an attachment download
         *
         * @param WC_Order $order
         */
        public static function generate_and_download($order)
        {
            if (!$order instanceof WC_Order) {
                wp_die('Invalid order object.', 'Vellajous Parfums', ['response' => 400]);
            }

            $pdf = new Vellajous_PDF_Builder();
            $pdf->AddPage();

            // Brand Colors
            $forestGreen = [41, 71, 66];   // #294742
            $goldAccent  = [197, 160, 89]; // #c5a059
            $darkText    = [30, 30, 30];
            $mutedText   = [100, 110, 105];
            $lightBg     = [244, 247, 245];
            $borderColor = [218, 226, 221];

            // 1. Header Banner
            $pdf->SetFillColor($forestGreen[0], $forestGreen[1], $forestGreen[2]);
            $pdf->Rect(15, 15, 180, 24, 'F');

            $pdf->SetFont('Helvetica', 'B', 15);
            $pdf->SetTextColor(255, 255, 255);
            $pdf->SetXY(15, 19);
            $pdf->Cell(180, 7, 'VELLAJOUS PRIVE', 0, 1, 'C');

            $pdf->SetFont('Helvetica', '', 8);
            $pdf->SetTextColor($goldAccent[0], $goldAccent[1], $goldAccent[2]);
            $pdf->Cell(180, 5, 'HAUTE PARFUMERIE  *  CONSIGNMENT MANIFEST & TAX INVOICE', 0, 1, 'C');

            $pdf->SetXY(15, 43);

            // 2. Order Metadata & Client Box (Two Columns)
            $pdf->SetFont('Helvetica', 'B', 10);
            $pdf->SetTextColor($forestGreen[0], $forestGreen[1], $forestGreen[2]);
            $pdf->Cell(90, 6, 'ACQUISITION METADATA', 0, 0, 'L');
            $pdf->Cell(90, 6, 'CLIENT INFORMATION', 0, 1, 'L');

            $pdf->SetDrawColor($goldAccent[0], $goldAccent[1], $goldAccent[2]);
            $pdf->Line(15, $pdf->GetY(), 100, $pdf->GetY());
            $pdf->Line(105, $pdf->GetY(), 195, $pdf->GetY());
            $pdf->Ln(3);

            $orderNumber = $order->get_order_number();
            $orderDate   = wc_format_datetime($order->get_date_created(), 'd F Y, h:i A');
            $payMethod   = $order->get_payment_method_title() ? wp_strip_all_tags($order->get_payment_method_title()) : 'Direct';
            $orderStatus = ucfirst($order->get_status());

            $billingName  = wp_strip_all_tags($order->get_formatted_billing_full_name());
            $billingEmail = $order->get_billing_email();
            $billingPhone = $order->get_billing_phone();

            $pdf->SetFont('Helvetica', '', 9);
            $pdf->SetTextColor($darkText[0], $darkText[1], $darkText[2]);

            // Row 1
            $pdf->SetFont('Helvetica', 'B', 9);
            $pdf->Cell(32, 5, 'Order Number:', 0, 0, 'L');
            $pdf->SetFont('Helvetica', '', 9);
            $pdf->Cell(58, 5, '#' . $orderNumber, 0, 0, 'L');

            $pdf->SetFont('Helvetica', 'B', 9);
            $pdf->Cell(28, 5, 'Name:', 0, 0, 'L');
            $pdf->SetFont('Helvetica', '', 9);
            $pdf->Cell(62, 5, self::sanitize_pdf_text($billingName ?: 'Valued Client'), 0, 1, 'L');

            // Row 2
            $pdf->SetFont('Helvetica', 'B', 9);
            $pdf->Cell(32, 5, 'Date Placed:', 0, 0, 'L');
            $pdf->SetFont('Helvetica', '', 9);
            $pdf->Cell(58, 5, self::sanitize_pdf_text($orderDate), 0, 0, 'L');

            $pdf->SetFont('Helvetica', 'B', 9);
            $pdf->Cell(28, 5, 'Email:', 0, 0, 'L');
            $pdf->SetFont('Helvetica', '', 9);
            $pdf->Cell(62, 5, self::sanitize_pdf_text($billingEmail), 0, 1, 'L');

            // Row 3
            $pdf->SetFont('Helvetica', 'B', 9);
            $pdf->Cell(32, 5, 'Payment Method:', 0, 0, 'L');
            $pdf->SetFont('Helvetica', '', 9);
            $pdf->Cell(58, 5, self::sanitize_pdf_text($payMethod), 0, 0, 'L');

            $pdf->SetFont('Helvetica', 'B', 9);
            $pdf->Cell(28, 5, 'Phone:', 0, 0, 'L');
            $pdf->SetFont('Helvetica', '', 9);
            $pdf->Cell(62, 5, self::sanitize_pdf_text($billingPhone ?: 'N/A'), 0, 1, 'L');

            // Row 4
            $pdf->SetFont('Helvetica', 'B', 9);
            $pdf->Cell(32, 5, 'Order Status:', 0, 0, 'L');
            $pdf->SetFont('Helvetica', '', 9);
            $pdf->Cell(58, 5, self::sanitize_pdf_text($orderStatus), 0, 1, 'L');

            $pdf->Ln(4);

            // 3. Addresses Section (Billing & Shipping)
            $pdf->SetFont('Helvetica', 'B', 9);
            $pdf->SetTextColor($forestGreen[0], $forestGreen[1], $forestGreen[2]);
            $pdf->Cell(90, 5, 'BILLING ADDRESS', 0, 0, 'L');
            $pdf->Cell(90, 5, 'DELIVERY DESTINATION', 0, 1, 'L');

            $pdf->SetDrawColor($borderColor[0], $borderColor[1], $borderColor[2]);
            $pdf->Line(15, $pdf->GetY(), 100, $pdf->GetY());
            $pdf->Line(105, $pdf->GetY(), 195, $pdf->GetY());
            $pdf->Ln(2);

            $billingAddrRaw  = wp_strip_all_tags($order->get_formatted_billing_address());
            $shippingAddrRaw = wp_strip_all_tags($order->get_formatted_shipping_address());
            if (empty(trim($shippingAddrRaw))) {
                $shippingAddrRaw = $billingAddrRaw ?: 'Same as Billing Address';
            }

            $bLines = explode("\n", str_replace("\r", "", $billingAddrRaw));
            $sLines = explode("\n", str_replace("\r", "", $shippingAddrRaw));
            $maxLines = max(count($bLines), count($sLines), 1);

            $pdf->SetFont('Helvetica', '', 8.5);
            $pdf->SetTextColor($darkText[0], $darkText[1], $darkText[2]);

            for ($i = 0; $i < $maxLines; $i++) {
                $bLine = isset($bLines[$i]) ? trim($bLines[$i]) : '';
                $sLine = isset($sLines[$i]) ? trim($sLines[$i]) : '';

                $pdf->Cell(90, 4.5, self::sanitize_pdf_text(substr($bLine, 0, 45)), 0, 0, 'L');
                $pdf->Cell(90, 4.5, self::sanitize_pdf_text(substr($sLine, 0, 45)), 0, 1, 'L');
            }

            $pdf->Ln(5);

            // 4. Products Table Header
            $pdf->SetFillColor($forestGreen[0], $forestGreen[1], $forestGreen[2]);
            $pdf->SetDrawColor($forestGreen[0], $forestGreen[1], $forestGreen[2]);
            $pdf->SetTextColor(255, 255, 255);
            $pdf->SetFont('Helvetica', 'B', 8.5);

            $pdf->Cell(96, 7, '  FRAGRANCE FORMULATION & SPECIFICATION', 1, 0, 'L', true);
            $pdf->Cell(20, 7, 'QTY', 1, 0, 'C', true);
            $pdf->Cell(32, 7, 'UNIT PRICE', 1, 0, 'R', true);
            $pdf->Cell(32, 7, 'TOTAL AMOUNT  ', 1, 1, 'R', true);

            // 5. Items Rows
            $pdf->SetFont('Helvetica', '', 8.5);
            $pdf->SetTextColor($darkText[0], $darkText[1], $darkText[2]);
            $pdf->SetDrawColor($borderColor[0], $borderColor[1], $borderColor[2]);

            $fillRow = false;
            foreach ($order->get_items() as $item_id => $item) {
                $productName = $item->get_name();
                $qty         = $item->get_quantity();
                $unitPrice   = $qty > 0 ? ($order->get_item_subtotal($item, false) / 1) : 0;
                $lineTotal   = $order->get_line_subtotal($item, false);

                $formattedUnitPrice = 'Rs. ' . number_format($unitPrice, 2);
                $formattedLineTotal = 'Rs. ' . number_format($lineTotal, 2);

                if ($fillRow) {
                    $pdf->SetFillColor($lightBg[0], $lightBg[1], $lightBg[2]);
                } else {
                    $pdf->SetFillColor(255, 255, 255);
                }

                $pdf->Cell(96, 7, '  ' . self::sanitize_pdf_text(substr($productName, 0, 48)), 1, 0, 'L', true);
                $pdf->Cell(20, 7, (string) $qty, 1, 0, 'C', true);
                $pdf->Cell(32, 7, $formattedUnitPrice, 1, 0, 'R', true);
                $pdf->Cell(32, 7, $formattedLineTotal . '  ', 1, 1, 'R', true);

                $fillRow = !$fillRow;
            }

            // 6. Totals Breakdown
            $orderTotals = $order->get_order_item_totals();
            if (!empty($orderTotals)) {
                foreach ($orderTotals as $key => $total) {
                    $label = wp_strip_all_tags($total['label']);
                    $val   = wp_strip_all_tags($total['value']);
                    $val   = str_replace(['&nbsp;', '&#8377;', '₹'], ['', '', 'Rs. '], $val);

                    $isGrandTotal = ($key === 'order_total');

                    if ($isGrandTotal) {
                        $pdf->SetFillColor($forestGreen[0], $forestGreen[1], $forestGreen[2]);
                        $pdf->SetTextColor(255, 255, 255);
                        $pdf->SetFont('Helvetica', 'B', 9.5);
                        $pdf->Cell(148, 8, 'TOTAL CONSIGNMENT VALUATION:  ', 1, 0, 'R', true);
                        $pdf->Cell(32, 8, self::sanitize_pdf_text($val) . '  ', 1, 1, 'R', true);
                    } else {
                        $pdf->SetFillColor(255, 255, 255);
                        $pdf->SetTextColor($darkText[0], $darkText[1], $darkText[2]);
                        $pdf->SetFont('Helvetica', '', 8.5);
                        $pdf->Cell(148, 6, self::sanitize_pdf_text($label) . '  ', 1, 0, 'R');
                        $pdf->Cell(32, 6, self::sanitize_pdf_text($val) . '  ', 1, 1, 'R');
                    }
                }
            }

            $pdf->Ln(8);

            // 7. Security & Authenticity Footer
            $pdf->SetFillColor($lightBg[0], $lightBg[1], $lightBg[2]);
            $pdf->SetDrawColor($goldAccent[0], $goldAccent[1], $goldAccent[2]);
            $pdf->Rect(15, $pdf->GetY(), 180, 22, 'FD');

            $pdf->SetFont('Helvetica', 'B', 8.5);
            $pdf->SetTextColor($forestGreen[0], $forestGreen[1], $forestGreen[2]);
            $pdf->SetXY(18, $pdf->GetY() + 3);
            $pdf->Cell(174, 4.5, 'VELLAJOUS ATELIER - 100% AUTHENTIC LUXURY CERTIFICATE', 0, 1, 'C');

            $pdf->SetFont('Helvetica', '', 7.5);
            $pdf->SetTextColor($mutedText[0], $mutedText[1], $mutedText[2]);
            $pdf->Cell(174, 4, 'Handcrafted with fine French and Arabian fragrance oils. Formulated for exceptional sillage and longevity.', 0, 1, 'C');
            $pdf->Cell(174, 4, 'Concierge Support: concierge@vellajous.com  |  Official Portal: www.vellajous.com', 0, 1, 'C');

            // Send download headers
            $pdfOutput = $pdf->Output();
            $filename  = 'Vellajous-Order-#' . $orderNumber . '.pdf';

            // Clean output buffer before sending headers
            if (ob_get_length()) {
                ob_clean();
            }

            header('Content-Description: File Transfer');
            header('Content-Type: application/pdf');
            header('Content-Disposition: attachment; filename="' . $filename . '"');
            header('Content-Transfer-Encoding: binary');
            header('Expires: 0');
            header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
            header('Pragma: public');
            header('Content-Length: ' . strlen($pdfOutput));

            echo $pdfOutput;
            exit;
        }

        /**
         * Render full-screen print-ready luxury HTML Tax Invoice
         *
         * @param WC_Order $order
         */
        public static function render_html_invoice($order)
        {
            $orderNumber = $order->get_order_number();
            $orderDate   = wc_format_datetime($order->get_date_created(), 'd F Y, h:i A');
            $payMethod   = $order->get_payment_method_title() ? wp_strip_all_tags($order->get_payment_method_title()) : 'Direct';
            $billingName = wp_strip_all_tags($order->get_formatted_billing_full_name());
            $pdfDownloadUrl = add_query_arg([
                'vellajous_action' => 'download_order_pdf',
                'order_id'         => $order->get_id(),
                'order_key'        => $order->get_order_key(),
            ], home_url('/'));
            ?>
            <!DOCTYPE html>
            <html lang="en">
            <head>
                <meta charset="UTF-8">
                <meta name="viewport" content="width=device-width, initial-scale=1.0">
                <title><?php echo sprintf(esc_html__('Vellajous Tax Invoice - Order #%s', 'vellajous'), esc_html($orderNumber)); ?></title>
                <link rel="preconnect" href="https://fonts.googleapis.com">
                <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
                <link href="https://fonts.googleapis.com/css2?family=Cinzel:wght@600;700&family=Plus+Jakarta+Sans:wght@400;500;600;700&display=swap" rel="stylesheet">
                <style>
                    :root {
                        --v-green: #294742;
                        --v-gold: #c5a059;
                        --v-dark: #1c322f;
                        --v-subtle: #f0f5f2;
                        --v-border: #dae2dd;
                    }
                    *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }
                    body {
                        font-family: 'Plus Jakarta Sans', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
                        color: #1a1a1a;
                        background: #f4f6f4;
                        line-height: 1.5;
                        font-size: 14px;
                        padding: 30px 15px;
                    }
                    .invoice-actions-bar {
                        max-width: 860px;
                        margin: 0 auto 20px auto;
                        display: flex;
                        justify-content: space-between;
                        align-items: center;
                        gap: 12px;
                        flex-wrap: wrap;
                    }
                    .action-btn {
                        display: inline-flex;
                        align-items: center;
                        gap: 8px;
                        padding: 10px 20px;
                        border-radius: 6px;
                        font-family: 'Cinzel', serif;
                        font-size: 12px;
                        font-weight: 700;
                        letter-spacing: 1px;
                        text-transform: uppercase;
                        text-decoration: none;
                        cursor: pointer;
                        border: none;
                        transition: all 0.25s ease;
                    }
                    .action-btn--primary {
                        background: var(--v-green);
                        color: #ffffff;
                    }
                    .action-btn--primary:hover {
                        background: var(--v-dark);
                    }
                    .action-btn--gold {
                        background: linear-gradient(135deg, #c5a059 0%, #a88339 100%);
                        color: #ffffff;
                        box-shadow: 0 4px 14px rgba(197, 160, 89, 0.35);
                    }
                    .action-btn--outline {
                        background: #ffffff;
                        color: var(--v-green);
                        border: 1.5px solid var(--v-border);
                    }
                    .action-btn--outline:hover {
                        border-color: var(--v-green);
                    }
                    .invoice-card {
                        max-width: 860px;
                        margin: 0 auto;
                        background: #ffffff;
                        border: 1px solid var(--v-border);
                        border-radius: 12px;
                        box-shadow: 0 6px 30px rgba(0, 0, 0, 0.05);
                        overflow: hidden;
                    }
                    .invoice-header {
                        background: linear-gradient(135deg, #294742 0%, #1c322f 100%);
                        padding: 32px 40px;
                        color: #ffffff;
                        display: flex;
                        justify-content: space-between;
                        align-items: center;
                        border-bottom: 3px solid var(--v-gold);
                    }
                    .brand-name {
                        font-family: 'Cinzel', serif;
                        font-size: 26px;
                        font-weight: 700;
                        letter-spacing: 3px;
                        color: #ffffff;
                        line-height: 1.2;
                    }
                    .brand-tagline {
                        font-family: 'Cinzel', serif;
                        font-size: 11px;
                        letter-spacing: 2.5px;
                        color: var(--v-gold);
                        text-transform: uppercase;
                        margin-top: 4px;
                    }
                    .invoice-title-block {
                        text-align: right;
                    }
                    .invoice-badge {
                        display: inline-block;
                        background: rgba(197, 160, 89, 0.2);
                        border: 1px solid var(--v-gold);
                        color: #f7e8c3;
                        font-size: 10px;
                        font-weight: 700;
                        letter-spacing: 1.5px;
                        text-transform: uppercase;
                        padding: 3px 10px;
                        border-radius: 4px;
                        margin-bottom: 6px;
                    }
                    .invoice-num {
                        font-size: 16px;
                        font-weight: 700;
                        letter-spacing: 1px;
                    }
                    .invoice-body {
                        padding: 35px 40px;
                    }
                    .meta-grid {
                        display: grid;
                        grid-template-columns: repeat(4, 1fr);
                        gap: 16px;
                        background: #fbfbf9;
                        border: 1px solid #e7ebe8;
                        border-radius: 8px;
                        padding: 18px 20px;
                        margin-bottom: 30px;
                    }
                    .meta-item-label {
                        display: block;
                        font-size: 10.5px;
                        font-weight: 700;
                        text-transform: uppercase;
                        letter-spacing: 1px;
                        color: #798d83;
                        margin-bottom: 4px;
                    }
                    .meta-item-value {
                        font-size: 13.5px;
                        font-weight: 600;
                        color: var(--v-green);
                    }
                    .addresses-grid {
                        display: grid;
                        grid-template-columns: 1fr 1fr;
                        gap: 30px;
                        margin-bottom: 32px;
                    }
                    .address-box {
                        background: #ffffff;
                        border: 1px solid #e3e9e5;
                        border-radius: 8px;
                        padding: 20px 22px;
                    }
                    .address-title {
                        font-family: 'Cinzel', serif;
                        font-size: 12px;
                        font-weight: 700;
                        letter-spacing: 1.5px;
                        text-transform: uppercase;
                        color: var(--v-green);
                        margin-bottom: 10px;
                        padding-bottom: 8px;
                        border-bottom: 1.5px solid var(--v-gold);
                    }
                    .address-text {
                        font-style: normal;
                        color: #444;
                        line-height: 1.6;
                        font-size: 13px;
                    }
                    .items-table {
                        width: 100%;
                        border-collapse: collapse;
                        margin-bottom: 30px;
                    }
                    .items-table th {
                        background: var(--v-green);
                        color: #ffffff;
                        font-family: 'Cinzel', serif;
                        font-size: 11px;
                        font-weight: 700;
                        letter-spacing: 1.5px;
                        text-transform: uppercase;
                        padding: 12px 16px;
                        text-align: left;
                    }
                    .items-table th.text-center, .items-table td.text-center { text-align: center; }
                    .items-table th.text-right, .items-table td.text-right { text-align: right; }
                    .items-table td {
                        padding: 14px 16px;
                        border-bottom: 1px solid #e6ece8;
                        font-size: 13.5px;
                    }
                    .items-table tr:nth-child(even) td {
                        background: #fbfcfb;
                    }
                    .totals-wrap {
                        display: flex;
                        justify-content: flex-end;
                        margin-bottom: 35px;
                    }
                    .totals-table {
                        width: 380px;
                        border-collapse: collapse;
                    }
                    .totals-table td {
                        padding: 8px 12px;
                        font-size: 13.5px;
                    }
                    .totals-table td:last-child {
                        text-align: right;
                        font-weight: 600;
                    }
                    .totals-table tr.grand-total td {
                        background: var(--v-green);
                        color: #ffffff;
                        font-family: 'Cinzel', serif;
                        font-size: 14px;
                        font-weight: 700;
                        padding: 12px 14px;
                        border-radius: 4px;
                    }
                    .invoice-footer {
                        border-top: 1px dashed var(--v-border);
                        padding-top: 25px;
                        text-align: center;
                        color: #6a7c72;
                        font-size: 12px;
                    }
                    .footer-seal {
                        font-family: 'Cinzel', serif;
                        font-size: 12px;
                        font-weight: 700;
                        letter-spacing: 1.5px;
                        color: var(--v-gold);
                        margin-bottom: 4px;
                    }
                    @media print {
                        body { background: #ffffff !important; padding: 0 !important; }
                        .invoice-actions-bar { display: none !important; }
                        .invoice-card { box-shadow: none !important; border: none !important; }
                        @page { size: A4; margin: 15mm; }
                    }
                    @media (max-width: 700px) {
                        .invoice-header { flex-direction: column; text-align: center; gap: 16px; padding: 24px; }
                        .invoice-title-block { text-align: center; }
                        .meta-grid { grid-template-columns: 1fr 1fr; }
                        .addresses-grid { grid-template-columns: 1fr; }
                        .totals-table { width: 100%; }
                        .invoice-body { padding: 20px; }
                    }
                </style>
            </head>
            <body>

                <div class="invoice-actions-bar">
                    <a href="<?php echo esc_url($order->get_checkout_order_received_url()); ?>" class="action-btn action-btn--outline">
                        &larr; <?php esc_html_e('Back to Order Received', 'vellajous'); ?>
                    </a>
                    <div style="display: flex; gap: 10px; flex-wrap: wrap;">
                        <button type="button" onclick="window.print();" class="action-btn action-btn--primary">
                            <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="6 9 6 2 18 2 18 9"></polyline><path d="M6 18H4a2 2 0 0 1-2-2v-5a2 2 0 0 1 2-2h16a2 2 0 0 1 2 2v5a2 2 0 0 1-2 2h-2"></path><rect x="6" y="14" width="12" height="8"></rect></svg>
                            <span><?php esc_html_e('Print Receipt', 'vellajous'); ?></span>
                        </button>
                        <a href="<?php echo esc_url($pdfDownloadUrl); ?>" class="action-btn action-btn--gold" download>
                            <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"></path><polyline points="7 10 12 15 17 10"></polyline><line x1="12" y1="15" x2="12" y2="3"></line></svg>
                            <span><?php esc_html_e('Download PDF File', 'vellajous'); ?></span>
                        </a>
                    </div>
                </div>

                <div class="invoice-card">
                    <div class="invoice-header">
                        <div>
                            <div class="brand-name">VELLAJOUS</div>
                            <div class="brand-tagline">Haute Parfumerie &middot; Prive Consignment</div>
                        </div>
                        <div class="invoice-title-block">
                            <span class="invoice-badge">Tax Manifest</span>
                            <div class="invoice-num">#<?php echo esc_html($orderNumber); ?></div>
                        </div>
                    </div>

                    <div class="invoice-body">
                        <div class="meta-grid">
                            <div>
                                <span class="meta-item-label"><?php esc_html_e('Order Date', 'vellajous'); ?></span>
                                <span class="meta-item-value"><?php echo esc_html($orderDate); ?></span>
                            </div>
                            <div>
                                <span class="meta-item-label"><?php esc_html_e('Payment Gateway', 'vellajous'); ?></span>
                                <span class="meta-item-value"><?php echo esc_html($payMethod); ?></span>
                            </div>
                            <div>
                                <span class="meta-item-label"><?php esc_html_e('Order Status', 'vellajous'); ?></span>
                                <span class="meta-item-value"><?php echo esc_html(ucfirst($order->get_status())); ?></span>
                            </div>
                            <div>
                                <span class="meta-item-label"><?php esc_html_e('Primary Client', 'vellajous'); ?></span>
                                <span class="meta-item-value"><?php echo esc_html($billingName ?: 'Valued Client'); ?></span>
                            </div>
                        </div>

                        <div class="addresses-grid">
                            <div class="address-box">
                                <h4 class="address-title"><?php esc_html_e('Billing Address', 'vellajous'); ?></h4>
                                <address class="address-text">
                                    <?php echo wp_kses_post($order->get_formatted_billing_address()); ?>
                                    <?php if ($order->get_billing_phone()) : ?>
                                        <div style="margin-top: 6px;"><strong>Tel:</strong> <?php echo esc_html($order->get_billing_phone()); ?></div>
                                    <?php endif; ?>
                                    <?php if ($order->get_billing_email()) : ?>
                                        <div><strong>Email:</strong> <?php echo esc_html($order->get_billing_email()); ?></div>
                                    <?php endif; ?>
                                </address>
                            </div>

                            <div class="address-box">
                                <h4 class="address-title"><?php esc_html_e('Delivery Destination', 'vellajous'); ?></h4>
                                <address class="address-text">
                                    <?php 
                                    $shippingAddr = $order->get_formatted_shipping_address();
                                    echo wp_kses_post($shippingAddr ? $shippingAddr : $order->get_formatted_billing_address()); 
                                    ?>
                                    <?php if ($order->get_shipping_phone()) : ?>
                                        <div style="margin-top: 6px;"><strong>Tel:</strong> <?php echo esc_html($order->get_shipping_phone()); ?></div>
                                    <?php endif; ?>
                                </address>
                            </div>
                        </div>

                        <table class="items-table">
                            <thead>
                                <tr>
                                    <th><?php esc_html_e('Fragrance Formulation', 'vellajous'); ?></th>
                                    <th class="text-center"><?php esc_html_e('Qty', 'vellajous'); ?></th>
                                    <th class="text-right"><?php esc_html_e('Unit Price', 'vellajous'); ?></th>
                                    <th class="text-right"><?php esc_html_e('Total Amount', 'vellajous'); ?></th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($order->get_items() as $item_id => $item) : ?>
                                    <tr>
                                        <td>
                                            <strong><?php echo esc_html($item->get_name()); ?></strong>
                                            <?php wc_display_item_meta($item); ?>
                                        </td>
                                        <td class="text-center"><?php echo esc_html($item->get_quantity()); ?></td>
                                        <td class="text-right"><?php echo $order->get_formatted_line_subtotal($item); ?></td>
                                        <td class="text-right"><strong><?php echo $order->get_formatted_line_subtotal($item); ?></strong></td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>

                        <div class="totals-wrap">
                            <table class="totals-table">
                                <?php
                                foreach ($order->get_order_item_totals() as $key => $total) {
                                    $isGrandTotal = ($key === 'order_total');
                                    ?>
                                    <tr class="<?php echo $isGrandTotal ? 'grand-total' : ''; ?>">
                                        <td><?php echo esc_html($total['label']); ?></td>
                                        <td><?php echo wp_kses_post($total['value']); ?></td>
                                    </tr>
                                    <?php
                                }
                                ?>
                            </table>
                        </div>

                        <div class="invoice-footer">
                            <div class="footer-seal">&diams; 100% AUTHENTIC LUXURY FRAGRANCE FORMULATIONS &diams;</div>
                            <p><?php esc_html_e('Handcrafted with pure French and Arabian fragrance oils. Thank you for choosing Vellajous Privé.', 'vellajous'); ?></p>
                            <p style="margin-top: 4px;"><?php esc_html_e('Concierge Support: concierge@vellajous.com | www.vellajous.com', 'vellajous'); ?></p>
                        </div>
                    </div>
                </div>

                <?php if (isset($_GET['print']) && $_GET['print'] === '1') : ?>
                    <script>
                        window.addEventListener('load', function() {
                            window.print();
                        });
                    </script>
                <?php endif; ?>

            </body>
            </html>
            <?php
        }

        /**
         * Clean string for Latin1 standard PDF font encoding
         *
         * @param string $txt
         * @return string
         */
        protected static function sanitize_pdf_text($txt)
        {
            $txt = html_entity_decode($txt, ENT_QUOTES, 'UTF-8');
            $txt = str_replace(
                ['₹', '&#8377;', '&nbsp;', '’', '‘', '“', '”', '–', '—', '•', '♦'],
                ['Rs. ', 'Rs. ', ' ', "'", "'", '"', '"', '-', '-', '*', '*'],
                $txt
            );
            // Convert to ISO-8859-1 (Latin1)
            if (function_exists('iconv')) {
                $converted = @iconv('UTF-8', 'ISO-8859-1//TRANSLIT', $txt);
                if ($converted !== false) {
                    return $converted;
                }
            }
            return preg_replace('/[^\x20-\x7E]/', '', $txt);
        }
    }
}

/**
 * Lightweight, rock-solid standalone PDF 1.4 Builder Class
 */
if (!class_exists('Vellajous_PDF_Builder')) {
    class Vellajous_PDF_Builder
    {
        protected $page = 0;
        protected $pages = [];
        protected $k = 72 / 25.4; // mm to points factor
        protected $w = 210;
        protected $h = 297;
        protected $lMargin = 15;
        protected $tMargin = 15;
        protected $x = 15;
        protected $y = 15;
        protected $fontSizePt = 12;
        protected $fontSize = 4.23;
        protected $currentStyle = '';
        protected $textColor = '0 g';
        protected $drawColor = '0 G';
        protected $fillColor = '1 g';

        public function AddPage()
        {
            $this->page++;
            $this->pages[$this->page] = '';
            $this->x = $this->lMargin;
            $this->y = $this->tMargin;
        }

        public function SetFont($family, $style = '', $size = 12)
        {
            $this->currentStyle = strtoupper($style);
            $this->fontSizePt   = $size;
            $this->fontSize     = $size / $this->k;
        }

        public function SetTextColor($r, $g = null, $b = null)
        {
            if ($g === null) {
                $this->textColor = sprintf('%.3F g', $r / 255);
            } else {
                $this->textColor = sprintf('%.3F %.3F %.3F rg', $r / 255, $g / 255, $b / 255);
            }
        }

        public function SetFillColor($r, $g = null, $b = null)
        {
            if ($g === null) {
                $this->fillColor = sprintf('%.3F g', $r / 255);
            } else {
                $this->fillColor = sprintf('%.3F %.3F %.3F rg', $r / 255, $g / 255, $b / 255);
            }
        }

        public function SetDrawColor($r, $g = null, $b = null)
        {
            if ($g === null) {
                $this->drawColor = sprintf('%.3F G', $r / 255);
            } else {
                $this->drawColor = sprintf('%.3F %.3F %.3F RG', $r / 255, $g / 255, $b / 255);
            }
        }

        public function Line($x1, $y1, $x2, $y2)
        {
            $this->pages[$this->page] .= sprintf(
                'q %s 0.6 w %.2F %.2F m %.2F %.2F l S Q' . "\n",
                $this->drawColor,
                $x1 * $this->k,
                ($this->h - $y1) * $this->k,
                $x2 * $this->k,
                ($this->h - $y2) * $this->k
            );
        }

        public function Rect($x, $y, $w, $h, $style = '')
        {
            $op = 'S';
            if ($style === 'F') $op = 'f';
            elseif ($style === 'FD' || $style === 'DF') $op = 'B';
            $this->pages[$this->page] .= sprintf(
                'q %s %s 0.6 w %.2F %.2F %.2F %.2F re %s Q' . "\n",
                $this->fillColor,
                $this->drawColor,
                $x * $this->k,
                ($this->h - $y - $h) * $this->k,
                $w * $this->k,
                $h * $this->k,
                $op
            );
        }

        public function SetXY($x, $y)
        {
            $this->x = $x;
            $this->y = $y;
        }

        public function GetX() { return $this->x; }
        public function GetY() { return $this->y; }

        public function Cell($w, $h = 0, $txt = '', $border = 0, $ln = 0, $align = '', $fill = false)
        {
            $k = $this->k;
            $txtEscaped = $this->escapeText($txt);

            $s = '';
            if ($fill || $border == 1) {
                $op = 'S';
                if ($fill && $border == 1) $op = 'B';
                elseif ($fill) $op = 'f';
                $s .= sprintf(
                    'q %s %s 0.6 w %.2F %.2F %.2F %.2F re %s Q' . "\n",
                    $this->fillColor,
                    $this->drawColor,
                    $this->x * $k,
                    ($this->h - ($this->y + $h)) * $k,
                    $w * $k,
                    $h * $k,
                    $op
                );
            }

            if ($txtEscaped !== '') {
                $fontName = $this->getFontKey();
                $textX = $this->x;
                $textY = $this->y + ($h / 2) + ($this->fontSize / 2.7);

                $approxCharWidth = ($this->fontSizePt * 0.52) / $k;
                $textWidth = strlen($txt) * $approxCharWidth;

                if ($align === 'C') {
                    $textX = $this->x + max(0, ($w - $textWidth) / 2);
                } elseif ($align === 'R') {
                    $textX = $this->x + max(0, $w - $textWidth - 2.5);
                } else {
                    $textX = $this->x + 2;
                }

                $s .= sprintf(
                    'BT %s /%s %.2F Tf %.2F %.2F Td (%s) Tj ET' . "\n",
                    $this->textColor,
                    $fontName,
                    $this->fontSizePt,
                    $textX * $k,
                    ($this->h - $textY) * $k,
                    $txtEscaped
                );
            }

            $this->pages[$this->page] .= $s;
            if ($ln > 0) {
                $this->y += $h;
                $this->x = $this->lMargin;
            } else {
                $this->x += $w;
            }
        }

        public function Ln($h = null)
        {
            $this->x = $this->lMargin;
            $this->y += ($h !== null ? $h : $this->fontSize);
        }

        protected function getFontKey()
        {
            if ($this->currentStyle === 'B') return 'F2';
            if ($this->currentStyle === 'I') return 'F3';
            return 'F1';
        }

        protected function escapeText($s)
        {
            $s = str_replace('\\', '\\\\', $s);
            $s = str_replace('(', '\\(', $s);
            $s = str_replace(')', '\\)', $s);
            return $s;
        }

        public function Output()
        {
            $out = "%PDF-1.4\n";
            $offsets = [];

            // Catalog
            $offsets[1] = strlen($out);
            $out .= "1 0 obj\n<< /Type /Catalog /Pages 2 0 R >>\nendobj\n";

            // Pages index
            $pageObjIds = [];
            for ($i = 1; $i <= $this->page; $i++) {
                $pageObjIds[$i] = 2 + $i;
            }

            $kids = '';
            foreach ($pageObjIds as $id) {
                $kids .= $id . " 0 R ";
            }
            $offsets[2] = strlen($out);
            $out .= "2 0 obj\n<< /Type /Pages /Kids [" . trim($kids) . "] /Count " . $this->page . " >>\nendobj\n";

            $nextObjId = 2 + $this->page + 1;
            $f1ObjId = $nextObjId++;
            $f2ObjId = $nextObjId++;
            $f3ObjId = $nextObjId++;

            // Stream objects for each page
            for ($i = 1; $i <= $this->page; $i++) {
                $contentObjId = $nextObjId++;
                $compressed = function_exists('gzcompress') ? gzcompress($this->pages[$i]) : false;
                $isCompressed = ($compressed !== false);
                $streamData = $isCompressed ? $compressed : $this->pages[$i];
                $streamLen = strlen($streamData);

                $offsets[$pageObjIds[$i]] = strlen($out);
                $out .= sprintf(
                    "%d 0 obj\n<< /Type /Page /Parent 2 0 R /MediaBox [0 0 %.2F %.2F] /Contents %d 0 R /Resources << /Font << /F1 %d 0 R /F2 %d 0 R /F3 %d 0 R >> >> >>\nendobj\n",
                    $pageObjIds[$i],
                    $this->w * $this->k,
                    $this->h * $this->k,
                    $contentObjId,
                    $f1ObjId,
                    $f2ObjId,
                    $f3ObjId
                );

                $filter = $isCompressed ? '/Filter /FlateDecode ' : '';
                $offsets[$contentObjId] = strlen($out);
                $out .= sprintf(
                    "%d 0 obj\n<< %s/Length %d >>\nstream\n%s\nendstream\nendobj\n",
                    $contentObjId,
                    $filter,
                    $streamLen,
                    $streamData
                );
            }

            // Fonts
            $offsets[$f1ObjId] = strlen($out);
            $out .= sprintf("%d 0 obj\n<< /Type /Font /Subtype /Type1 /BaseFont /Helvetica /Encoding /WinAnsiEncoding >>\nendobj\n", $f1ObjId);

            $offsets[$f2ObjId] = strlen($out);
            $out .= sprintf("%d 0 obj\n<< /Type /Font /Subtype /Type1 /BaseFont /Helvetica-Bold /Encoding /WinAnsiEncoding >>\nendobj\n", $f2ObjId);

            $offsets[$f3ObjId] = strlen($out);
            $out .= sprintf("%d 0 obj\n<< /Type /Font /Subtype /Type1 /BaseFont /Helvetica-Oblique /Encoding /WinAnsiEncoding >>\nendobj\n", $f3ObjId);

            // Cross reference table
            $xrefOffset = strlen($out);
            $totalObjs = $nextObjId - 1;
            $out .= "xref\n0 " . ($totalObjs + 1) . "\n";
            $out .= "0000000000 65535 f \n";
            for ($i = 1; $i <= $totalObjs; $i++) {
                $out .= sprintf("%010d 00000 n \n", $offsets[$i]);
            }

            // Trailer
            $out .= sprintf("trailer\n<< /Size %d /Root 1 0 R >>\nstartxref\n%d\n%%%%EOF", $totalObjs + 1, $xrefOffset);

            return $out;
        }
    }
}
