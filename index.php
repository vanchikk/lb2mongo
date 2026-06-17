<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

require_once 'db_mongo.php';

$result_data = [];
$query_type = "";
$options = ['projection' => ['_id' => 0]];

if (isset($_GET['action'])) {
    // 1. Перелік виробників, з якими працює магазин
    if ($_GET['action'] === 'vendors_list') {
        $query_type = "Перелік виробників, з якими працює магазин";
        // Метод distinct собирает только уникальные значения поля 'vendor'
        $raw_vendors = $collection->distinct('vendor');
        foreach ($raw_vendors as $v) {
            $result_data[] = ['vendor_name' => $v];
        }
    } 
    // 2. Товари, відсутні на складі (quantity = 0)
    elseif ($_GET['action'] === 'out_of_stock') {
        $query_type = "Товари, відсутні на складі (кількість = 0)";
        $result_data = $collection->find(['quantity' => 0], $options)->toArray();
    }
    // 3. Товари в обраному ціновому діапазоні
    elseif ($_GET['action'] === 'price_range') {
        $min = (int)$_GET['min_price'];
        $max = (int)$_GET['max_price'];
        $query_type = "Товари в ціновому діапазоні від $min до $max грн";
        
        // Используем NoSQL операторы сравнения $gte (>=) и $lte (<=)
        $result_data = $collection->find([
            'price' => [
                '$gte' => $min,
                '$lte' => $max
            ]
        ], $options)->toArray();
    }
}
?>

<!DOCTYPE html>
<html lang="uk">
<head>
    <meta charset="UTF-8">
    <title>Використання (MongoDB - Інтернет-магазин)</title>
    <style>
        body { font-family: sans-serif; background: #f0x2f5; background: #f0f2f5; padding: 20px; }
        .grid { display: flex; gap: 20px; margin-bottom: 20px; }
        .card { background: white; padding: 15px; border-radius: 8px; box-shadow: 0 2px 4px rgba(0,0,0,0.1); flex: 1; }
        table { width: 100%; border-collapse: collapse; margin-top: 20px; background: white; }
        th, td { padding: 12px; border: 1px solid #ddd; text-align: left; }
        th { background: #c9c9c9; color: white; }
        .history { background: #e9ecef; padding: 10px; margin-bottom: 20px; border-left: 4px solid #ffffff; }
        .tag { background: #eee; padding: 2px 6px; border-radius: 4px; margin-right: 5px; font-size: 0.8em; display: inline-block; margin-bottom: 2px; }
        input { padding: 5px; border: 1px solid #ccc; border-radius: 4px; }
        button { padding: 6px 12px; cursor: pointer; }
    </style>
</head>
<body>

<h1>Облік товарів інтернет-магазину (NoSQL)</h1>

<div class="grid">
    <div class="card">
        <h3>Бренди магазину</h3>
        <form method="GET">
            <input type="hidden" name="action" value="vendors_list">
            <button type="submit">Отримати перелік виробників</button>
        </form>
    </div>

    <div class="card">
        <h3>Дефіцитні позиції</h3>
        <form method="GET">
            <input type="hidden" name="action" value="out_of_stock">
            <button type="submit" style="background: #dc3545; color: white; border: none; padding: 8px 12px; border-radius: 4px; cursor: pointer;">
                Товари з залишком 0
            </button>
        </form>
    </div>

    <div class="card">
        <h3>Фільтр за ціною</h3>
        <form method="GET">
            <input type="hidden" name="action" value="price_range">
            <input type="number" name="min_price" value="0" placeholder="Мін" style="width: 70px;" required min="0">
            <input type="number" name="max_price" value="15000" placeholder="Макс" style="width: 70px;" required min="0">
            <button type="submit" style="background: #28a745; color: white; border: none; padding: 6px 12px; border-radius: 4px;">Пошук</button>
        </form>
    </div>
</div>

<div class="history">
    <strong>Останній запит:</strong> <span id="hist_val">немає даних</span>
</div>

<?php if (isset($_GET['action'])): ?>
    <h2>Результати: <?= htmlspecialchars($query_type) ?></h2>
    <?php if (empty($result_data)): ?>
        <p>Нічого не знайдено.</p>
    <?php else: ?>
        <table>
            <?php if ($_GET['action'] === 'vendors_list'): ?>
                <tr>
                    <th>Назва фірми-виробника</th>
                </tr>
                <?php foreach ($result_data as $row): ?>
                <tr>
                    <td><strong><?= htmlspecialchars($row['vendor_name']) ?></strong></td>
                </tr>
                <?php endforeach; ?>
            <?php else: ?>
                <tr>
                    <th>Назва товару</th>
                    <th>Ціна</th>
                    <th>Кількість</th>
                    <th>Категорія</th>
                    <th>Виробник</th>
                    <th>Стан</th>
                    <th>Відгуки</th>
                </tr>
                <?php foreach ($result_data as $row): ?>
                <tr>
                    <td><strong><?= htmlspecialchars($row['name'] ?? 'Без назви') ?></strong></td>
                    <td><?= $row['price'] ?? 0 ?> грн</td>
                    <td><?= $row['quantity'] ?? 0 ?> шт</td>
                    <td><?= htmlspecialchars($row['category'] ?? 'Інше') ?></td>
                    <td><?= htmlspecialchars($row['vendor'] ?? 'Не вказано') ?></td>
                    <td><?= (isset($row['condition']) && $row['condition'] === 'new') ? 'Новий' : 'Б/В' ?></td>
                    <td>
                        <?php if (isset($row['reviews']) && (is_array($row['reviews']) || is_object($row['reviews']))): ?>
                            <?php foreach ($row['reviews'] as $rev): ?>
                                <span class="tag">💬 <?= htmlspecialchars($rev) ?></span>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <span style="color: #aaa;">Немає відгуків</span>
                        <?php endif; ?>
                    </td>
                </tr>
                <?php endforeach; ?>
            <?php endif; ?>
        </table>
        <script>localStorage.setItem('mongo_history', '<?= addslashes($query_type) ?>');</script>
    <?php endif; ?>
<?php endif; ?>

<script>
    const hist = localStorage.getItem('mongo_history');
    if (hist) document.getElementById('hist_val').innerText = hist;
</script>

</body>
</html>