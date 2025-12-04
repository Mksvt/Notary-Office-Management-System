<div class="page-header">
    <h2><?= isset($isEdit) ? 'Редагування нотаріуса' : 'Новий нотаріус' ?></h2>
</div>

<form method="post" action="<?= isset($isEdit) ? BASE_URL . '/notaries/edit/' . $data['notary_id'] : BASE_URL . '/notaries/create' ?>">
    <input type="hidden" name="csrf_token" value="<?= $csrfToken ?>">
    
    <div class="form-row">
        <label for="last_name">Прізвище *</label>
        <input type="text" id="last_name" name="last_name" 
               value="<?= htmlspecialchars($data['last_name'] ?? '') ?>" required>
    </div>

    <div class="form-row">
        <label for="first_name">Ім'я *</label>
        <input type="text" id="first_name" name="first_name" 
               value="<?= htmlspecialchars($data['first_name'] ?? '') ?>" required>
    </div>

    <div class="form-row">
        <label for="middle_name">По батькові</label>
        <input type="text" id="middle_name" name="middle_name" 
               value="<?= htmlspecialchars($data['middle_name'] ?? '') ?>">
    </div>

    <div class="form-row">
        <label for="license_number">Номер ліцензії *</label>
        <input type="text" id="license_number" name="license_number" 
               value="<?= htmlspecialchars($data['license_number'] ?? '') ?>" required>
        <?php if (!empty($errors['license_number'])): ?>
            <div class="error"><?= htmlspecialchars($errors['license_number']) ?></div>
        <?php endif; ?>
    </div>

    <div class="form-row">
        <label for="license_issue_date">Дата видачі ліцензії</label>
        <input type="date" id="license_issue_date" name="license_issue_date" 
               value="<?= htmlspecialchars($data['license_issue_date'] ?? '') ?>">
    </div>

    <div class="form-row">
        <label for="office_id">Офіс *</label>
        <select id="office_id" name="office_id" required>
            <option value="">Оберіть офіс</option>
            <?php foreach ($offices as $office): ?>
                <option value="<?= $office['office_id'] ?>" <?= ($data['office_id'] ?? '') == $office['office_id'] ? 'selected' : '' ?>>
                    <?= htmlspecialchars($office['name']) ?>
                </option>
            <?php endforeach; ?>
        </select>
        <?php if (!empty($errors['office_id'])): ?>
            <div class="error"><?= htmlspecialchars($errors['office_id']) ?></div>
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
    </div>

    <div class="form-row">
        <label for="hired_at">Дата прийняття на роботу</label>
        <input type="date" id="hired_at" name="hired_at" 
               value="<?= htmlspecialchars($data['hired_at'] ?? '') ?>">
    </div>

    <div class="form-row">
        <label>
            <input type="checkbox" name="is_active" <?= isset($data['is_active']) && $data['is_active'] ? 'checked' : '' ?>>
            Активний
        </label>
    </div>

    <div class="form-actions">
        <button type="submit" class="btn btn-primary">Зберегти</button>
        <a href="<?= BASE_URL ?>/notaries" class="btn btn-secondary">Скасувати</a>
    </div>
</form>
