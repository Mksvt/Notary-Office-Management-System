<div class="page-header">
    <h2>Платежі</h2>
</div>

<div class="search-bar">
    <form method="get" action="<?= BASE_URL ?>/payments">
        <select name="status">
            <option value="">Всі статуси</option>
            <option value="pending" <?= ($statusFilter ?? '') === 'pending' ? 'selected' : '' ?>>Очікується</option>
            <option value="paid" <?= ($statusFilter ?? '') === 'paid' ? 'selected' : '' ?>>Оплачено</option>
            <option value="cancelled" <?= ($statusFilter ?? '') === 'cancelled' ? 'selected' : '' ?>>Скасовано</option>
            <option value="refunded" <?= ($statusFilter ?? '') === 'refunded' ? 'selected' : '' ?>>Повернено</option>
        </select>
        <button type="submit" class="btn btn-primary">Фільтрувати</button>
    </form>
</div>

<?php if (empty($payments)): ?>
    <p>Платежі не знайдені</p>
<?php else: ?>
    <?php 
    $paymentModel = new Payment();
    $totalAmount = 0;
    foreach ($payments as $payment) {
        if ($payment['status'] === 'paid') {
            $totalAmount += $payment['amount'];
        }
    }
    ?>
    
    <div class="alert alert-info">
        <strong>Всього оплачено:</strong> <?= number_format($totalAmount, 2) ?> ₴
    </div>
    
    <table class="data-table">
        <thead>
            <tr>
                <th>Номер справи</th>
                <th>Клієнт</th>
                <th>Послуга</th>
                <th>Дата</th>
                <th>Сума</th>
                <th>Метод</th>
                <th>Статус</th>
                <th>Дії</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($payments as $payment): ?>
                <tr>
                    <td>
                        <a href="<?= BASE_URL ?>/cases/view/<?= $payment['case_id'] ?>">
                            <?= htmlspecialchars($payment['case_number']) ?>
                        </a>
                    </td>
                    <td><?= htmlspecialchars($payment['client_name']) ?></td>
                    <td><?= htmlspecialchars($payment['service_name']) ?></td>
                    <td><?= date('d.m.Y', strtotime($payment['payment_date'])) ?></td>
                    <td><?= number_format($payment['amount'], 2) ?> ₴</td>
                    <td><?= htmlspecialchars($paymentModel->getMethodLabel($payment['method'])) ?></td>
                    <td>
                        <span class="status-badge status-<?= $payment['status'] ?>">
                            <?= htmlspecialchars($paymentModel->getStatusLabel($payment['status'])) ?>
                        </span>
                    </td>
                    <td class="table-actions">
                        <a href="<?= BASE_URL ?>/payments/receipt/<?= $payment['payment_id'] ?>" 
                           class="btn btn-secondary btn-small" target="_blank">Квитанція</a>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
<?php endif; ?>
