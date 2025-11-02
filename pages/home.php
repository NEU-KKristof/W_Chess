<h2>Üdvözöllek az Online Sakk Oldalon!</h2>

<?php if (isset($_SESSION['user'])): ?>
    <p>Szia, <strong><?php echo htmlspecialchars($_SESSION['user']['username']); ?></strong>!</p>
    <p>Kattints a "Sakk Játék" gombra egy új partiért, vagy nézd meg a "Profil" oldaladat a korábbi meccseidért.</p>
<?php else: ?>
    <p>Ez egy PHP alapú weboldal, ahol sakkozhatsz.</p>
    <p>A meccseid mentéséhez és a profilod megtekintéséhez kérlek, <a href="index.php?page=login">jelentkezz be</a> vagy <a href="index.php?page=register">regisztrálj</a>.</p>
<?php endif; ?>

