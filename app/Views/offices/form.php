<div class="page-header">
    <h2><?= isset($isEdit) ? 'Редагування офісу' : 'Новий офіс' ?></h2>
</div>

<form method="post" action="<?= isset($isEdit) ? BASE_URL . '/offices/edit/' . $data['office_id'] : BASE_URL . '/offices/create' ?>">
    <input type="hidden" name="csrf_token" value="<?= $csrfToken ?>">
    
    <div class="form-row">
        <label for="name">Назва офісу *</label>
        <input type="text" id="name" name="name" 
               value="<?= htmlspecialchars($data['name'] ?? '') ?>" required>
    </div>

    <div class="form-row">
        <label for="city">Місто *</label>
        <input type="text" id="city" name="city" 
               value="<?= htmlspecialchars($data['city'] ?? '') ?>" required>
    </div>

    <div class="form-row">
        <label for="address">Адреса *</label>
        <input type="text" id="address" name="address" 
               value="<?= htmlspecialchars($data['address'] ?? '') ?>" required>
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
        <label for="schedule">Графік роботи</label>
        <textarea id="schedule" name="schedule"><?= htmlspecialchars($data['schedule'] ?? '') ?></textarea>
    </div>

    <div class="form-actions">
        <button type="submit" class="btn btn-primary">Зберегти</button>
        <a href="<?= BASE_URL ?>/offices" class="btn btn-secondary">Скасувати</a>
    </div>
</form>
