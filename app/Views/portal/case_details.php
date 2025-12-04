<div class="auth-container" style="align-items: flex-start; padding: 20px 0;">
    <div class="auth-box" style="max-width: 900px;">
        <h2>Справа №<?= htmlspecialchars($case['case_number']) ?></h2>
        
        <div class="card" style="margin-bottom: 20px;">
            <div class="card-header">Статус справи</div>
            <div class="card-body">
                <div class="info-row">
                    <div class="info-label">Статус:</div>
                    <div class="info-value">
                        <?php 
                        $caseModel = new NotarialCase();
                        ?>
                        <span class="status-badge status-<?= $case['status'] ?>">
                            <?= htmlspecialchars($caseModel->getStatusLabel($case['status'])) ?>
                        </span>
                    </div>
                </div>
                <div class="info-row">
                    <div class="info-label">Клієнт:</div>
                    <div class="info-value">
                        <?= htmlspecialchars($case['client_last_name'] . ' ' . $case['client_first_name']) ?>
                    </div>
                </div>
                <div class="info-row">
                    <div class="info-label">Послуга:</div>
                    <div class="info-value"><?= htmlspecialchars($case['service_name']) ?></div>
                </div>
                <div class="info-row">
                    <div class="info-label">Нотаріус:</div>
                    <div class="info-value">
                        <?= htmlspecialchars($case['notary_last_name'] . ' ' . $case['notary_first_name']) ?>
                    </div>
                </div>
                <div class="info-row">
                    <div class="info-label">Офіс:</div>
                    <div class="info-value">
                        <?= htmlspecialchars($case['office_name']) ?><br>
                        <small><?= htmlspecialchars($case['office_address']) ?></small><br>
                        <small>Тел: <?= htmlspecialchars($case['office_phone']) ?></small>
                    </div>
                </div>
                <div class="info-row">
                    <div class="info-label">Дата відкриття:</div>
                    <div class="info-value"><?= date('d.m.Y', strtotime($case['open_date'])) ?></div>
                </div>
                <?php if ($case['close_date']): ?>
                    <div class="info-row">
                        <div class="info-label">Дата закриття:</div>
                        <div class="info-value"><?= date('d.m.Y', strtotime($case['close_date'])) ?></div>
                    </div>
                <?php endif; ?>
            </div>
        </div>
        
        <div class="card" style="margin-bottom: 20px;">
            <div class="card-header">Документи</div>
            <div class="card-body">
                <?php if (empty($documents)): ?>
                    <p>Документи ще не завантажені</p>
                <?php else: ?>
                    <table class="data-table">
                        <thead>
                            <tr>
                                <th>Тип документа</th>
                                <th>Номер</th>
                                <th>Дата видачі</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($documents as $doc): ?>
                                <tr>
                                    <td><?= htmlspecialchars($doc['doc_type']) ?></td>
                                    <td><?= htmlspecialchars($doc['doc_number'] ?? '-') ?></td>
                                    <td><?= $doc['issue_date'] ? date('d.m.Y', strtotime($doc['issue_date'])) : '-' ?></td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                <?php endif; ?>
            </div>
        </div>
        
        <div class="card" style="margin-bottom: 20px;">
            <div class="card-header">Платежі</div>
            <div class="card-body">
                <?php if (empty($payments)): ?>
                    <p>Платежі відсутні</p>
                <?php else: ?>
                    <?php 
                    $paymentModel = new Payment();
                    $totalPaid = 0;
                    ?>
                    <table class="data-table">
                        <thead>
                            <tr>
                                <th>Дата</th>
                                <th>Сума</th>
                                <th>Метод</th>
                                <th>Статус</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($payments as $payment): ?>
                                <?php if ($payment['status'] === 'paid') $totalPaid += $payment['amount']; ?>
                                <tr>
                                    <td><?= date('d.m.Y', strtotime($payment['payment_date'])) ?></td>
                                    <td><?= number_format($payment['amount'], 2) ?> ₴</td>
                                    <td><?= htmlspecialchars($paymentModel->getMethodLabel($payment['method'])) ?></td>
                                    <td>
                                        <span class="status-badge status-<?= $payment['status'] ?>">
                                            <?= htmlspecialchars($paymentModel->getStatusLabel($payment['status'])) ?>
                                        </span>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                        <tfoot>
                            <tr>
                                <td><strong>Всього оплачено:</strong></td>
                                <td colspan="3"><strong><?= number_format($totalPaid, 2) ?> ₴</strong></td>
                            </tr>
                        </tfoot>
                    </table>
                <?php endif; ?>
            </div>
        </div>
        
        <div style="text-align: center; margin-top: 30px;">
            <a href="<?= BASE_URL ?>/portal/check-status" class="btn btn-secondary">
                Перевірити іншу справу
            </a>
            <a href="<?= BASE_URL ?>/portal/application" class="btn btn-primary">
                Подати нову заяву
            </a>
        </div>
    </div>
</div>
