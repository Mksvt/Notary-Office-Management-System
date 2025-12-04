<div class="page-header">
    <h2>Справа №<?= htmlspecialchars($case['case_number']) ?></h2>
</div>

<div class="card">
    <div class="card-header">
        Інформація про справу
        <?php if ($case['status'] !== 'closed' && $case['status'] !== 'cancelled'): ?>
            <div style="float: right;">
                <form method="post" action="<?= BASE_URL ?>/cases/status/<?= $case['case_id'] ?>" style="display: inline;">
                    <input type="hidden" name="csrf_token" value="<?= $csrfToken ?>">
                    <?php if ($case['status'] === 'open'): ?>
                        <button type="submit" name="status" value="in_progress" class="btn btn-success btn-small">
                            Взяти в роботу
                        </button>
                    <?php elseif ($case['status'] === 'in_progress'): ?>
                        <button type="submit" name="status" value="closed" class="btn btn-success btn-small">
                            Закрити справу
                        </button>
                    <?php endif; ?>
                    <button type="submit" name="status" value="cancelled" class="btn btn-danger btn-small">
                        Скасувати справу
                    </button>
                </form>
            </div>
        <?php endif; ?>
    </div>
    <div class="card-body">
        <div class="info-row">
            <div class="info-label">Номер справи:</div>
            <div class="info-value"><?= htmlspecialchars($case['case_number']) ?></div>
        </div>
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
                <a href="<?= BASE_URL ?>/clients/view/<?= $case['client_id'] ?>">
                    <?= htmlspecialchars($case['client_last_name'] . ' ' . $case['client_first_name'] . ' ' . ($case['client_middle_name'] ?? '')) ?>
                </a>
            </div>
        </div>
        <div class="info-row">
            <div class="info-label">ІПН клієнта:</div>
            <div class="info-value"><?= htmlspecialchars($case['client_tax_id'] ?? '-') ?></div>
        </div>
        <div class="info-row">
            <div class="info-label">Телефон клієнта:</div>
            <div class="info-value"><?= htmlspecialchars($case['client_phone'] ?? '-') ?></div>
        </div>
        <div class="info-row">
            <div class="info-label">Нотаріус:</div>
            <div class="info-value">
                <?= htmlspecialchars($case['notary_last_name'] . ' ' . $case['notary_first_name'] . ' ' . ($case['notary_middle_name'] ?? '')) ?>
            </div>
        </div>
        <div class="info-row">
            <div class="info-label">Офіс:</div>
            <div class="info-value"><?= htmlspecialchars($case['office_name']) ?></div>
        </div>
        <div class="info-row">
            <div class="info-label">Послуга:</div>
            <div class="info-value"><?= htmlspecialchars($case['service_name']) ?></div>
        </div>
        <div class="info-row">
            <div class="info-label">Базова вартість:</div>
            <div class="info-value"><?= number_format($case['base_price'], 2) ?> ₴</div>
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
        <?php if ($case['notes']): ?>
            <div class="info-row">
                <div class="info-label">Примітки:</div>
                <div class="info-value"><?= nl2br(htmlspecialchars($case['notes'])) ?></div>
            </div>
        <?php endif; ?>
    </div>
</div>

<div class="card">
    <div class="card-header">
        Документи
        <a href="<?= BASE_URL ?>/cases/<?= $case['case_id'] ?>/documents/upload" 
           class="btn btn-primary btn-small" style="float: right;">Завантажити документ</a>
    </div>
    <div class="card-body">
        <?php if (empty($documents)): ?>
            <p>Документи відсутні</p>
        <?php else: ?>
            <table class="data-table">
                <thead>
                    <tr>
                        <th>Тип документа</th>
                        <th>Номер</th>
                        <th>Дата видачі</th>
                        <th>Оригінал/Копія</th>
                        <th>Дії</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($documents as $doc): ?>
                        <tr>
                            <td><?= htmlspecialchars($doc['doc_type']) ?></td>
                            <td><?= htmlspecialchars($doc['doc_number'] ?? '-') ?></td>
                            <td><?= $doc['issue_date'] ? date('d.m.Y', strtotime($doc['issue_date'])) : '-' ?></td>
                            <td><?= $doc['is_original'] ? 'Оригінал' : 'Копія' ?></td>
                            <td>
                                <a href="<?= BASE_URL ?>/documents/download/<?= $doc['document_id'] ?>" 
                                   class="btn btn-secondary btn-small">Завантажити</a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php endif; ?>
    </div>
</div>

<div class="card">
    <div class="card-header">
        Платежі
        <a href="<?= BASE_URL ?>/cases/<?= $case['case_id'] ?>/payments/create" 
           class="btn btn-primary btn-small" style="float: right;">Додати платіж</a>
    </div>
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
                        <th>Номер квитанції</th>
                        <th>Статус</th>
                        <th>Дії</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($payments as $payment): ?>
                        <?php if ($payment['status'] === 'paid') $totalPaid += $payment['amount']; ?>
                        <tr>
                            <td><?= date('d.m.Y', strtotime($payment['payment_date'])) ?></td>
                            <td><?= number_format($payment['amount'], 2) ?> ₴</td>
                            <td><?= htmlspecialchars($paymentModel->getMethodLabel($payment['method'])) ?></td>
                            <td><?= htmlspecialchars($payment['receipt_number'] ?? '-') ?></td>
                            <td>
                                <span class="status-badge status-<?= $payment['status'] ?>">
                                    <?= htmlspecialchars($paymentModel->getStatusLabel($payment['status'])) ?>
                                </span>
                            </td>
                            <td>
                                <a href="<?= BASE_URL ?>/payments/receipt/<?= $payment['payment_id'] ?>" 
                                   class="btn btn-secondary btn-small" target="_blank">Квитанція</a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
                <tfoot>
                    <tr>
                        <td colspan="1"><strong>Всього оплачено:</strong></td>
                        <td colspan="5"><strong><?= number_format($totalPaid, 2) ?> ₴</strong></td>
                    </tr>
                </tfoot>
            </table>
        <?php endif; ?>
    </div>
</div>
