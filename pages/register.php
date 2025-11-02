<h2>Regisztráció</h2>
<p>Regisztrálj, hogy menthesd és visszanézhesd a meccseidet!</p>

<?php
$message = '';
$message_class = 'error'; // Alapból hiba

// Ellenőrizzük, hogy elküldték-e az űrlapot
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = $_POST['username'] ?? '';
    $email = $_POST['email'] ?? '';
    $password = $_POST['password'] ?? '';

    if (!empty($username) && !empty($email) && !empty($password)) {
        // A $pdo változó az index.php-ból érkezik
        $result = registerUser($pdo, $username, $email, $password);
        
        if ($result === "siker") {
            $message = "Sikeres regisztráció! Most már bejelentkezhetsz.";
            $message_class = 'success';
        } else {
            $message = $result; // Hibaüzenet kiírása
        }
    } else {
        $message = "Minden mező kitöltése kötelező.";
    }
}
?>

<?php if ($message): ?>
    <p class="<?php echo $message_class; ?>"><?php echo htmlspecialchars($message); ?></p>
<?php endif; ?>

<form action="index.php?page=register" method="POST">
    <div>
        <label for="username">Felhasználónév:</label>
        <input type="text" id="username" name="username" required>
    </div>
    <div>
        <label for="email">E-mail cím:</label>
        <input type="email" id="email" name="email" required>
    </div>
    <div>
        <label for="password">Jelszó:</label>
        <input type="password" id="password" name="password" required>
    </div>
    <div>
        <input type="submit" value="Regisztráció">
    </div>
</form>
