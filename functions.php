<?php
function pdo_connect_mysql()
{
  // Update the details below with your MySQL details
  $DATABASE_HOST = 'localhost';
  $DATABASE_USER = 'root';
  $DATABASE_PASS = '';
  $DATABASE_NAME = 'shoppingcart';
  try {
    return new PDO('mysql:host=' . $DATABASE_HOST . ';dbname=' . $DATABASE_NAME . ';charset=utf8', $DATABASE_USER, $DATABASE_PASS);
  } catch (PDOException $exception) {
    // If there is an error with the connection, stop the script and display the error.
    exit('Failed to connect to database!');
  }
}
// Template header, this will showing to costumer
function template_header($title)
{
  // Get the amount of items in the shopping cart
  $num_items_in_cart = isset($_SESSION['cart']) ? count($_SESSION['cart']) : 0;
  echo <<<EOT
<!DOCTYPE html>
<html>
	<head>
		<meta charset="utf-8">
		<title>$title</title>
		<link href="style.css" rel="stylesheet" type="text/css">
		<link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.7.1/css/all.css">
    <link rel="apple-touch-icon" sizes="180x180" href="imgs/icon/apple-touch-icon.png">
    <link rel="icon" type="image/png" sizes="32x32" href="imgs/icon/favicon-32x32.png">
    <link rel="icon" type="image/png" sizes="16x16" href="imgs/icon/favicon-16x16.png">
    <link rel="manifest" href="imgs/icon/site.webmanifest">
	</head>
	<body>
        <header>
            <div class="content-wrapper">
                <h1><a href=".">Gadgets Pedia</a></h1>
                <nav>
                  <!-- <form action="index.php?page=products" method="GET">
                    <input type="text" name="q" placeholder="Search products...">
                    <input type="submit" value="search">
                  </form> -->
                </nav>
                <div class="link-icons">
                  <a href="index.php?page=products">Products</a>
                  <a href="register/.">Register</a>
                  <a href="login/." class="btn-login">Login</a>
                  <a href="index.php?page=cart">
                    <i class="fas fa-shopping-cart"></i>
                    <span>$num_items_in_cart</span>
                  </a>
                </div>
            </div>
        </header>
        <main>
EOT;
}
// Template footer
function template_footer()
{
  $year = date('Y');
  echo <<<EOT
        </main>
        <footer>
            <div class="content-wrapper">
                <p>&copy; $year, Gadgets Pedia</p>
            </div>
        </footer>
        <script src="script.js"></script>
    </body>
</html>
EOT;
}

//Admin template header
function admin_template_header($title)
{
  echo <<<EOT
<!DOCTYPE html>
<html>
	<head>
		<meta charset="utf-8">
		<title>$title</title>
		<link href="../style.css" rel="stylesheet" type="text/css">
    <link href="style.css" rel="stylesheet" type="text/css">
		<link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.7.1/css/all.css">
    <link rel="apple-touch-icon" sizes="180x180" href="../imgs/admin/apple-touch-icon.png">
    <link rel="icon" type="image/png" sizes="32x32" href="../imgs/admin/favicon-32x32.png">
    <link rel="icon" type="image/png" sizes="16x16" href="../imgs/admin/favicon-16x16.png">
    <link rel="manifest" href="../imgs/admin/site.webmanifest">
	</head>
	<body>
        <header>
            <div class="content-wrapper">
                <h1>Admin</h1>
                <nav>
                    <a href=".">Home</a>
                    <a href="create.php">Add Product</a>
                    <a href="orders.php">Orders</a>
                </nav>
                <div class="link-icons">
                  <a href="../login/logout.php" onclick="return confirm('Are you sure to exit?')"><i class="fas fa-sign-out-alt"></i> Logout</a>
                </div>
            </div>
        </header>
        <main>
EOT;
}

// this function return total products in database
function count_total_products()
{
  $pdo = pdo_connect_mysql();
  $stmt = $pdo->prepare('SELECT count_total_product()');
  $stmt->execute();
  $total_products = $stmt->fetchAll(PDO::FETCH_ASSOC);
  foreach ($total_products as $key) {
    return $key['count_total_product()'];
  }
}
