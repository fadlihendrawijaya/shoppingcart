<?php
session_start();
// Include functions and connect to the database using MySQLi
include 'functions.php';
$con = mysqli_connect_to_database();

// Now we check if the data from the login form was submitted, isset() will check if the data exists.
if (!isset($_POST['username'], $_POST['password'])) {
  // Could not get the data that should have been sent.
  create_alert_message('Please fill both the username and password fields!');
}
// Prepare our SQL, preparing the SQL statement will prevent SQL injection.
if ($stmt = $con->prepare('SELECT id, password, role FROM accounts WHERE username = ?')) {
  // Bind parameters (s = string, i = int, b = blob, etc), in our case the username is a string so we use "s"
  $stmt->bind_param('s', $_POST['username']);
  $stmt->execute();
  // Store the result so we can check if the account exists in the database.
  $stmt->store_result();

  if ($stmt->num_rows > 0) {
    $stmt->bind_result($id, $password, $role);
    $stmt->fetch();
    // Account exists, now we verify the password.
    // Note: remember to use password_hash in your registration file to store the hashed passwords.
    if (password_verify($_POST['password'], $password)) {
      // Verification success! User has logged-in!
      // Create sessions, so we know the user is logged in, they basically act like cookies but remember the data on the server.
      session_regenerate_id();
      $_SESSION['loggedin'] = TRUE;
      $_SESSION['name'] = $_POST['username'];
      $_SESSION['id'] = $id;
      $_SESSION['role'] = $role;

      if ($role == 'admin') {
        header('Location: ../admin/.');
      }

      if ($role == 'costumer') {
        header('Location: ../.');
      }
    } else {
      // Incorrect password
      create_alert_message('Incorrect password!');
    }
  } else {
    // Incorrect username
    create_alert_message('Incorrect username!');
  }

  $stmt->close();
}
