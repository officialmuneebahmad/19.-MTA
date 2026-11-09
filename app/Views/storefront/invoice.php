<?php
/**
 * Multan Tech Accessories — Official Customer Invoice / Cash on Delivery Receipt
 * MultanTech.pk
 */
$orderNumber = $order['order_number'] ?? '';
$createdDate = !empty($order['created_at']) ? date('d M Y, h:i A', strtotime($order['created_at'])) : date('d M Y');
$subtotal = (float)($order['subtotal'] ?? 0);
$deliveryCharge = (float)($order['delivery_charge'] ?? 0);
$total = (float)($order['total'] ?? 0);
$advance = (float)($order['advance_amount'] ?? 0);
$codDue = (float)($order['cod_due'] ?? max(0, $total - $advance));
$status = strtoupper($order['status'] ?? 'PENDING');
?>
<!DOCTYPE html>
<html lang="en-PK">
<head>
  <!-- Google tag (gtag.js) - GTM-NSR7DDL8 -->
  <script async src="https://www.googletagmanager.com/gtm.js?id=GTM-NSR7DDL8"></script>
  <script>
    window.dataLayer = window.dataLayer || [];
    function gtag(){dataLayer.push(arguments);}
    gtag('js', new Date());
  </script>

  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Invoice #INV-<?= e($orderNumber) ?> — Multan Tech Accessories</title>
  <meta name="robots" content="noindex, nofollow, noarchive">
  <link rel="icon" type="image/png" sizes="32x32" href="/assets/images/favicon-32.png">

  <!-- Google Fonts: Inter & Space Grotesk -->
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&family=Space+Grotesk:wght@600;700;800&display=swap" rel="stylesheet">

  <style>
    *, *::before, *::after {
      box-sizing: border-box;
      margin: 0;
      padding: 0;
    }

    body {
      font-family: 'Inter', -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif;
      background: #F4F3EF;
      color: #141413;
      padding: 2rem 1rem;
      line-height: 1.5;
      -webkit-font-smoothing: antialiased;
    }

    /* Screen Action Bar (Hidden on Print) */
    .action-bar {
      max-width: 820px;
      margin: 0 auto 1.5rem auto;
      display: flex;
      align-items: center;
      justify-content: space-between;
      flex-wrap: wrap;
      gap: 1rem;
    }

    .btn {
      display: inline-flex;
      align-items: center;
      gap: 0.5rem;
      padding: 0.65rem 1.25rem;
      border-radius: 8px;
      font-weight: 700;
      font-size: 0.9rem;
      text-decoration: none;
      cursor: pointer;
      border: 1px solid transparent;
      transition: all 0.15s ease;
    }

    .btn-print {
      background: #FF6B2C;
      color: #FFFFFF;
      box-shadow: 0 2px 8px rgba(255, 107, 44, 0.3);
    }
    .btn-print:hover {
      background: #E8501A;
      transform: translateY(-1px);
    }

    .btn-secondary {
      background: #FFFFFF;
      color: #141413;
      border-color: #E8E6DC;
    }
    .btn-secondary:hover {
      background: #FAF9F5;
    }

    .btn-whatsapp {
      background: #128C7E;
      color: #FFFFFF;
    }
    .btn-whatsapp:hover {
      background: #0B685C;
    }

    /* Invoice Container */
    .invoice-card {
      max-width: 820px;
      margin: 0 auto;
      background: #FFFFFF;
      border: 1px solid #E8E6DC;
      border-radius: 16px;
      padding: 2.75rem 2.5rem;
      box-shadow: 0 4px 20px rgba(0, 0, 0, 0.05);
    }

    /* Header */
    .invoice-header {
      display: flex;
      align-items: flex-start;
      justify-content: space-between;
      border-bottom: 2px solid #F0EEE6;
      padding-bottom: 2rem;
      margin-bottom: 2rem;
      flex-wrap: wrap;
      gap: 1.5rem;
    }

    .brand-section img {
      max-width: 220px;
      height: auto;
      display: block;
      margin-bottom: 0.75rem;
      border-radius: 6px;
    }

    .company-details {
      font-size: 0.825rem;
      color: #5E5D59;
      line-height: 1.6;
    }

    .meta-section {
      text-align: right;
    }

    .invoice-title {
      font-family: 'Space Grotesk', sans-serif;
      font-size: 1.75rem;
      font-weight: 800;
      color: #141413;
      letter-spacing: -0.5px;
      text-transform: uppercase;
    }

    .invoice-number {
      font-family: monospace;
      font-size: 1rem;
      font-weight: 700;
      color: #FF6B2C;
      margin-top: 0.25rem;
    }

    .meta-details {
      margin-top: 0.75rem;
      font-size: 0.85rem;
      color: #5E5D59;
      line-height: 1.6;
    }

    .status-badge {
      display: inline-block;
      padding: 0.25rem 0.65rem;
      border-radius: 999px;
      font-size: 0.75rem;
      font-weight: 700;
      margin-top: 0.5rem;
      background: #E8F9EE;
      color: #128C7E;
      border: 1px solid #C4F3D6;
    }

    /* Client Details Grid */
    .client-grid {
      display: grid;
      grid-template-columns: 1fr 1fr;
      gap: 2rem;
      background: #FAF9F5;
      border: 1px solid #E8E6DC;
      border-radius: 12px;
      padding: 1.5rem;
      margin-bottom: 2rem;
    }

    .grid-block-title {
      font-size: 0.75rem;
      text-transform: uppercase;
      font-weight: 700;
      letter-spacing: 0.5px;
      color: #8E8D88;
      margin-bottom: 0.5rem;
    }

    .grid-block-content {
      font-size: 0.9rem;
      color: #141413;
      line-height: 1.6;
    }

    /* Table */
    .invoice-table-responsive {
      width: 100%;
      overflow-x: auto;
      -webkit-overflow-scrolling: touch;
      margin-bottom: 2rem;
      border: 1px solid #E8E6DC;
      border-radius: 10px;
      background: #FFFFFF;
    }

    .invoice-table-responsive::-webkit-scrollbar {
      height: 6px;
    }

    .invoice-table-responsive::-webkit-scrollbar-thumb {
      background: #D5D3C8;
      border-radius: 3px;
    }

    .invoice-table {
      width: 100%;
      min-width: 540px;
      border-collapse: collapse;
    }

    .invoice-table th {
      background: #F0EEE6;
      color: #141413;
      font-size: 0.8rem;
      font-weight: 700;
      text-transform: uppercase;
      letter-spacing: 0.5px;
      padding: 0.75rem 1rem;
      text-align: left;
      border-bottom: 1px solid #E8E6DC;
    }

    .invoice-table td {
      padding: 0.9rem 1rem;
      border-bottom: 1px solid #F0EEE6;
      font-size: 0.9rem;
      color: #141413;
      vertical-align: middle;
    }

    .invoice-table tr:hover td {
      background: #FCFBF9;
    }

    .prod-sku {
      font-size: 0.75rem;
      color: #8E8D88;
      margin-top: 0.2rem;
      font-family: monospace;
    }

    .text-center { text-align: center; }
    .text-right { text-align: right; }

    /* Summary & Totals */
    .summary-section {
      display: flex;
      justify-content: flex-end;
      margin-bottom: 2.5rem;
    }

    .summary-box {
      width: 100%;
      max-width: 340px;
      display: flex;
      flex-direction: column;
      gap: 0.6rem;
    }

    .summary-row {
      display: flex;
      justify-content: space-between;
      font-size: 0.9rem;
      color: #5E5D59;
    }

    .summary-row strong {
      color: #141413;
    }

    .cod-due-box {
      margin-top: 0.5rem;
      padding: 1rem;
      background: #FFF7ED;
      border: 2px dashed #FF6B2C;
      border-radius: 10px;
      display: flex;
      justify-content: space-between;
      align-items: center;
      font-size: 1.15rem;
      font-weight: 800;
      color: #FF6B2C;
      font-family: 'Space Grotesk', sans-serif;
    }

    /* Policy & Terms Footer */
    .invoice-footer {
      border-top: 1px solid #E8E6DC;
      padding-top: 1.75rem;
      display: grid;
      grid-template-columns: 1fr 1fr;
      gap: 2rem;
      font-size: 0.8rem;
      color: #7E7C74;
      line-height: 1.6;
    }

    .policy-title {
      font-weight: 700;
      color: #141413;
      margin-bottom: 0.35rem;
      text-transform: uppercase;
      font-size: 0.75rem;
      letter-spacing: 0.5px;
    }

    /* Print Styles */
    @media print {
      body {
        background: #FFFFFF !important;
        padding: 0 !important;
        color: #000000 !important;
      }
      .no-print {
        display: none !important;
      }
      .invoice-card {
        border: none !important;
        box-shadow: none !important;
        padding: 0 !important;
        max-width: 100% !important;
      }
      .invoice-table-responsive {
        overflow: visible !important;
        border: none !important;
        margin-bottom: 1.5rem !important;
      }
      .invoice-table {
        min-width: 100% !important;
        width: 100% !important;
      }
      @page {
        margin: 12mm;
        size: A4 portrait;
      }
    }

    @media (max-width: 640px) {
      body {
        padding: 1rem 0.65rem;
      }
      .action-bar {
        flex-direction: column;
        align-items: stretch;
        gap: 0.65rem;
      }
      .action-bar > div {
        display: flex;
        gap: 0.5rem;
        width: 100%;
      }
      .action-bar .btn {
        flex: 1;
        justify-content: center;
        padding: 0.65rem 0.75rem;
        font-size: 0.85rem;
      }
      .invoice-card {
        padding: 1.25rem 1rem;
        border-radius: 12px;
      }
      .invoice-header {
        flex-direction: column;
        gap: 1.25rem;
        padding-bottom: 1.25rem;
        margin-bottom: 1.25rem;
      }
      .brand-section img {
        max-width: 140px;
      }
      .meta-section {
        text-align: left;
        width: 100%;
        padding-top: 1rem;
        border-top: 1px dashed #E8E6DC;
      }
      .invoice-title {
        font-size: 1.45rem;
      }
      .client-grid {
        grid-template-columns: 1fr;
        gap: 1.25rem;
        padding: 1rem;
        margin-bottom: 1.5rem;
      }
      .grid-block-content {
        word-break: break-word;
        overflow-wrap: break-word;
      }
      .invoice-footer {
        grid-template-columns: 1fr;
        gap: 1.25rem;
        padding-top: 1.25rem;
      }
      .summary-section {
        justify-content: stretch;
        margin-bottom: 1.75rem;
      }
      .summary-box {
        max-width: 100%;
      }
      .cod-due-box {
        font-size: 1rem;
        padding: 0.85rem;
        flex-wrap: wrap;
        gap: 0.5rem;
      }
    }
  </style>
