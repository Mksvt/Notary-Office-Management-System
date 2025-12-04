<div class="page-header">
    <h2>Завантаження документа</h2>
</div>

<form method="post" action="<?= BASE_URL ?>/cases/<?= $case['case_id'] ?>/documents/upload" enctype="multipart/form-data">
    <input type="hidden" name="csrf_token" value="<?= $csrfToken ?>">
    
    <div class="form-row">
        <label for="doc_type">Тип документа *</label>
        <input type="text" id="doc_type" name="doc_type" 
               value="<?= htmlspecialchars($data['doc_type'] ?? '') ?>" 
               placeholder="Наприклад: Паспорт, Довідка, Договір" required>
        <?php if (!empty($errors['doc_type'])): ?>
            <div class="error"><?= htmlspecialchars($errors['doc_type']) ?></div>
        <?php endif; ?>
    </div>

    <div class="form-row">
        <label for="doc_number">Номер документа</label>
        <input type="text" id="doc_number" name="doc_number" 
               value="<?= htmlspecialchars($data['doc_number'] ?? '') ?>">
    </div>

    <div class="form-row">
        <label for="issue_date">Дата видачі</label>
        <input type="date" id="issue_date" name="issue_date" 
               value="<?= htmlspecialchars($data['issue_date'] ?? '') ?>">
    </div>

    <div class="form-row">
        <label for="expiry_date">Дата закінчення дії</label>
        <input type="date" id="expiry_date" name="expiry_date" 
               value="<?= htmlspecialchars($data['expiry_date'] ?? '') ?>">
    </div>

    <div class="form-row">
        <label>
            <input type="checkbox" name="is_original" checked>
            Оригінал документа
        </label>
    </div>

    <div class="form-row">
        <label for="document">Файл документа *</label>
        <input type="file" id="document" name="document" required>
        <small>Дозволені формати: PDF, JPG, PNG, DOC, DOCX. Максимальний розмір: 5 MB</small>
        <?php if (!empty($errors['file'])): ?>
            <div class="error"><?= htmlspecialchars($errors['file']) ?></div>
        <?php endif; ?>
    </div>

    <div class="form-actions">
        <button type="submit" class="btn btn-primary">Завантажити</button>
        <a href="<?= BASE_URL ?>/cases/view/<?= $case['case_id'] ?>" class="btn btn-secondary">Скасувати</a>
    </div>
</form>
