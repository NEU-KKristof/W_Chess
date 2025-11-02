<?php
// --- 0. MUNKAMENET INDÍTÁSA ---
// Ennek KELL lennie az ELSŐ dolognak a fájlban!
// Ez kezeli a bejelentkezett állapotot.
session_start();

// --- 1. ADATBÁZIS BEHÍVÁSA ---
// Beolvassuk az adatbázis-kapcsolatot ($pdo) és a függvényeket (pl. registerUser)
// Mivel minden oldalon szükség lehet rá, itt érdemes behívni.
require_once 'database_handler.php';


// --- 2. ALAPBEÁLLÍTÁSOK ---
$page = $_GET['page'] ?? 'home';

// --- 3. BIZTONSÁG: ENGEDÉLYEZÉSI LISTA (WHITELIST) ---
// Módosítva az új oldalakra
$allowed_pages = [
    'home',
    'register',
    'login',
    'logout',
    'profile',
    'chess' // Hozzáadtuk a sakk oldalt
];

// --- 4. TARTALOMKEZELÉS ---
$content_file = 'pages/404.php';

if (in_array($page, $allowed_pages)) {
    $page_path = "pages/{$page}.php";
    
    if (file_exists($page_path)) {
        $content_file = $page_path;
    }
} else {
    http_response_code(404);
}

// --- 5. AZ OLDAL FELÉPÍTÉSE ---

// 1. Fejléc (dinamikus menüvel)
include 'partials/header.php';

// 2. Dinamikus tartalom
// A $pdo változó itt már elérhető lesz a beillesztett oldalak (pl. login.php) számára.
include $content_file;

// 3. Lábléc
include 'partials/footer.php';
?>

