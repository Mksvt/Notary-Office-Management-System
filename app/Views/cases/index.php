<div class="page-header">
    <h2>Справи</h2>
    <a href="<?= BASE_URL ?>/cases/create" class="btn btn-primary" style="float: right;">Нова справа</a>
</div>

<div class="search-bar">
    <form method="get" action="<?= BASE_URL ?>/cases">
        <select name="status">
            <option value="">Всі статуси</option>
            <option value="open" <?= ($statusFilter ?? '') === 'open' ? 'selected' : '' ?>>Відкрита</option>
            <option value="in_progress" <?= ($statusFilter ?? '') === 'in_progress' ? 'selected' : '' ?>>В роботі</option>
            <option value="closed" <?= ($statusFilter ?? '') === 'closed' ? 'selected' : '' ?>>Закрита</option>
            <option value="cancelled" <?= ($statusFilter ?? '') === 'cancelled' ? 'selected' : '' ?>>Скасована</option>
        </select>
        <button type="submit" class="btn btn-primary">Фільтрувати</button>
    </form>
</div>

<?php if (empty($cases)): ?>
    <p>Справи не знайдені</p>
<?php else: ?>
    <table class="data-table">
        <thead>
            <tr>
                <th>Номер справи</th>
                <th>Клієнт</th>
                <th>Послуга</th>
                <th>Нотаріус</th>
                <th>Дата відкриття</th>
                <th>Статус</th>
                <th>Дії</th>
            </tr>
        </thead>
        <tbody>
            <?php 
            $caseModel = new NotarialCase();
            foreach ($cases as $case): 
            ?>
                <tr>
                    <td>
                        <a href="<?= BASE_URL ?>/cases/view/<?= $case['case_id'] ?>">
                            <?= htmlspecialchars($case['case_number']) ?>
                        </a>
                    </td>
                    <td><?= htmlspecialchars($case['client_name']) ?></td>
                    <td><?= htmlspecialchars($case['service_name']) ?></td>
                    <td><?= htmlspecialchars($case['notary_name']) ?></td>
                    <td><?= date('d.m.Y', strtotime($case['open_date'])) ?></td>
                    <td>
                        <span class="status-badge status-<?= $case['status'] ?>">
                            <?= htmlspecialchars($caseModel->getStatusLabel($case['status'])) ?>
                        </span>
                    </td>
                    <td class="table-actions">
                        <a href="<?= BASE_URL ?>/cases/view/<?= $case['case_id'] ?>" 
                           class="btn btn-secondary btn-small">Переглянути</a>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
<?php endif; ?>
