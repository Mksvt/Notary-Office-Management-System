<div class="page-header">
    <h2>Головна сторінка</h2>
</div>

<div class="stats-grid">
    <div class="stat-card">
        <h3><?= $stats['total_cases'] ?></h3>
        <p>Всього справ</p>
    </div>
    <div class="stat-card">
        <h3><?= $stats['open_cases'] ?></h3>
        <p>Відкритих справ</p>
    </div>
    <div class="stat-card">
        <h3><?= $stats['in_progress_cases'] ?></h3>
        <p>В роботі</p>
    </div>
    <div class="stat-card">
        <h3><?= $stats['closed_cases'] ?></h3>
        <p>Закритих справ</p>
    </div>
    <div class="stat-card">
        <h3><?= $stats['total_clients'] ?></h3>
        <p>Клієнтів</p>
    </div>
    <div class="stat-card">
        <h3><?= number_format($stats['total_paid'], 2) ?> ₴</h3>
        <p>Оплачено</p>
    </div>
</div>

<div class="card">
    <div class="card-header">
        Останні справи
        <a href="<?= BASE_URL ?>/cases/create" class="btn btn-primary btn-small" style="float: right;">Нова справа</a>
    </div>
    <div class="card-body">
        <?php if (empty($recentCases)): ?>
            <p>Справи відсутні</p>
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
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($recentCases as $case): ?>
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
                                    <?php
                                    $caseModel = new NotarialCase();
                                    echo htmlspecialchars($caseModel->getStatusLabel($case['status']));
                                    ?>
                                </span>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php endif; ?>
    </div>
</div>
