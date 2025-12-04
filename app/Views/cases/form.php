<div class="page-header">
    <h2>Нова справа</h2>
</div>

<?php if (!empty($errors['general'])): ?>
    <div class="alert alert-error">
        <?= htmlspecialchars($errors['general']) ?>
    </div>
<?php endif; ?>

<form method="post" action="<?= BASE_URL ?>/cases/create">
    <input type="hidden" name="csrf_token" value="<?= $csrfToken ?>">
    
    <div class="form-row">
        <label for="client_id">Клієнт *</label>
        <select id="client_id" name="client_id" required>
            <option value="">Оберіть клієнта</option>
            <?php foreach ($clients as $client): ?>
                <option value="<?= $client['client_id'] ?>" <?= ($data['client_id'] ?? '') == $client['client_id'] ? 'selected' : '' ?>>
                    <?= htmlspecialchars($client['last_name'] . ' ' . $client['first_name']) ?>
                    <?php if ($client['tax_id']): ?>
                        (ІПН: <?= htmlspecialchars($client['tax_id']) ?>)
                    <?php endif; ?>
                </option>
            <?php endforeach; ?>
        </select>
        <?php if (!empty($errors['client_id'])): ?>
            <div class="error"><?= htmlspecialchars($errors['client_id']) ?></div>
        <?php endif; ?>
    </div>

    <div class="form-row">
        <label for="notary_id">Нотаріус *</label>
        <select id="notary_id" name="notary_id" required>
            <option value="">Оберіть нотаріуса</option>
            <?php foreach ($notaries as $notary): ?>
                <option value="<?= $notary['notary_id'] ?>" <?= ($data['notary_id'] ?? '') == $notary['notary_id'] ? 'selected' : '' ?>>
                    <?= htmlspecialchars($notary['last_name'] . ' ' . $notary['first_name']) ?>
                    (<?= htmlspecialchars($notary['office_name']) ?>)
                </option>
            <?php endforeach; ?>
        </select>
        <?php if (!empty($errors['notary_id'])): ?>
            <div class="error"><?= htmlspecialchars($errors['notary_id']) ?></div>
        <?php endif; ?>
    </div>

    <div class="form-row">
        <label for="service_id">Послуга *</label>
        <select id="service_id" name="service_id" required>
            <option value="">Оберіть послугу</option>
            <?php foreach ($services as $service): ?>
                <option value="<?= $service['service_id'] ?>" <?= ($data['service_id'] ?? '') == $service['service_id'] ? 'selected' : '' ?>>
                    <?= htmlspecialchars($service['name']) ?> 
                    (<?= number_format($service['base_price'], 2) ?> ₴)
                </option>
            <?php endforeach; ?>
        </select>
        <?php if (!empty($errors['service_id'])): ?>
            <div class="error"><?= htmlspecialchars($errors['service_id']) ?></div>
        <?php endif; ?>
    </div>

    <div class="form-row">
        <label for="open_date">Дата відкриття *</label>
        <input type="date" id="open_date" name="open_date" 
               value="<?= htmlspecialchars($data['open_date'] ?? date('Y-m-d')) ?>" required>
        <?php if (!empty($errors['open_date'])): ?>
            <div class="error"><?= htmlspecialchars($errors['open_date']) ?></div>
        <?php endif; ?>
    </div>

    <div class="form-row">
        <label for="notes">Примітки</label>
        <textarea id="notes" name="notes"><?= htmlspecialchars($data['notes'] ?? '') ?></textarea>
    </div>

    <div class="form-actions">
        <button type="submit" class="btn btn-primary">Створити справу</button>
        <a href="<?= BASE_URL ?>/cases" class="btn btn-secondary">Скасувати</a>
    </div>
</form>
