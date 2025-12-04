<div class="page-header">
    <h2><?= htmlspecialchars($client['last_name'] . ' ' . $client['first_name'] . ' ' . ($client['middle_name'] ?? '')) ?></h2>
    <a href="<?= BASE_URL ?>/clients/edit/<?= $client['client_id'] ?>" 
       class="btn btn-primary" style="float: right;">Редагувати</a>
</div>

<div class="card">
    <div class="card-header">Основна інформація</div>
    <div class="card-body">
        <div class="info-row">
            <div class="info-label">ПІБ:</div>
            <div class="info-value">
                <?= htmlspecialchars($client['last_name'] . ' ' . $client['first_name'] . ' ' . ($client['middle_name'] ?? '')) ?>
            </div>
        </div>
        <?php if ($client['birth_date']): ?>
            <div class="info-row">
                <div class="info-label">Дата народження:</div>
                <div class="info-value"><?= date('d.m.Y', strtotime($client['birth_date'])) ?></div>
            </div>
        <?php endif; ?>
        <?php if ($client['passport_series'] && $client['passport_number']): ?>
            <div class="info-row">
                <div class="info-label">Паспорт:</div>
                <div class="info-value">
                    <?= htmlspecialchars($client['passport_series'] . ' ' . $client['passport_number']) ?>
                </div>
            </div>
        <?php endif; ?>
        <?php if ($client['tax_id']): ?>
            <div class="info-row">
                <div class="info-label">ІПН:</div>
                <div class="info-value"><?= htmlspecialchars($client['tax_id']) ?></div>
            </div>
        <?php endif; ?>
        <?php if ($client['phone']): ?>
            <div class="info-row">
                <div class="info-label">Телефон:</div>
                <div class="info-value"><?= htmlspecialchars($client['phone']) ?></div>
            </div>
        <?php endif; ?>
        <?php if ($client['email']): ?>
            <div class="info-row">
                <div class="info-label">Email:</div>
                <div class="info-value"><?= htmlspecialchars($client['email']) ?></div>
            </div>
        <?php endif; ?>
        <?php if ($client['address']): ?>
            <div class="info-row">
                <div class="info-label">Адреса:</div>
                <div class="info-value"><?= htmlspecialchars($client['address']) ?></div>
            </div>
        <?php endif; ?>
        <div class="info-row">
            <div class="info-label">Дата реєстрації:</div>
            <div class="info-value"><?= date('d.m.Y H:i', strtotime($client['created_at'])) ?></div>
        </div>
    </div>
</div>

<div class="card">
    <div class="card-header">Справи клієнта</div>
    <div class="card-body">
        <?php if (empty($cases)): ?>
            <p>Справи відсутні</p>
        <?php else: ?>
            <table class="data-table">
                <thead>
                    <tr>
                        <th>Номер справи</th>
                        <th>Послуга</th>
                        <th>Дата відкриття</th>
                        <th>Статус</th>
                        <th>Дії</th>
                    </tr>
                </thead>
                <tbody>
                    <?php 
                    $caseModel = new NotarialCase();
                    $serviceModel = new Service();
                    foreach ($cases as $case): 
                        $service = $serviceModel->findById($case['service_id']);
                    ?>
                        <tr>
                            <td><?= htmlspecialchars($case['case_number']) ?></td>
                            <td><?= htmlspecialchars($service['name'] ?? '-') ?></td>
                            <td><?= date('d.m.Y', strtotime($case['open_date'])) ?></td>
                            <td>
                                <span class="status-badge status-<?= $case['status'] ?>">
                                    <?= htmlspecialchars($caseModel->getStatusLabel($case['status'])) ?>
                                </span>
                            </td>
                            <td>
                                <a href="<?= BASE_URL ?>/cases/view/<?= $case['case_id'] ?>" 
                                   class="btn btn-secondary btn-small">Переглянути</a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php endif; ?>
    </div>
</div>
