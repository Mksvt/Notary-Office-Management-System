<div class="page-header">
    <h2><?= isset($isEdit) ? 'Редагування клієнта' : 'Новий клієнт' ?></h2>
</div>

<?php if (!empty($errors['general'])): ?>
    <div class="alert alert-error">
        <?= htmlspecialchars($errors['general']) ?>
    </div>
<?php endif; ?>

<form method="post" action="<?= isset($isEdit) ? BASE_URL . '/clients/edit/' . $data['client_id'] : BASE_URL . '/clients/create' ?>">
    <input type="hidden" name="csrf_token" value="<?= $csrfToken ?>">
    
    <div class="form-row">
        <label for="last_name">Прізвище *</label>
        <input type="text" id="last_name" name="last_name" 
               value="<?= htmlspecialchars($data['last_name'] ?? '') ?>" required>
        <?php if (!empty($errors['last_name'])): ?>
            <div class="error"><?= htmlspecialchars($errors['last_name']) ?></div>
        <?php endif; ?>
    </div>

    <div class="form-row">
        <label for="first_name">Ім'я *</label>
        <input type="text" id="first_name" name="first_name" 
               value="<?= htmlspecialchars($data['first_name'] ?? '') ?>" required>
        <?php if (!empty($errors['first_name'])): ?>
            <div class="error"><?= htmlspecialchars($errors['first_name']) ?></div>
        <?php endif; ?>
    </div>

    <div class="form-row">
        <label for="middle_name">По батькові</label>
        <input type="text" id="middle_name" name="middle_name" 
               value="<?= htmlspecialchars($data['middle_name'] ?? '') ?>">
    </div>

    <div class="form-row">
        <label for="birth_date">Дата народження</label>
        <input type="date" id="birth_date" name="birth_date" 
               value="<?= htmlspecialchars($data['birth_date'] ?? '') ?>">
        <?php if (!empty($errors['birth_date'])): ?>
            <div class="error"><?= htmlspecialchars($errors['birth_date']) ?></div>
        <?php endif; ?>
    </div>

    <div class="form-row">
        <label for="passport_series">Серія паспорта</label>
        <input type="text" id="passport_series" name="passport_series" 
               value="<?= htmlspecialchars($data['passport_series'] ?? '') ?>">
    </div>

    <div class="form-row">
        <label for="passport_number">Номер паспорта</label>
        <input type="text" id="passport_number" name="passport_number" 
               value="<?= htmlspecialchars($data['passport_number'] ?? '') ?>">
        <?php if (!empty($errors['passport'])): ?>
            <div class="error"><?= htmlspecialchars($errors['passport']) ?></div>
        <?php endif; ?>
    </div>

    <div class="form-row">
        <label for="tax_id">ІПН (10 цифр)</label>
        <input type="text" id="tax_id" name="tax_id" 
               value="<?= htmlspecialchars($data['tax_id'] ?? '') ?>" maxlength="10">
        <?php if (!empty($errors['tax_id'])): ?>
            <div class="error"><?= htmlspecialchars($errors['tax_id']) ?></div>
        <?php endif; ?>
    </div>

    <div class="form-row">
        <label for="phone">Телефон</label>
        <input type="text" id="phone" name="phone" 
               value="<?= htmlspecialchars($data['phone'] ?? '') ?>">
    </div>

    <div class="form-row">
        <label for="email">Email</label>
        <input type="email" id="email" name="email" 
               value="<?= htmlspecialchars($data['email'] ?? '') ?>">
        <?php if (!empty($errors['email'])): ?>
            <div class="error"><?= htmlspecialchars($errors['email']) ?></div>
        <?php endif; ?>
    </div>

    <div class="form-row">
        <label for="address">Адреса</label>
        <textarea id="address" name="address"><?= htmlspecialchars($data['address'] ?? '') ?></textarea>
    </div>

    <div class="form-actions">
        <button type="submit" class="btn btn-primary">Зберегти</button>
        <a href="<?= BASE_URL ?>/clients" class="btn btn-secondary">Скасувати</a>
    </div>
</form>
