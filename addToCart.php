<?php
session_start();
require_once 'DBController.php';
require_once 'helpers.php';

// fără product_id nu avem ce adăuga, revenim la listă
if (!isset($_GET['product_id'])) {
    redirectTo('index.php');
}

$productId = (int)$_GET['product_id'];

if (!isset($_SESSION['member_id'])) {
    // salvăm produsul dorit și trimitem userul la login
    $_SESSION['pending_cart_product_id'] = $productId;
    $_SESSION['redirect_after_login'] = 'cart.php';
    redirectTo('login.php');
}

$db = new DBController();
$memberId = (int)$_SESSION['member_id'];

// utilizator logat -> inserăm direct produsul în coș
$db->updateDB(
    "INSERT INTO tbl_cart (product_id, quantity, id_member) VALUES (?, 1, ?)",
    [$productId, $memberId]
);

redirectTo('cart.php');
