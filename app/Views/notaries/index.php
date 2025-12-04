<div class="page-header">
    <h2>Нотаріуси</h2>
    <?php if ($_SESSION['user_role'] === 'admin'): ?>
        <a href="<?= BASE_URL ?>/notaries/create" class="btn btn-primary" style="float: right;">Новий нотаріус</a>
    <?php endif; ?>
</div>

<table class="data-table">
    <thead>
        <tr>
            <th>ПІБ</th>
            <th>Номер ліцензії</th>
            <th>Офіс</th>
            <th>Телефон</th>
            <th>Статус</th>
            <?php if ($_SESSION['user_role'] === 'admin'): ?>
                <th>Дії</th>
            <?php endif; ?>
        </tr>
    </thead>
    <tbody>
        <?php foreach ($notaries as $notary): ?>
            <tr>
                <td><?= htmlspecialchars($notary['last_name'] . ' ' . $notary['first_name']) ?></td>
                <td><?= htmlspecialchars($notary['license_number']) ?></td>
                <td><?= htmlspecialchars($notary['office_name']) ?></td>
                <td><?= htmlspecialchars($notary['phone'] ?? '-') ?></td>
                <td>
                    <span class="status-badge <?= $notary['is_active'] ? 'status-paid' : 'status-cancelled' ?>">
                        <?= $notary['is_active'] ? 'Активний' : 'Неактивний' ?>
                    </span>
                </td>
                <?php if ($_SESSION['user_role'] === 'admin'): ?>
                    <td class="table-actions">
                        <a href="<?= BASE_URL ?>/notaries/edit/<?= $notary['notary_id'] ?>" 
                           class="btn btn-secondary btn-small">Редагувати</a>
                    </td>
                <?php endif; ?>
            </tr>
        <?php endforeach; ?>
    </tbody>
</table>
