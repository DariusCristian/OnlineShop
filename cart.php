<?php
session_start();
require_once 'DBController.php';
require_once 'helpers.php';

if (!isset($_SESSION['member_id'])) {
    // ne asigurăm că doar userii logați văd coșul
    redirectTo('login.php');
}

$db = new DBController();
$member_id = $_SESSION['member_id'];

// citim toate produsele din coșul utilizatorului curent
$cart_items = $db->getDBResult(
    "SELECT p.name, p.price, c.quantity, c.id, c.product_id
     FROM tbl_cart c
     JOIN tbl_product p ON c.product_id = p.id
     WHERE c.id_member = ?",
    [$member_id]
);

echo "<h1>Coș de cumpărături</h1>";

if (empty($cart_items)) {
    echo "<p>Coșul este gol.</p>";
} else {
    foreach ($cart_items as $item) {
        echo "<div>";
        echo htmlspecialchars($item['name']) . " - "
            . number_format($item['price'], 2) . " lei";

        echo "
            <form method='post' action='updateCart.php' style='display:inline-block; margin-left:10px;'>
                <input type='number' name='quantity' value='" . (int)$item['quantity'] . "' min='1' />
                <input type='hidden' name='cart_id' value='" . (int)$item['id'] . "' />
                <input type='submit' value='Actualizeaza' />
            </form>
            <a href='removeFromCart.php?cart_id=" . (int)$item['id'] . "' style='margin-left:10px;'>Elimina</a>
        ";

        echo "</div>";
    }
}

echo "<p><a href='emptyCart.php'>Golește coșul</a></p>";
echo "<p><a href='index.php'>Înapoi la produse</a></p>";
echo "<p><a href='logout.php'>Logout</a></p>";

?>
