<?php
// Jika pengguna mengklik tombol tambahkan di halaman produk, dapat memeriksa data formulir
if (isset($_POST['product_id'], $_POST['quantity']) && is_numeric($_POST['product_id']) && is_numeric($_POST['quantity'])) {
  // Mengatur variabel post agar mudah mengidentifikasinya, pastikan bilangan bulat
  $product_id = (int)$_POST['product_id'];
  $quantity = (int)$_POST['quantity'];
  // Untuk memeriksa apakah produk ada di database
  $stmt = $pdo->prepare('SELECT * FROM products WHERE id = ?');
  $stmt->execute([$_POST['product_id']]);
  // Mengambil data produk dari database untuk mengembalikan hasilnya menggunakan Array
  $product = $stmt->fetch(PDO::FETCH_ASSOC);
  // Memeriksa apakah produk ada (array tidak kosong)
  if ($product && $quantity > 0) {
    // Jika produk ada di database, lalu dapat membuat/memperbarui variabel
    if (isset($_SESSION['cart']) && is_array($_SESSION['cart'])) {
      if (array_key_exists($product_id, $_SESSION['cart'])) {
        // Jika produk ada di keranjang lalu memperbarui jumlahnya
        $_SESSION['cart'][$product_id] += $quantity;
      } else {
        // Jika produk tidak ada, untuk menambahkannya
        $_SESSION['cart'][$product_id] = $quantity;
      }
    } else {
      // Jika tidak ada produk, kemudian menambahkannya produk pertama
      $_SESSION['cart'] = array($product_id => $quantity);
    }
  }
  // Untuk mencegah pengiriman ulang formulir...
  header('location: index.php?page=cart');
  exit;
}
// Jika menghapus produk dari troli, memeriksa URL "hapus", id produk, pastikan juga nomornya apakah ada di troli
if (isset($_GET['remove']) && is_numeric($_GET['remove']) && isset($_SESSION['cart']) && isset($_SESSION['cart'][$_GET['remove']])) {
  // Menghapus produk dari shopping cart
  unset($_SESSION['cart'][$_GET['remove']]);
}
// Memperbarui jumlah produk jika pengguna mengklik tombol "Perbarui" di halaman belanja
if (isset($_POST['update']) && isset($_SESSION['cart'])) {
  // Jika mengulangi data maka dapat memperbarui jumlah untuk setiap produk
  foreach ($_POST as $k => $v) {
    if (strpos($k, 'quantity') !== false && is_numeric($v)) {
      $id = str_replace('quantity-', '', $k);
      $quantity = (int)$v;
      // Untuk melakukan pengecekan dan validasi
      if (is_numeric($id) && isset($_SESSION['cart'][$id]) && $quantity > 0) {
        // Memperbarui jumlah
        $_SESSION['cart'][$id] = $quantity;
      }
    }
  }
  // Untuk mencengah pengiriman ulang formulir...
  header('location: index.php?page=cart');
  exit;
}
// Untuk pengguna menuju ke halaman pemesanan jika mengklik tombol Tempatkan Pesanan, tidak boleh kosong
if (isset($_POST['placeorder']) && isset($_SESSION['cart']) && !empty($_SESSION['cart'])) {
  $stmt = $pdo->prepare('INSERT INTO `orders` (`productID`, `accountsID`, `date_created`, `jumlah`, `total`) VALUES (?,?,?,?,?)');

  if (empty($_SESSION['id'])) {
    header('Location: register/index.php');
    exit;
  }
  $productID = $_POST['id'];
  $accountsID = $_SESSION['id'];
  $current_date_time = date("Y-m-d H:i:s");
  $jumlah = $_POST['jumlah'];
  $total = $_POST['total'];

  try {
    $stmt->execute([$productID, $accountsID, $current_date_time, $jumlah, $total]);
  } catch (\Throwable $e) {
    echo "Someting went wrong happen <br>";
    echo $e->getMessage();
    exit;
  }

  header('Location: index.php?page=placeorder');
  exit;
}
// Periksa variabel produk
$products_in_cart = isset($_SESSION['cart']) ? $_SESSION['cart'] : array();
$products = array();
$subtotal = 0.00;
// Jika ada produk di keranjang
if ($products_in_cart) {
  // Jika ada produk di troli, lalu perlu memilih produk tersebut dari database
  // Jika produk dalam array, string, lalu memerlukan sql untuk menambahkannya
  $array_to_question_marks = implode(',', array_fill(0, count($products_in_cart), '?'));
  $stmt = $pdo->prepare('SELECT * FROM products WHERE id IN (' . $array_to_question_marks . ')');
  // Hanya membutuhkan kunci array, id produk
  $stmt->execute(array_keys($products_in_cart));
  // Untuk mengambil data dari database, mengembalikan hasilnya sebagai Array
  $products = $stmt->fetchAll(PDO::FETCH_ASSOC);
  // Menghitung jumlah 
  foreach ($products as $product) {
    $subtotal += (float)$product['price'] * (int)$products_in_cart[$product['id']];
  }
}
?>
<?= template_header('Cart') ?>

<div class="cart content-wrapper">
  <h1>Shopping Cart</h1>
  <form action="index.php?page=cart" method="post">
    <table>
      <thead>
        <tr>
          <td colspan="2">Product</td>
          <td>Price</td>
          <td>Quantity</td>
          <td>Total</td>
        </tr>
      </thead>
      <tbody>
        <?php if (empty($products)) : ?>
          <tr>
            <td colspan="5" style="text-align:center;">You have no products added in your Shopping Cart</td>
          </tr>
        <?php else : ?>
          <?php foreach ($products as $product) : ?>
            <tr>
              <td class="img">
                <a href="index.php?page=product&id=<?= $product['id'] ?>">
                  <img src="imgs/<?= $product['img'] ?>" width="50" height="50" alt="<?= $product['name'] ?>">
                </a>
              </td>
              <td>
                <a href="index.php?page=product&id=<?= $product['id'] ?>"><?= $product['name'] ?></a>
                <br>
                <a href="index.php?page=cart&remove=<?= $product['id'] ?>" class="remove">Remove</a>
              </td>
              <td class="price">&dollar;<?= $product['price'] ?></td>
              <td class="quantity">
                <input type="number" name="quantity-<?= $product['id'] ?>" value="<?= $products_in_cart[$product['id']] ?>" min="1" max="<?= $product['quantity'] ?>" placeholder="Quantity" required>
              </td>
              <td class="price">&dollar;<?= $product['price'] * $products_in_cart[$product['id']] ?></td>
            </tr>
            <input type="hidden" name="id" value="<?= $product['id'] ?>">
            <input type="hidden" name="jumlah" value="<?= $products_in_cart[$product['id']] ?>">
            <input type="hidden" name="total" value="<?= $subtotal ?>">
          <?php endforeach; ?>
        <?php endif; ?>
      </tbody>
    </table>
    <div class="subtotal">
      <span class="text">Subtotal</span>
      <span class="price">&dollar;<?= $subtotal ?></span>
    </div>
    <div class="buttons">
      <input type="submit" value="Update" name="update">
      <input type="submit" value="Place Order" name="placeorder">
    </div>
  </form>
</div>

<?= template_footer() ?>
