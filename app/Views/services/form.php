<div class="page-header">
    <h2><?= isset($isEdit) ? 'Редагування послуги' : 'Нова послуга' ?></h2>
</div>

<form method="post" action="<?= isset($isEdit) ? BASE_URL . '/services/edit/' . $data['service_id'] : BASE_URL . '/services/create' ?>">
    <input type="hidden" name="csrf_token" value="<?= $csrfToken ?>">
    
    <div class="form-row">
        <label for="name">Назва послуги *</label>
        <input type="text" id="name" name="name" 
               value="<?= htmlspecialchars($data['name'] ?? '') ?>" required>
    </div>

    <div class="form-row">
        <label for="description">Опис</label>
        <textarea id="description" name="description"><?= htmlspecialchars($data['description'] ?? '') ?></textarea>
    </div>

    <div class="form-row">
        <label for="base_price">Базова ціна (₴) *</label>
        <input type="number" id="base_price" name="base_price" step="0.01" min="0"
               value="<?= htmlspecialchars($data['base_price'] ?? '0.00') ?>" required>
    </div>

    <div class="form-row">
        <label>
            <input type="checkbox" name="is_active" <?= isset($data['is_active']) && $data['is_active'] ? 'checked' : 'checked' ?>>
            Активна
        </label>
    </div>

    <div class="form-actions">
        <button type="submit" class="btn btn-primary">Зберегти</button>
        <a href="<?= BASE_URL ?>/services" class="btn btn-secondary">Скасувати</a>
    </div>
</form>
