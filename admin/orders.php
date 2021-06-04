<?php
// Perlu menggunakan sesi, jadi harus selalu memulai sesi menggunakan kode
session_start();
// Jika pengguna tidak login akan otomomatis ke halaman login
if (!isset($_SESSION['loggedin'])) {
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

$stmt = $pdo->prepare('SELECT * FROM orderan_masuk');
$stmt->execute();

$orders = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<?= admin_template_header('Orderan masuk') ?>
<div class="content read">
  <h1>Orders</h1>
  <table>
    <thead>
      <tr>
        <td>Product Name</td>
        <td>Pemesan</td>
        <td>Jumlah</td>
        <td>Harga</td>
        <td>Total</td>
      </tr>
    </thead>
    <tbody>
      <?php if (empty($orders)) : ?>
        <tr>
          <td colspan="5" style="text-align:center;">You have no orders</td>
        </tr>
      <?php else : ?>
        <?php foreach ($orders as $order) : ?>
          <tr>
            <td><?= $order['name'] ?></td>
            <td><?= $order['username'] ?></td>
            <td><?= $order['jumlah'] ?></td>
            <td><?= $order['price'] ?></td>
            <td><?= $order['total'] ?></td>
          </tr>
        <?php endforeach; ?>
      <?php endif; ?>
    </tbody>
  </table>
</div>
<?= template_footer() ?>
