<?php

// We need to use sessions, so you should always start sessions using the below code.
session_start();
// If the user is not logged in redirect to the login page...
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
// Check if POST data is not empty
if (!empty($_POST)) {
    // Post data not empty insert a new record
    // Set-up the variables that are going to be inserted, we must check if the POST variables exist if not we can default them to blank
    $id = isset($_POST['id']) && !empty($_POST['id']) && $_POST['id'] != 'auto' ? $_POST['id'] : NULL;
    // Check if POST variable "name" exists, if not default the value to blank, basically the same for all variables
    $name = isset($_POST['name']) ? $_POST['name'] : '';
    $desc = isset($_POST['desc']) ? $_POST['desc'] : '';
    $price = isset($_POST['price']) ? $_POST['price'] : '';
    $rrp = isset($_POST['rrp']) ? $_POST['rrp'] : '';
    $quantity = isset($_POST['quantity']) ? $_POST['quantity'] : '';
    $img = isset($_POST['img']) ? $_POST['img'] : '';
    $dataadded = isset($_POST['data_added']) ? $_POST['data_added'] : date('Y-m-d H:i:s');
    // Insert new record into the contacts table
    $stmt = $pdo->prepare('CALL insert_new_product(?, ?, ?, ?, ?, ?, ?, ?)');

    // Output message
    $msg = 'Product Added Successfully!';

    try {
        $stmt->execute([$id, $name, $desc, $price, $rrp, $quantity, $img, $dataadded]);
    } catch (\Throwable $e) {
        $msg = 'Something went wrong, Please try again!';
    }
}
?>

<?= admin_template_header('Add New Product') ?>

<div class="content update">
    <h2>Add New Product</h2>
    <?php if ($msg == "Product Added Successfully!") : ?>
        <div class="alert success">
            <span class="closebtn">&times;</span>
            <p><?= $msg ?></p>
        </div>
    <?php endif; ?>

    <?php if ($msg == "Something went wrong, Please try again!") : ?>
        <div class="alert danger">
            <span class="closebtn">&times;</span>
            <p><?= $msg ?></p>
        </div>
    <?php endif; ?>

    <form action="create.php" method="post">
        <label for="id">ID</label>
        <label for="name">Name</label>

        <input type="number" name="id" placeholder="ID Product" id="id" required>
        <input type="text" name="name" placeholder="Product name" id="name" required>

        <label for="desc">Description</label>
        <label for="img">Image</label>

        <input type="text" name="desc" placeholder="A brief description about the product" id="desc">
        <input type="file" name="img" id="img" required>

        <label for="price">Price</label>
        <label for="rrp">RRP</label>

        <input type="number" name="price" id="price" required>
        <input type="number" name="rrp" id="rrp">

        <label for="quantity">Quantity</label>
        <label for="data">Data Added</label>

        <input type="number" name="quantity" id="quantity" required>
        <input type="datetime-local" name="data_added" id="data_added" required>
        <input type="submit" value="ADD">
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