<?php
// menampilkan jumlah produk di setiap halaman
$num_products_on_each_page = 8;
// Halaman di URL akan muncul sebagai index.php?page=products&p=1, index.php?page=products&p=2, dll...
$current_page = isset($_GET['p']) && is_numeric($_GET['p']) ? (int)$_GET['p'] : 1;
// Pilih produk yang dipesan berdasarkan tanggal ditambahkan
$stmt = $pdo->prepare('SELECT * FROM products ORDER BY date_added DESC LIMIT ?,?');
// bindValue untuk menggunakan integer dalam mySQL, kita perlu menggunakan untuk LIMIT
$stmt->bindValue(1, ($current_page - 1) * $num_products_on_each_page, PDO::PARAM_INT);
$stmt->bindValue(2, $num_products_on_each_page, PDO::PARAM_INT);
$stmt->execute();
// Jika mengambil dari database kemudian akan dikembalikan hasilnya menggunakan Array
$products = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>
<?= template_header('Products') ?>

<div class="products content-wrapper">
  <h1>Products</h1>
  <p><?= count_total_products() ?> Products</p>
  <div class="products-wrapper">
    <?php foreach ($products as $product) : ?>
      <a href="index.php?page=product&id=<?= $product['id'] ?>" class="product">
        <img src="imgs/<?= $product['img'] ?>" width="200" height="200" alt="<?= $product['name'] ?>">
        <span class="name"><?= $product['name'] ?></span>
        <span class="price">
          &dollar;<?= $product['price'] ?>
          <?php if ($product['rrp'] > 0) : ?>
            <span class="rrp">&dollar;<?= $product['rrp'] ?></span>
          <?php endif; ?>
        </span>
      </a>
    <?php endforeach; ?>
  </div>
  <div class="buttons">
    <?php if ($current_page > 1) : ?>
      <a href="index.php?page=products&p=<?= $current_page - 1 ?>">Prev</a>
    <?php endif; ?>
    <?php if (count_total_products() > ($current_page * $num_products_on_each_page) - $num_products_on_each_page + count($products)) : ?>
      <a href="index.php?page=products&p=<?= $current_page + 1 ?>">Next</a>
    <?php endif; ?>
  </div>
</div>

<?= template_footer() ?>
