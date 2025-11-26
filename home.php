<?php
session_start();
require_once 'helpers.php';

// pagina de bun venit este disponibilă doar după login
if (!isset($_SESSION['user_id'])) {
    redirectTo('login.php');
}
?>

Bine ai venit, utilizator #<?php echo htmlspecialchars($_SESSION['user_id']); ?>!<br>
<a href="index.php">Mergi la magazin (coș de cumpărături)</a><br>
<a href="logout.php">Logout</a>