</head>
<body>

  <!-- Screen Navigation Bar -->
  <div class="action-bar no-print">
    <div style="display: flex; gap: 0.75rem; align-items: center; flex-wrap: wrap;">
      <button type="button" onclick="handleInvoiceBack()" class="btn btn-secondary" title="Go back to previous page">
        &larr; Back
      </button>
      <button type="button" onclick="window.print();" class="btn btn-print">
        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="6 9 6 2 18 2 18 9"></polyline><path d="M6 18H4a2 2 0 0 1-2-2v-5a2 2 0 0 1 2-2h16a2 2 0 0 1 2 2v5a2 2 0 0 1-2 2h-2"></path><rect x="6" y="14" width="12" height="8"></rect></svg>
        Print / Save PDF
      </button>
    </div>
    <div>
      <a href="https://wa.me/923070655275?text=Hello%20MTA%21%20Inquiring%20about%20Order%20<?= urlencode($orderNumber) ?>" target="_blank" class="btn btn-whatsapp">
        💬 WhatsApp Support
      </a>
    </div>
  </div>

  <!-- Printable Invoice Document -->
  <div class="invoice-card">
    
    <!-- Top Header -->
    <header class="invoice-header">
      <div class="brand-section">
        <img src="/assets/images/logo-invoice.jpg" alt="Multan Tech Accessories" width="220" height="auto">
        <div class="company-details">
          <div><strong>Multan Tech Accessories</strong></div>
          <div>Al-Quresh Phase 3, Sher Shah Road, Multan, Punjab, Pakistan</div>
          <div>Phone / WhatsApp: +92 307 0655275</div>
          <div>Website: multantech.pk &bull; Email: support@multantech.pk</div>
        </div>
      </div>

      <div class="meta-section">
        <div class="invoice-title">Tax Invoice</div>
        <div class="invoice-number">INV-<?= e($orderNumber) ?></div>
        <div class="meta-details">
          <div><strong>Order Ref:</strong> #<?= e($orderNumber) ?></div>
          <div><strong>Date:</strong> <?= e($createdDate) ?></div>
          <div><strong>Payment:</strong> Cash on Delivery (COD)</div>
        </div>
        <span class="status-badge"><?= e($status) ?></span>
      </div>
    </header>

    <!-- Client & Delivery Details -->
    <section class="client-grid">
      <div>
        <div class="grid-block-title">Billed & Shipped To:</div>
        <div class="grid-block-content">
          <div style="font-size: 1.05rem; font-weight: 700; color: #141413;"><?= e($order['customer_name'] ?? 'Valued Customer') ?></div>
          <div><strong>Phone:</strong> <?= e($order['customer_phone'] ?? 'N/A') ?></div>
          <?php if (!empty($order['customer_email'])): ?>
            <div><strong>Email:</strong> <?= e($order['customer_email']) ?></div>
          <?php endif; ?>
          <div><strong>Destination City:</strong> <?= e($order['city'] ?? 'Multan') ?></div>
        </div>
      </div>

      <div>
        <div class="grid-block-title">Shipping Address & Notes:</div>
        <div class="grid-block-content">
          <div><?= nl2br(e($order['shipping_address'] ?? '')) ?></div>
          <?php if (!empty($order['customer_notes'])): ?>
            <div style="margin-top: 0.5rem; font-size: 0.825rem; color: #5E5D59;">
              <strong>Delivery Notes:</strong> <?= e($order['customer_notes']) ?>
            </div>
          <?php endif; ?>
        </div>
      </div>
    </section>

    <!-- Ordered Items Table -->
    <div class="invoice-table-responsive">
      <table class="invoice-table">
        <thead>
          <tr>
            <th style="width: 40px;" class="text-center">#</th>
            <th>Item Description</th>
            <th class="text-center" style="width: 70px;">Qty</th>
            <th class="text-right" style="width: 120px;">Unit Price</th>
            <th class="text-right" style="width: 130px;">Amount</th>
          </tr>
        </thead>
        <tbody>
          <?php $idx = 1; foreach ($items as $item): ?>
            <tr>
              <td class="text-center" style="color: #8E8D88; font-weight: 600;"><?= $idx++ ?></td>
              <td>
                <div style="font-weight: 600;"><?= e($item['product_title']) ?></div>
                <?php if (!empty($item['product_sku'])): ?>
                  <div class="prod-sku">SKU: <?= e($item['product_sku']) ?></div>
                <?php endif; ?>
              </td>
              <td class="text-center"><?= (int)$item['quantity'] ?></td>
              <td class="text-right"><?= mta_money($item['unit_price']) ?></td>
              <td class="text-right" style="font-weight: 700;"><?= mta_money($item['total_price']) ?></td>
            </tr>
          <?php endforeach; ?>
        </tbody>
      </table>
    </div>

    <!-- Totals Calculation -->
    <div class="summary-section">
      <div class="summary-box">
        <div class="summary-row">
          <span>Item Subtotal:</span>
          <strong><?= mta_money($subtotal) ?></strong>
        </div>
        <div class="summary-row">
          <span>Delivery Charges:</span>
          <strong><?= $deliveryCharge <= 0 ? 'FREE' : mta_money($deliveryCharge) ?></strong>
        </div>
        <div class="summary-row" style="border-top: 1px solid #E8E6DC; padding-top: 0.5rem; font-size: 1rem;">
          <span>Order Grand Total:</span>
          <strong><?= mta_money($total) ?></strong>
        </div>
        <?php if ($advance > 0): ?>
          <div class="summary-row" style="color: #128C7E;">
            <span>Advance Deposit Paid<?= !empty($order['advance_reference']) ? ' (TID: ' . e($order['advance_reference']) . ')' : '' ?>:</span>
            <strong>- <?= mta_money($advance) ?></strong>
          </div>
        <?php endif; ?>

        <!-- Outstanding Cash on Delivery Box -->
        <div class="cod-due-box">
          <span>Total COD Payable:</span>
          <span><?= mta_money($codDue) ?></span>
        </div>
      </div>
    </div>

    <!-- Official Warranty & Policies Footer -->
    <footer class="invoice-footer">
      <div>
        <div class="policy-title">3 Days Check Warranty</div>
        <p>
          Please thoroughly inspect and test your accessories within 72 hours of receiving the parcel. In case of any technical defect, notify us immediately via WhatsApp at +92 307 0655275.
        </p>
      </div>

      <div>
        <div class="policy-title">Exchange & Verification Terms</div>
        <p>
          Exchanges are granted strictly for eligible technical defect conditions. The product packaging, accessories, and warranty seal must be retained. This is a computer-generated invoice and requires no physical stamp.
        </p>
      </div>
    </footer>

  </div>

  <script>
    function handleInvoiceBack() {
      // 1. If this window was opened by window.open / target="_blank" and has an active opener
      if (window.opener && !window.opener.closed) {
        window.close();
        return;
      }
      // 2. If document.referrer exists and belongs to the same domain, navigate back to where the user was
      if (document.referrer && document.referrer !== window.location.href) {
        try {
          var ref = new URL(document.referrer);
          if (ref.origin === window.location.origin) {
            window.location.href = document.referrer;
            return;
          }
        } catch (e) {}
      }
      // 3. Fallback to browser history
      if (window.history.length > 1) {
        window.history.back();
        return;
      }
      // 4. Default fallback
      window.location.href = '/order/summary/<?= e($orderNumber) ?>';
    }
  </script>

</body>
</html>

