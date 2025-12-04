<div class="auth-container">
    <div class="auth-box">
        <h2>Перевірка статусу справи</h2>
        
        <p style="text-align: center; margin-bottom: 20px; color: #666;">
            Введіть номер справи та ваш ІПН для перевірки статусу
        </p>
        
        <?php if (!empty($errors['general'])): ?>
            <div class="alert alert-error">
                <?= htmlspecialchars($errors['general']) ?>
            </div>
        <?php endif; ?>
        
        <form method="post" action="<?= BASE_URL ?>/portal/check-status">
            <div class="form-row">
                <label for="case_number">Номер справи *</label>
                <input type="text" id="case_number" name="case_number" 
                       value="<?= htmlspecialchars($data['case_number'] ?? '') ?>" 
                       placeholder="Наприклад: 2025-000001" required>
                <?php if (!empty($errors['case_number'])): ?>
                    <div class="error"><?= htmlspecialchars($errors['case_number']) ?></div>
                <?php endif; ?>
            </div>

            <div class="form-row">
                <label for="tax_id">ІПН (10 цифр) *</label>
                <input type="text" id="tax_id" name="tax_id" 
                       value="<?= htmlspecialchars($data['tax_id'] ?? '') ?>" 
                       placeholder="1234567890" maxlength="10" required>
                <?php if (!empty($errors['tax_id'])): ?>
                    <div class="error"><?= htmlspecialchars($errors['tax_id']) ?></div>
                <?php endif; ?>
            </div>

            <div class="form-row">
                <button type="submit" class="btn btn-primary" style="width: 100%;">Перевірити статус</button>
            </div>
        </form>
        
        <hr style="margin: 30px 0;">
        
        <div style="text-align: center;">
            <p style="margin-bottom: 10px;">Хочете подати заяву на нотаріальну послугу?</p>
            <a href="<?= BASE_URL ?>/portal/application" class="btn btn-success" style="width: 100%;">
                Подати заяву онлайн
            </a>
        </div>
        
        <hr style="margin: 30px 0;">
        
        <div style="text-align: center;">
            <a href="<?= BASE_URL ?>/login">Вхід для співробітників</a>
        </div>
    </div>
</div>
