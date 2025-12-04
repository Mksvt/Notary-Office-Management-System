<div class="page-header">
    <h2>Новий платіж</h2>
</div>

<form method="post" action="<?= BASE_URL ?>/cases/<?= $case['case_id'] ?>/payments/create">
    <input type="hidden" name="csrf_token" value="<?= $csrfToken ?>">
    
    <div class="card" style="margin-bottom: 20px;">
        <div class="card-header">Інформація про справу</div>
        <div class="card-body">
            <div class="info-row">
                <div class="info-label">Номер справи:</div>
                <div class="info-value"><?= htmlspecialchars($case['case_number']) ?></div>
            </div>
            <div class="info-row">
                <div class="info-label">Клієнт:</div>
                <div class="info-value">
                    <?= htmlspecialchars($case['client_last_name'] . ' ' . $case['client_first_name']) ?>
                </div>
            </div>
            <div class="info-row">
                <div class="info-label">Послуга:</div>
                <div class="info-value"><?= htmlspecialchars($case['service_name']) ?></div>
            </div>
            <div class="info-row">
                <div class="info-label">Базова вартість:</div>
                <div class="info-value"><?= number_format($case['base_price'], 2) ?> ₴</div>
            </div>
        </div>
    </div>
    
    <div class="form-row">
        <label for="payment_date">Дата платежу *</label>
        <input type="date" id="payment_date" name="payment_date" 
               value="<?= htmlspecialchars($data['payment_date'] ?? date('Y-m-d')) ?>" required>
        <?php if (!empty($errors['payment_date'])): ?>
            <div class="error"><?= htmlspecialchars($errors['payment_date']) ?></div>
        <?php endif; ?>
    </div>

    <div class="form-row">
        <label for="amount">Сума (₴) *</label>
        <input type="number" id="amount" name="amount" step="0.01" min="0.01"
               value="<?= htmlspecialchars($data['amount'] ?? $case['base_price']) ?>" required>
        <?php if (!empty($errors['amount'])): ?>
            <div class="error"><?= htmlspecialchars($errors['amount']) ?></div>
        <?php endif; ?>
    </div>

    <div class="form-row">
        <label for="method">Метод платежу *</label>
        <select id="method" name="method" required>
            <option value="cash" <?= ($data['method'] ?? 'cash') === 'cash' ? 'selected' : '' ?>>Готівка</option>
            <option value="card" <?= ($data['method'] ?? '') === 'card' ? 'selected' : '' ?>>Картка</option>
            <option value="bank_transfer" <?= ($data['method'] ?? '') === 'bank_transfer' ? 'selected' : '' ?>>Банківський переказ</option>
            <option value="other" <?= ($data['method'] ?? '') === 'other' ? 'selected' : '' ?>>Інше</option>
        </select>
    </div>

    <div class="form-row">
        <label for="status">Статус *</label>
        <select id="status" name="status" required>
            <option value="paid" <?= ($data['status'] ?? 'paid') === 'paid' ? 'selected' : '' ?>>Оплачено</option>
            <option value="pending" <?= ($data['status'] ?? '') === 'pending' ? 'selected' : '' ?>>Очікується</option>
        </select>
    </div>

    <div class="form-row">
        <label for="receipt_number">Номер квитанції</label>
        <input type="text" id="receipt_number" name="receipt_number" 
               value="<?= htmlspecialchars($data['receipt_number'] ?? '') ?>"
               placeholder="Буде згенеровано автоматично">
    </div>

    <div class="form-row">
        <label for="comment">Коментар</label>
        <textarea id="comment" name="comment"><?= htmlspecialchars($data['comment'] ?? '') ?></textarea>
    </div>

    <div class="form-actions">
        <button type="submit" class="btn btn-primary">Створити платіж</button>
        <a href="<?= BASE_URL ?>/cases/view/<?= $case['case_id'] ?>" class="btn btn-secondary">Скасувати</a>
    </div>
</form>
