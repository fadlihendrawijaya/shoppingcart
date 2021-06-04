<?php
// Perlu menggunakan sesi, jadi harus selalu memulai sesi menggunakan kode
session_start();
// Jika pengguna tidak login akan otomomatis ke halaman login
if (!isset($_SESSION['loggedin'])) {
    header('Location: ../login/.');
    exit;
}
// untuk menampilkan lokasi customer 
if ($_SESSION['role'] == 'costumer') {
    header('Location: ../.');
    exit;
}

include '../functions.php';
// Koneksi ke database
$pdo = pdo_connect_mysql();
// menampikan halaman menggunakan perintah GET
$page = isset($_GET['page']) && is_numeric($_GET['page']) ? (int)$_GET['page'] : 1;
// menampilkan jumlah di setiap halaman
$records_per_page = 5;

// database dari produk akan menampilkan di halaman
$stmt = $pdo->prepare('SELECT * FROM products ORDER BY id LIMIT :current_page, :record_per_page');
$stmt->bindValue(':current_page', ($page - 1) * $records_per_page, PDO::PARAM_INT);
$stmt->bindValue(':record_per_page', $records_per_page, PDO::PARAM_INT);
$stmt->execute();
// mengambil data sehingga ditampilkan di tamplate
$products = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Untuk mendapatkan jumlah total produk, agar dapat menentukan apakah harus ada tombol berikutnya dan sebelumnya
$num_products = $pdo->query('SELECT COUNT(*) FROM products')->fetchColumn();
?>

<?= admin_template_header('Administration | Gadgets Pedia') ?>

<div class="content read">
    <h2 style="text-transform: capitalize;">Welcome, <?= $_SESSION['name'] ?>!</h2>
    <p><?= count_total_products() ?> products currently on sale</p>
    <table>
        <thead>
            <tr>
                <td>ID</td>
                <td>Name</td>
                <td>Price</td>
                <td>rrp</td>
                <td>Quantity</td>
                <td>Image</td>
                <td>Date added</td>
                <td>Actions</td>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($products as $product) : ?>
                <tr>
                    <td><?= $product['id'] ?></td>
                    <td><?= $product['name'] ?></td>
                    <td><?= $product['price'] ?></td>
                    <td><?= $product['rrp'] ?></td>
                    <td><?= $product['quantity'] ?></td>
                    <td>
                        <img src="../imgs/<?= $product['img'] ?>" width="50" height="50" alt="<?= $product['name'] ?>">
                    </td>
                    <td><?= $product['date_added'] ?></td>
                    <td class="actions">
                        <a href="update.php?id=<?= $product['id'] ?>" class="edit"><i class="fas fa-pen fa-xs"></i></a>
                        <a href="delete.php?id=<?= $product['id'] ?>" class="trash"><i class="fas fa-trash fa-xs"></i></a>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
    <div class="pagination">
        <?php if ($page > 1) : ?>
            <a href="index.php?page=<?= $page - 1 ?>"><i class="fas fa-angle-double-left fa-sm"></i></a>
        <?php endif; ?>
        <?php if ($page * $records_per_page < $num_products) : ?>
            <a href="index.php?page=<?= $page + 1 ?>"><i class="fas fa-angle-double-right fa-sm"></i></a>
        <?php endif; ?>
    </div>
</div>

<?= template_footer() ?>
