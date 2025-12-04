<div class="auth-container" style="align-items: flex-start; padding: 20px 0;">
    <div class="auth-box" style="max-width: 700px;">
        <h2>Подати заяву</h2>
        
        <p style="text-align: center; margin-bottom: 20px; color: #666;">
            Заповніть форму нижче, і наш працівник зв'яжеться з вами найближчим часом
        </p>
        
        <?php if (!empty($errors['general'])): ?>
            <div class="alert alert-error">
                <?= htmlspecialchars($errors['general']) ?>
            </div>
        <?php endif; ?>
        
        <form method="post" action="<?= BASE_URL ?>/portal/application">
            <h3 style="margin-top: 20px;">Особисті дані</h3>
            
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
                <label for="tax_id">ІПН (10 цифр)</label>
                <input type="text" id="tax_id" name="tax_id" 
                       value="<?= htmlspecialchars($data['tax_id'] ?? '') ?>" 
                       maxlength="10" placeholder="1234567890">
                <?php if (!empty($errors['tax_id'])): ?>
                    <div class="error"><?= htmlspecialchars($errors['tax_id']) ?></div>
                <?php endif; ?>
            </div>

            <h3 style="margin-top: 30px;">Контактні дані</h3>

            <div class="form-row">
                <label for="phone">Телефон *</label>
                <input type="text" id="phone" name="phone" 
                       value="<?= htmlspecialchars($data['phone'] ?? '') ?>" 
                       placeholder="+380XXXXXXXXX" required>
                <?php if (!empty($errors['phone'])): ?>
                    <div class="error"><?= htmlspecialchars($errors['phone']) ?></div>
                <?php endif; ?>
            </div>

            <div class="form-row">
                <label for="email">Email</label>
                <input type="email" id="email" name="email" 
                       value="<?= htmlspecialchars($data['email'] ?? '') ?>" 
                       placeholder="your@email.com">
                <?php if (!empty($errors['email'])): ?>
                    <div class="error"><?= htmlspecialchars($errors['email']) ?></div>
                <?php endif; ?>
            </div>

            <h3 style="margin-top: 30px;">Деталі заяви</h3>

            <div class="form-row">
                <label for="service_id">Послуга *</label>
                <select id="service_id" name="service_id" required>
                    <option value="">Оберіть послугу</option>
                    <?php foreach ($services as $service): ?>
                        <option value="<?= $service['service_id'] ?>" 
                                <?= ($data['service_id'] ?? '') == $service['service_id'] ? 'selected' : '' ?>>
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
                <label for="message">Додаткова інформація або запитання</label>
                <textarea id="message" name="message" rows="4"
                          placeholder="Опишіть деталі вашого запиту..."><?= htmlspecialchars($data['message'] ?? '') ?></textarea>
            </div>

            <div class="form-row">
                <button type="submit" class="btn btn-primary" style="width: 100%;">Подати заяву</button>
            </div>
        </form>
        
        <hr style="margin: 30px 0;">
        
        <div style="text-align: center;">
            <a href="<?= BASE_URL ?>/portal/check-status">Перевірити статус справи</a>
            <span style="margin: 0 10px;">|</span>
            <a href="<?= BASE_URL ?>/login">Вхід для співробітників</a>
        </div>
    </div>
</div>
