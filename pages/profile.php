<?php
// Ellenőrizzük, hogy be van-e lépve a felhasználó
if (!isset($_SESSION['user'])) {
    // Ha nincs, átirányítjuk a login oldalra
    header('Location: index.php?page=login');
    exit;
}

// Bejelentkezett felhasználó adatainak lekérése
$user = $_SESSION['user'];
$userId = $user['id'];

?>
<br>
<h2>Üdvözlünk, <?php echo htmlspecialchars($user['username']); ?>!</h2>
<p>Ez a te profil oldalad. Itt láthatod a korábbi meccseidet.</p>
<p>Regisztrált e-mail címed: <strong><?php echo htmlspecialchars($user['email']); ?></strong> </p>

<hr>
<center>
   <h2 style="background-color:gray; padding:10px; "><a href="index.php?page=chess" class="<?php echo ($page == 'chess') ? 'active' : ''; ?>"><button style="background-color:#C4B7B7; border-radius:10px"><strong>Sakk Játék</strong> </button></a></h2>
   <hr>

 
</center>
<br>
<br>
<h3>Előző meccsek</h3>

<?php
// Meccsek lekérdezése az adatbázisból
// A $pdo az index.php-ból jön, a getMatchesForUser pedig a database_handler.php-ból
$matches = getMatchesForUser($pdo, $userId);
?>

<?php if (empty($matches)): ?>
    <p>Még nincsenek mentett meccseid.</p>
<?php else: ?>
    <table style="width: 100%; border-collapse: collapse;">
        <thead style="background: #eee;">
            <tr>
                <th style="padding: 8px; border: 1px solid #ddd;">Dátum</th>
                <th style="padding: 8px; border: 1px solid #ddd;">Fehér</th>
                <th style="padding: 8px; border: 1px solid #ddd;">Fekete</th>
                <th style="padding: 8px; border: 1px solid #ddd;">Eredmény</th>
                <!-- <th style="padding: 8px; border: 1px solid #ddd;">Lépések (JSON)</th> -->
            </tr>
        </thead>
        <tbody>
            <?php foreach ($matches as $match): ?>
                <tr>
                    <td style="padding: 8px; border: 1px solid #ddd;"><?php echo $match['played_at']; ?></td>
                    <td style="padding: 8px; border: 1px solid #ddd;"><?php echo htmlspecialchars($match['white_username']); ?></td>
                    <td style="padding: 8px; border: 1px solid #ddd;"><?php echo htmlspecialchars($match['black_username']); ?></td>
                    <td style="padding: 8px; border: 1px solid #ddd;">
                        <?php
                        if ($match['winner_id'] === null) {
                            echo "Függőben";
                        } elseif ($match['winner_id'] == $match['user_id_white']) {
                            echo "Fehér nyert";
                        } elseif ($match['winner_id'] == $match['user_id_black']) {
                            echo "Fekete nyert";
                        } else {
                            // Feltételezve, hogy a 0 a döntetlen (ahogy a db handlerben terveztük)
                            echo "Döntetlen";
                        }
                        ?>
                    </td>
                    <!-- 
                    <td style="padding: 8px; border: 1px solid #ddd;">
                        <?php // echo htmlspecialchars(substr($match['match_data'], 0, 50)) . '...'; ?>
                    </td>
                    -->
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
<?php endif; ?>
