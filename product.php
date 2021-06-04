<?php
// Memeriksa untuk memastikan parameter id ditentukan dalam URL
if (isset($_GET['id'])) {
  // Menjalankan dan mencegah injeksi SQL
  $stmt = $pdo->prepare('CALL tampilkan_produk(?)');
  $stmt->execute([$_GET['id']]);
  // Mengambil produk dari database dan mengembalikan hasil sebagai Array
  $product = $stmt->fetch(PDO::FETCH_ASSOC);
  // Memeriksa apakah produk ada (array tidak kosong)
  if (!$product) {
    // Jika ada kesalahan akan ditampilkan jika id untuk produk tidak ada (array kosong)
    exit('Product does not exist!');
  }
} else {
  // Jika ada kesalahan akan  ditampilkan jika id tidak ditentukan
  exit('Product does not exist!');
}
?>
<?= template_header($product['name']) ?>

<div class="product content-wrapper">
  <img src="imgs/<?= $product['img'] ?>" width="500" height="500" alt="<?= $product['name'] ?>">
  <div>
    <h1 class="name"><?= $product['name'] ?></h1>
    <span class="price">
      &dollar;<?= $product['price'] ?>
      <?php if ($product['rrp'] > 0) : ?>
        <span class="rrp">&dollar;<?= $product['rrp'] ?></span>
      <?php endif; ?>
    </span>
    <form action="index.php?page=cart" method="post">
      <input type="number" name="quantity" value="1" min="1" max="<?= $product['quantity'] ?>" placeholder="Quantity" required>
      <input type="hidden" name="product_id" value="<?= $product['id'] ?>">
      <input type="submit" value="Add To Cart">
    </form>
    <div class="description">
      <?= $product['desc'] ?>
    </div>
  </div>
</div>

<?= template_footer() ?>
