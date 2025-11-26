<?php
session_start();
require_once 'DBController.php';
require_once 'helpers.php';

$error = "";

// permitem accesul direct cu ?product_id= pentru a seta produsul dorit înainte de cont
if (isset($_GET['product_id'])) {
    $_SESSION['pending_cart_product_id'] = (int)$_GET['product_id'];
    $_SESSION['redirect_after_login'] = 'cart.php';
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = $_POST['username'];
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);

    $db = new DBController();
    $query = "INSERT INTO users (username, password) VALUES (?, ?)";

    try {
        $db->updateDB($query, [$username, $password]);
        $member_id = (int)$db->getConnection()->lastInsertId();

        // după înregistrare autentificăm automat utilizatorul
        $_SESSION['user_id'] = $member_id;
        $_SESSION['member_id'] = $member_id;

        $redirect = 'home.php';

        if (isset($_SESSION['pending_cart_product_id'])) {
            // produsul selectat anterior este adăugat imediat în coș
            $product_id = (int)$_SESSION['pending_cart_product_id'];

            try {
                $db->updateDB(
                    "INSERT INTO tbl_cart (product_id, quantity, id_member) VALUES (?, ?, ?)",
                    [$product_id, 1, $member_id]
                );
            } catch (Exception $e) {
            }

            unset($_SESSION['pending_cart_product_id']);
            $redirect = 'cart.php';
        }

        if (isset($_SESSION['redirect_after_login'])) {
            $redirect = $_SESSION['redirect_after_login'];
            unset($_SESSION['redirect_after_login']);
        }

        redirectTo($redirect);
    } catch (Exception $e) {
        $error = "Eroare: " . $e->getMessage();
    }
}
?>

<form method="post">
    Username: <input type="text" name="username" required><br>
    Password: <input type="password" name="password" required><br>
    <button type="submit">Register</button>
</form>

<?php
if (!empty($error)) {
    echo "<p style='color:red;'>" . htmlspecialchars($error) . "</p>";
}
?>
