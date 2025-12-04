<div class="auth-container">
    <div class="auth-box" style="text-align: center;">
        <h2 style="color: #28a745;">✓ Справу створено!</h2>
        
        <div style="margin: 30px 0; padding: 20px; background: #d4edda; border-radius: 8px; color: #155724;">
            <p style="margin: 0; font-size: 18px; font-weight: bold;">
                Номер вашої справи: <?= htmlspecialchars($generatedCaseNumber ?? '') ?>
            </p>
            <p style="margin: 10px 0 0 0; font-size: 14px;">
                Збережіть цей номер для перевірки статусу
            </p>
        </div>
        
        <div style="margin: 30px 0; text-align: left; background: #f8f9fa; padding: 20px; border-radius: 8px;">
            <h3 style="margin-top: 0;">Що далі?</h3>
            <ol style="margin: 10px 0 0 20px; line-height: 1.8;">
                <li>Справа створена та очікує на обробку</li>
                <li>Нотаріус розгляне вашу заяву</li>
                <li>З вами зв'яжуться для уточнення деталей та призначення зустрічі</li>
                <li>Ви можете перевірити статус справи за номером та ІПН</li>
            </ol>
        </div>
        
        <div style="margin-top: 40px;">
            <a href="<?= BASE_URL ?>/portal/application" class="btn btn-secondary" style="width: 45%; display: inline-block;">
                Подати ще одну заяву
            </a>
            <a href="<?= BASE_URL ?>/portal/check-status" class="btn btn-primary" style="width: 45%; display: inline-block; margin-left: 5%;">
                Перевірити статус справи
            </a>
        </div>
        
        <hr style="margin: 30px 0;">
        
        <p style="color: #666; font-size: 14px;">
            Якщо у вас виникнуть запитання, зв'яжіться з нами за телефоном нотаріальної контори
        </p>
    </div>
</div>
