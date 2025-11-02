<?php
// Munkamenet törlése
session_unset(); // Törli az összes session változót
session_destroy(); // Megsemmisíti a munkamenetet

// Átirányítás a kezdőlapra
header('Location: index.php?page=home');
exit;
?>
