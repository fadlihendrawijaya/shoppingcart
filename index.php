<?php
session_start();
// fungsi untuk menghubungkan ke database menggunakan PDO mySQL
include 'functions.php';
$pdo = pdo_connect_mysql();
//  Halaman akan otomotis ke (home.php) secara default, jika mengunjungi halaman yang akan dilihat
$page = isset($_GET['page']) && file_exists($_GET['page'] . '.php') ? $_GET['page'] : 'home';
// Menampikan halaman yang diminta
include $page . '.php';
