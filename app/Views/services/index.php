<div class="page-header">
    <h2>Послуги</h2>
    <?php if ($_SESSION['user_role'] === 'admin'): ?>
        <a href="<?= BASE_URL ?>/services/create" class="btn btn-primary" style="float: right;">Нова послуга</a>
    <?php endif; ?>
</div>

<table class="data-table">
    <thead>
        <tr>
            <th>Назва</th>
            <th>Опис</th>
            <th>Базова ціна</th>
            <th>Статус</th>
            <?php if ($_SESSION['user_role'] === 'admin'): ?>
                <th>Дії</th>
            <?php endif; ?>
        </tr>
    </thead>
    <tbody>
        <?php foreach ($services as $service): ?>
            <tr>
                <td><?= htmlspecialchars($service['name']) ?></td>
                <td><?= htmlspecialchars(mb_substr($service['description'] ?? '', 0, 50)) ?><?= mb_strlen($service['description'] ?? '') > 50 ? '...' : '' ?></td>
                <td><?= number_format($service['base_price'], 2) ?> ₴</td>
                <td>
                    <span class="status-badge <?= $service['is_active'] ? 'status-paid' : 'status-cancelled' ?>">
                        <?= $service['is_active'] ? 'Активна' : 'Неактивна' ?>
                    </span>
                </td>
                <?php if ($_SESSION['user_role'] === 'admin'): ?>
                    <td class="table-actions">
                        <a href="<?= BASE_URL ?>/services/edit/<?= $service['service_id'] ?>" 
                           class="btn btn-secondary btn-small">Редагувати</a>
                    </td>
                <?php endif; ?>
            </tr>
        <?php endforeach; ?>
    </tbody>
</table>
