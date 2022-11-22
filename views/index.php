<?php include( __DIR__ . '/../sources/templates/header.php'); ?>
<?php if (isset($_SESSION['loggedIn']) == true) header('location: dashboard.php'); ?>

<h1>Chill & Play</h1>

<form method="POST" name="auth" action="/auth">
    <input type="submit" name="authUser" value="Login via Discord">
</form>