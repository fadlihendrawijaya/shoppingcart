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
// Memeriksa apakah id produk ada, update.php?id=1 akan mendapatkan kontak dengan id 1
if (isset($_GET['id'])) {
    if (!empty($_POST)) {
        // Bagian ini mirip dengan create.php, tetapi memperbarui data dan tidak menyisipkan
        $id = isset($_POST['id']) ? $_POST['id'] : NULL;
        $name = isset($_POST['name']) ? $_POST['name'] : '';
        $desc = isset($_POST['desc']) ? $_POST['desc'] : '';
        $price = isset($_POST['price']) ? $_POST['price'] : '';
        $rrp = isset($_POST['rrp']) ? $_POST['rrp'] : '';
        $quantity = isset($_POST['quantity']) ? $_POST['quantity'] : '';
        $img = isset($_POST['img']) ? $_POST['img'] : '';
        $dataadded = isset($_POST['data_added']) ? $_POST['data_added'] : date('Y-m-d H:i:s');
        // Menjalankan update
        $stmt = $pdo->prepare('CALL update_product(?,?,?,?,?,?,?,?)');

        $msg = 'Updated Successfully!';

        try {
            $stmt->execute([$_GET['id'], $name, $desc, $price, $rrp, $quantity, $img, $dataadded]);
        } catch (\Throwable $e) {
            $msg = "Unable to update product, please try again!";
        }
    }
    // Mendapatlan data dari tabel produk
    $stmt = $pdo->prepare('SELECT * FROM products WHERE id = ?');
    $stmt->execute([$_GET['id']]);
    $products = $stmt->fetch(PDO::FETCH_ASSOC);
    if (!$products) {
        exit('Contact doesn\'t exist with that ID!');
    }
} else {
    exit('No ID specified!');
}
?>

<?= admin_template_header('Update Product') ?>

<div class="content update">
    <h2>Update Product "<?= $products['name'] ?>"</h2>
    <?php if ($msg == "Updated Successfully!") : ?>
        <div class="alert success">
            <span class="closebtn">&times;</span>
            <p><?= $msg ?></p>
        </div>
    <?php endif; ?>

    <?php if ($msg == "Unable to update product, please try again!") : ?>
        <div class="alert danger">
            <span class="closebtn">&times;</span>
            <p><?= $msg ?></p>
        </div>
    <?php endif; ?>

    <form action="update.php?id=<?= $products['id'] ?>" method="post">
        <label for="id">ID</label>
        <label for="name">Name</label>

        <input type="number" name="id" value="<?= $products['id'] ?>" id="id" title="ID Product cannot be update" readonly>
        <input type="text" name="name" value="<?= $products['name'] ?>" id="name">

        <label for="desc">Description</label>
        <label for="img">Image</label>

        <input type="text" name="desc" value="<?= $products['desc'] ?>" id="desc">
        <input type="file" name="img" value="<?= $products['img'] ?>" id="img">

        <label for="price">Price</label>
        <label for="rrp">RRP</label>

        <input type="number" name="price" value="<?= $products['price'] ?>" id="price">
        <input type="number" name="rrp" value="<?= $products['rrp'] ?>" id="rrp">

        <label for="quantity">Quantity</label>
        <label for="data">Date Added</label>

        <input type="number" name="quantity" value="<?= $products['quantity'] ?>" id="quantity">
        <input type="datetime-local" name="data_added" value="<?= date('Y-m-d\TH:i', strtotime($products['date_added'])) ?>" id="data">
        <input type="submit" value="Update">
    </form>
</div>

<script>
    var close = document.getElementsByClassName("closebtn");
    var i;

    for (i = 0; i < close.length; i++) {
        close[i].onclick = function() {
            var div = this.parentElement;
            div.style.opacity = "0";
            setTimeout(function() {
                div.style.display = "none";
            }, 600);
        }
    }
</script>

<?= template_footer() ?>
