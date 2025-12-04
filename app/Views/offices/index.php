<div class="page-header">
    <h2>Офіси</h2>
    <?php if ($_SESSION['user_role'] === 'admin'): ?>
        <a href="<?= BASE_URL ?>/offices/create" class="btn btn-primary" style="float: right;">Новий офіс</a>
    <?php endif; ?>
</div>

<table class="data-table">
    <thead>
        <tr>
            <th>Назва</th>
            <th>Місто</th>
            <th>Адреса</th>
            <th>Телефон</th>
            <?php if ($_SESSION['user_role'] === 'admin'): ?>
                <th>Дії</th>
            <?php endif; ?>
        </tr>
    </thead>
    <tbody>
        <?php foreach ($offices as $office): ?>
            <tr>
                <td><?= htmlspecialchars($office['name']) ?></td>
                <td><?= htmlspecialchars($office['city']) ?></td>
                <td><?= htmlspecialchars($office['address']) ?></td>
                <td><?= htmlspecialchars($office['phone'] ?? '-') ?></td>
                <?php if ($_SESSION['user_role'] === 'admin'): ?>
                    <td class="table-actions">
                        <a href="<?= BASE_URL ?>/offices/edit/<?= $office['office_id'] ?>" 
                           class="btn btn-secondary btn-small">Редагувати</a>
                    </td>
                <?php endif; ?>
            </tr>
        <?php endforeach; ?>
    </tbody>
</table>
