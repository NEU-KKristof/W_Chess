<?php
//INDÍTÁSA
session_start();

//1.ADATBÁZIS BEHÍVÁSA
require_once 'database_handler.php';


//2. ALAPBEÁLLÍTÁSOK
$page = $_GET['page'] ?? 'home';

//3.Lehetséges oldalak
$allowed_pages = [
    'home',
    'register',
    'login',
    'logout',
    'profile',
    'chess'
];

if (in_array($page, $allowed_pages)) {
    $page_path = "pages/{$page}.php";
    
    if (file_exists($page_path)) {
        $content_file = $page_path;
    }
} else {
    http_response_code(404);
}

//5. AZ OLDAL MEGJELENITÉSE

// 1. Fejléc
include 'partials/header.php';

// 2.Tartalom
include $content_file;

// 3. Lábléc
include 'partials/footer.php';
?>

