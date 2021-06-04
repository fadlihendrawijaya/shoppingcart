<?php
// Perlu menggunakan sesi, jadi harus selalu memulai sesi menggunakan kode
session_start();
// Jika pengguna tidak login akan otomomatis ke halaman login
if (!isset($_SESSION['loggedin'])) {
    header('Location: ../login/.');
    exit;
}

if ($_SESSION['role'] == 'costumer') {
    header('Location: ../.');
    exit;
}

include '../functions.php';
$pdo = pdo_connect_mysql();
$msg = '';
// memeriksa apakah ID produk ada
if (isset($_GET['id'])) {
    // Select the record that is going to be deleted
    $stmt = $pdo->prepare('SELECT * FROM products WHERE id = ?');
    $stmt->execute([$_GET['id']]);
    $products = $stmt->fetch(PDO::FETCH_ASSOC);
    if (!$products) {
        exit('Products doesn\'t exist with that ID!');
    }
    // untuk memastikan pengguna jika menghapus dan mengkonfirmasikan
        if ($_GET['confirm'] == 'yes') {
            // jika klik yes maka akan menjalankan
            $stmt = $pdo->prepare('DELETE FROM products WHERE id = ?');
            $stmt->execute([$_GET['id']]);
            $msg = 'You have deleted the Products!';
        } else {
            // jika klik no maka tidak akan menjalankan dan kembali ke halaman sebelumnya
            header('Location: index.php');
            exit;
        }
    }
} else {
    exit('No ID specified!');
}
?>

<?= admin_template_header('Delete') ?>

<div class="content delete">
    <h2>Delete Products #<?= $products['id'] ?></h2>
    <?php if ($msg) : ?>
        <p><?= $msg ?></p>
    <?php else : ?>
        <p>Are you sure you want to delete products #<?= $products['id'] ?>?</p>
        <div class="yesno">
            <a href="delete.php?id=<?= $products['id'] ?>&confirm=yes" style="background-color: #f44336; color: #FFFFFF;">Yes</a>
            <a href="delete.php?id=<?= $products['id'] ?>&confirm=no">No</a>
        </div>
    <?php endif; ?>
</div>

<?= template_footer() ?>
