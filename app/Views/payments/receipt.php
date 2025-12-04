<!DOCTYPE html>
<html lang="uk">
<head>
    <meta charset="UTF-8">
    <title>Квитанція №<?= htmlspecialchars($payment['receipt_number']) ?></title>
    <style>
        body {
            font-family: Arial, sans-serif;
            max-width: 800px;
            margin: 0 auto;
            padding: 20px;
        }
        .receipt-header {
            text-align: center;
            border-bottom: 2px solid #000;
            padding-bottom: 20px;
            margin-bottom: 20px;
        }
        .receipt-header h1 {
            margin: 0 0 10px 0;
        }
        .receipt-info {
            margin: 20px 0;
        }
        .info-row {
            display: flex;
            padding: 10px 0;
            border-bottom: 1px solid #ddd;
        }
        .info-label {
            font-weight: bold;
            width: 200px;
        }
        .info-value {
            flex: 1;
        }
        .amount-box {
            background: #f0f0f0;
            padding: 20px;
            margin: 20px 0;
            text-align: center;
            font-size: 24px;
            font-weight: bold;
        }
        .print-btn {
            text-align: center;
            margin: 20px 0;
        }
        @media print {
            .print-btn {
                display: none;
            }
        }
    </style>
</head>
<body>
    <div class="receipt-header">
        <h1>КВИТАНЦІЯ</h1>
        <p>№<?= htmlspecialchars($payment['receipt_number']) ?></p>
        <p><?= htmlspecialchars($payment['office_name']) ?></p>
        <p><?= htmlspecialchars($payment['office_address']) ?></p>
    </div>

    <div class="receipt-info">
        <div class="info-row">
            <div class="info-label">Дата:</div>
            <div class="info-value"><?= date('d.m.Y', strtotime($payment['payment_date'])) ?></div>
        </div>
        <div class="info-row">
            <div class="info-label">Номер справи:</div>
            <div class="info-value"><?= htmlspecialchars($payment['case_number']) ?></div>
        </div>
        <div class="info-row">
            <div class="info-label">Клієнт:</div>
            <div class="info-value"><?= htmlspecialchars($payment['client_name']) ?></div>
        </div>
        <div class="info-row">
            <div class="info-label">Послуга:</div>
            <div class="info-value"><?= htmlspecialchars($payment['service_name']) ?></div>
        </div>
        <div class="info-row">
            <div class="info-label">Метод платежу:</div>
            <div class="info-value">
                <?php 
                $paymentModel = new Payment();
                echo htmlspecialchars($paymentModel->getMethodLabel($payment['method']));
                ?>
            </div>
        </div>
    </div>

    <div class="amount-box">
        СУМА: <?= number_format($payment['amount'], 2) ?> ₴
    </div>

    <div class="print-btn">
        <button onclick="window.print()" style="padding: 10px 30px; font-size: 16px; cursor: pointer;">
            Друкувати
        </button>
    </div>
</body>
</html>
