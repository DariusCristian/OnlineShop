<?php
session_start();
require_once 'DBController.php';
require_once 'helpers.php';

$error = "";

// dacă venim dintr-un link de tip ?product_id= salvăm intenția pentru după login
if (isset($_GET['product_id'])) {
    $_SESSION['pending_cart_product_id'] = (int)$_GET['product_id'];
    $_SESSION['redirect_after_login'] = 'cart.php';
}

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $username = $_POST['username'] ?? '';
    $password = $_POST['password'] ?? '';

    $db = new DBController();

    try {
        $user = $db->getDBResult(
            "SELECT id, password FROM users WHERE username = ?",
            [$username]
        );
    } catch (Exception $e) {
        $user = [];
    }

    if ($user && password_verify($password, $user[0]['password'])) {
        // login reușit -> punem id-ul în sesiune
        $memberId = (int)$user[0]['id'];
        $_SESSION['user_id'] = $memberId;
        $_SESSION['member_id'] = $memberId;

        $redirect = 'home.php';

        if (isset($_SESSION['pending_cart_product_id'])) {
            // dacă exista un produs selectat anterior îl adăugăm în coș acum
            $productId = (int)$_SESSION['pending_cart_product_id'];

            try {
                $db->updateDB(
                    "INSERT INTO tbl_cart (product_id, quantity, id_member) VALUES (?, 1, ?)",
                    [$productId, $memberId]
                );
            } catch (Exception $e) {
            }

            unset($_SESSION['pending_cart_product_id']);
            $redirect = 'cart.php';
        }

        if (isset($_SESSION['redirect_after_login'])) {
            // redirect custom păstrat din addToCart/register
            $redirect = $_SESSION['redirect_after_login'];
            unset($_SESSION['redirect_after_login']);
        }

        redirectTo($redirect);
    } else {
        $error = "Username sau parolă greșite.";
    }
}
?>

<form method="post">
    Username: <input type="text" name="username" required><br>
    Password: <input type="password" name="password" required><br>
    <button type="submit">Login</button>
</form>

<?php
if (!empty($error)) {
    echo "<p style='color:red;'>" . htmlspecialchars($error) . "</p>";
}
?>
<p><a href="register.php">Nu ai cont? Înregistrează-te</a></p>
