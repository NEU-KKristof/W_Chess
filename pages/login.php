<h2>Bejelentkezés</h2>

<?php
$message = '';

// Ha már be van jelentkezve, átirányítjuk
if (isset($_SESSION['user'])) {
    header('Location: index.php?page=profile');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = $_POST['username'] ?? '';
    $password = $_POST['password'] ?? '';

    if (!empty($username) && !empty($password)) {
        // A $pdo változó az index.php-ból érkezik
        $result = loginUser($pdo, $username, $password);
        
        if (is_array($result)) {
            // Siker! Bejelentkeztetjük a felhasználót
            $_SESSION['user'] = $result;
            
            // Átirányítás a profil oldalra
            header('Location: index.php?page=profile');
            exit; // Fontos, hogy utána ne fusson tovább a kód
        } else {
            $message = $result; // Hibaüzenet (pl. "Hibás felhasználónév...")
        }
    } else {
        $message = "Minden mező kitöltése kötelező.";
    }
}
?>

<?php if ($message): ?>
    <p class="error"><?php echo htmlspecialchars($message); ?></p>
<?php endif; ?>

<form action="index.php?page=login" method="POST">
    <div>
        <label for="username">Felhasználónév:</label>
        <input type="text" id="username" name="username" required>
    </div>
    <div>
        <label for="password">Jelszó:</label>
        <input type="password" id="password" name="password" required>
    </div>
    <div>
        <input type="submit" value="Bejelentkezés">
    </div>
</form>
