<div class="auth-container">
    <div class="auth-box">
        <h2>Вхід до системи</h2>
        
        <?php if (!empty($errors['general'])): ?>
            <div class="alert alert-error">
                <?= htmlspecialchars($errors['general']) ?>
            </div>
        <?php endif; ?>
        
        <form method="post" action="<?= BASE_URL ?>/login">
            <div class="form-row">
                <label for="username">Логін *</label>
                <input type="text" id="username" name="username" 
                       value="<?= htmlspecialchars($data['username'] ?? '') ?>" required>
                <?php if (!empty($errors['username'])): ?>
                    <div class="error"><?= htmlspecialchars($errors['username']) ?></div>
                <?php endif; ?>
            </div>

            <div class="form-row">
                <label for="password">Пароль *</label>
                <input type="password" id="password" name="password" required>
                <?php if (!empty($errors['password'])): ?>
                    <div class="error"><?= htmlspecialchars($errors['password']) ?></div>
                <?php endif; ?>
            </div>

            <div class="form-row">
                <button type="submit" class="btn btn-primary" style="width: 100%;">Увійти</button>
            </div>
        </form>
        
        <hr style="margin: 30px 0;">
        
        <div style="text-align: center;">
            <p style="margin-bottom: 15px; color: #666;">
                Ви клієнт нотаріальної контори?
            </p>
            <a href="<?= BASE_URL ?>/portal/check-status" class="btn btn-secondary" style="width: 48%; display: inline-block;">
                Перевірити статус справи
            </a>
            <a href="<?= BASE_URL ?>/portal/application" class="btn btn-success" style="width: 48%; display: inline-block; margin-left: 2%;">
                Подати заяву
            </a>
        </div>
        
        <p style="text-align: center; margin-top: 20px; font-size: 12px; color: #666;">
            Тестові дані для співробітників:<br>
            <strong>admin / admin123</strong> (Адміністратор)
        </p>
    </div>
</div>
