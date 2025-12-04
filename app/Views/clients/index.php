<div class="page-header">
    <h2>Клієнти</h2>
    <a href="<?= BASE_URL ?>/clients/create" class="btn btn-primary" style="float: right;">Новий клієнт</a>
</div>

<div class="search-bar">
    <form method="get" action="<?= BASE_URL ?>/clients">
        <input type="text" name="search" placeholder="Пошук за ПІБ, ІПН, телефоном..." 
               value="<?= htmlspecialchars($search ?? '') ?>">
        <button type="submit" class="btn btn-primary">Шукати</button>
    </form>
</div>

<?php if (empty($clients)): ?>
    <p>Клієнти не знайдені</p>
<?php else: ?>
    <table class="data-table">
        <thead>
            <tr>
                <th>ПІБ</th>
                <th>ІПН</th>
                <th>Телефон</th>
                <th>Email</th>
                <th>Дата реєстрації</th>
                <th>Дії</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($clients as $client): ?>
                <tr>
                    <td>
                        <a href="<?= BASE_URL ?>/clients/view/<?= $client['client_id'] ?>">
                            <?= htmlspecialchars($client['last_name'] . ' ' . $client['first_name']) ?>
                            <?php if ($client['middle_name']): ?>
                                <?= htmlspecialchars($client['middle_name']) ?>
                            <?php endif; ?>
                        </a>
                    </td>
                    <td><?= htmlspecialchars($client['tax_id'] ?? '-') ?></td>
                    <td><?= htmlspecialchars($client['phone'] ?? '-') ?></td>
                    <td><?= htmlspecialchars($client['email'] ?? '-') ?></td>
                    <td><?= date('d.m.Y', strtotime($client['created_at'])) ?></td>
                    <td class="table-actions">
                        <a href="<?= BASE_URL ?>/clients/edit/<?= $client['client_id'] ?>" 
                           class="btn btn-secondary btn-small">Редагувати</a>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
<?php endif; ?>
