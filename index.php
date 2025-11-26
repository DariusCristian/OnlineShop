<?php
session_start();
require_once 'DBController.php';

$db = new DBController();

// extragem produsele pentru pagina principală a magazinului
$products = $db->getDBResult("SELECT * FROM tbl_product");

echo "<h1>Produse disponibile</h1>";
echo "<ul>";
foreach ($products as $product) {
    $link = "addToCart.php?product_id=" . $product['id'];
    echo "<li>"
        . htmlspecialchars($product['name'])
        . " - "
        . number_format($product['price'], 2)
        . " lei "
        . "<a href='" . $link . "'>Adaugă în coș</a>"
        . "</li>";
}
echo "</ul>";

// mesajul de sub listă se schimbă în funcție de starea de autentificare
if (!isset($_SESSION['member_id'])) {
    echo "<p><a href='login.php'>Autentifică-te pentru a folosi coșul</a></p>";
} else {
    echo "<p><a href='cart.php'>Vezi coșul</a></p>";
}

?>
<a href="logout.php">Logout</a>
