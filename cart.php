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
?>
<!DOCTYPE html>
<html lang="ro">
<head>
    <meta charset="UTF-8">
    <title>Coș de cumpărături</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 2rem;
        }
        .cart-item {
            padding: 0.7rem 0;
            border-bottom: 1px solid #e0e0e0;
        }
        .cart-actions form {
            display: inline-block;
            margin-left: 1rem;
        }
        .links p {
            margin: 0.3rem 0;
        }
    </style>
</head>
<body>
    <h1>Coș de cumpărături</h1>

    <?php if (empty($cart_items)): ?>
        <p>Coșul este gol.</p>
    <?php else: ?>
        <?php foreach ($cart_items as $item): ?>
            <div class="cart-item">
                <strong><?php echo htmlspecialchars($item['name']); ?></strong>
                - <?php echo number_format((float)$item['price'], 2); ?> lei
                <span class="cart-actions">
                    <form method="post" action="updateCart.php">
                        <input type="number" name="quantity" value="<?php echo (int)$item['quantity']; ?>" min="1" />
                        <input type="hidden" name="cart_id" value="<?php echo (int)$item['id']; ?>" />
                        <input type="submit" value="Actualizează" />
                    </form>
                    <a href="removeFromCart.php?cart_id=<?php echo (int)$item['id']; ?>">Elimină</a>
                </span>
            </div>
        <?php endforeach; ?>
    <?php endif; ?>

    <div class="links">
        <p><a href="emptyCart.php">Golește coșul</a></p>
        <p><a href="categoryIndex.php">Înapoi la categorii</a></p>
        <p><a href="index.php">Înapoi la produse</a></p>
        <p><a href="logout.php">Logout</a></p>
    </div>
</body>
</html>
