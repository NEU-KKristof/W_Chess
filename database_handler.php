<?php
// --- 1. ADATBÁZIS KAPCSOLAT BEÁLLÍTÁSAI ---
$db_host = '127.0.0.1'; // Vagy 'localhost'
$db_name = 'sakk_adatbazis'; // Hozz létre egy ilyen nevű adatbázist (sakk_adatbazis)
$db_user = 'root'; // Az adatbázis felhasználóneved
$db_pass = ''; // Az adatbázis jelszavad (XAMPP esetén alapból üres)

// Csatlakozás PDO-val (PHP Data Objects) - ez modern és biztonságos
try {
    $pdo = new PDO("mysql:host=$db_host;dbname=$db_name;charset=utf8mb4", $db_user, $db_pass);
    // Hibakezelés beállítása: kivételeket dobjon hiba esetén
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    // Hiba esetén leállítjuk az oldalt és kiírjuk a hibát
    die("Hiba az adatbázis-kapcsolódás során: " . $e->getMessage());
}


// --- 2. TÁBLÁK LÉTREHOZÁSA (HA MÉG NEM LÉTEZNEK) ---
// Ez a rész automatikusan létrehozza a szükséges táblákat az első futáskor.

// Felhasználói tábla
$sql_users = "
CREATE TABLE IF NOT EXISTS users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) NOT NULL UNIQUE,
    email VARCHAR(100) NOT NULL UNIQUE,
    password_hash VARCHAR(255) NOT NULL, -- Itt tároljuk a VÉDETT jelszót
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);
";

// Meccsek tábla
$sql_matches = "
CREATE TABLE IF NOT EXISTS matches (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id_white INT NOT NULL,
    user_id_black INT NOT NULL,
    match_data TEXT, -- Ide mentheted a lépéseket (pl. JSON vagy PGN formátumban)
    winner_id INT, -- A győztes user ID-ja, vagy 0 döntetlen esetén, NULL ha még tart
    played_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    
    -- Idegen kulcsok, hogy összekössük a felhasználókkal
    FOREIGN KEY (user_id_white) REFERENCES users(id),
    FOREIGN KEY (user_id_black) REFERENCES users(id),
    FOREIGN KEY (winner_id) REFERENCES users(id)
);
";

// Táblák futtatása
try {
    $pdo->exec($sql_users);
    $pdo->exec($sql_matches);
} catch (PDOException $e) {
    die("Hiba a táblák létrehozása során: " . $e->getMessage());
}


// --- 3. FELHASZNÁLÓI FUNKCIÓK ---

/**
 * Új felhasználó regisztrálása VÉDETT jelszóval.
 *
 * @param PDO $pdo Az adatbázis kapcsolat
 * @param string $username A választott felhasználónév
 * @param string $email Az e-mail cím
 * @param string $password A sima szöveges jelszó
 * @return string Siker esetén "siker", hiba esetén hibaüzenet
 */
function registerUser($pdo, $username, $email, $password) {
    // Ellenőrzés, hogy foglalt-e már
    $stmt = $pdo->prepare("SELECT * FROM users WHERE username = ? OR email = ?");
    $stmt->execute([$username, $email]);
    if ($stmt->fetch()) {
        return "A felhasználónév vagy az e-mail cím már foglalt.";
    }

    // Jelszó hashelése (VÉDELEM)
    // Ez a legfontosabb rész! Soha ne tárolj sima jelszót!
    $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

    // Felhasználó beszúrása az adatbázisba
    try {
        $stmt = $pdo->prepare("INSERT INTO users (username, email, password_hash) VALUES (?, ?, ?)");
        $stmt->execute([$username, $email, $hashedPassword]);
        return "siker";
    } catch (PDOException $e) {
        return "Hiba a regisztráció során: " . $e->getMessage();
    }
}

/**
 * Felhasználó bejelentkeztetése.
 *
 * @param PDO $pdo Az adatbázis kapcsolat
 * @param string $username A felhasználónév
 * @param string $password A megadott jelszó
 * @return array|string Hiba esetén hibaüzenet, siker esetén a felhasználó adatai
 */
function loginUser($pdo, $username, $password) {
    // Felhasználó keresése
    $stmt = $pdo->prepare("SELECT * FROM users WHERE username = ?");
    $stmt->execute([$username]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    // Ellenőrzés: létezik a felhasználó?
    if (!$user) {
        return "Hibás felhasználónév vagy jelszó.";
    }

    // Jelszó ellenőrzése a hashelt változattal
    if (password_verify($password, $user['password_hash'])) {
        // Siker! Visszaadjuk a felhasználó adatait (jelszó hash NÉLKÜL)
        unset($user['password_hash']); // Biztonsági okból töröljük a tömbből
        return $user;
    } else {
        // Hibás jelszó
        return "Hibás felhasználónév vagy jelszó.";
    }
}


// --- 4. SAKKMECCS FUNKCIÓK ---

/**
 * Elment egy befejezett sakkmeccset.
 *
 * @param PDO $pdo Az adatbázis kapcsolat
 * @param int $whiteId A fehér játékos user ID-ja
 * @param int $blackId A fekete játékos user ID-ja
 * @param string $matchData A meccs adatai (pl. lépéslista JSON-ben)
 * @param int|null $winnerId A győztes user ID-ja (vagy 0 döntetlennél)
 * @return bool Sikerült-e a mentés
 */
function saveMatch($pdo, $whiteId, $blackId, $matchData, $winnerId = null) {
    try {
        $sql = "INSERT INTO matches (user_id_white, user_id_black, match_data, winner_id) 
                VALUES (?, ?, ?, ?)";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$whiteId, $blackId, $matchData, $winnerId]);
        return true;
    } catch (PDOException $e) {
        // Itt érdemes lenne naplózni a hibát
        // error_log("Meccs mentési hiba: " . $e->getMessage());
        return false;
    }
}

/**
 * Lekérdezi egy felhasználó összes meccsét.
 *
 * @param PDO $pdo Az adatbázis kapcsolat
 * @param int $userId A felhasználó ID-ja
 * @return array A meccsek listája
 */
function getMatchesForUser($pdo, $userId) {
    // Lekérdezzük azokat a meccseket, ahol a felhasználó VAGY fehér VAGY fekete volt.
    // A JOIN-okkal lekérjük a játékosok neveit is az ID-k helyett.
    $stmt = $pdo->prepare("
        SELECT 
            m.*, 
            w.username AS white_username, 
            b.username AS black_username
        FROM matches m
        JOIN users w ON m.user_id_white = w.id
        JOIN users b ON m.user_id_black = b.id
        WHERE m.user_id_white = ? OR m.user_id_black = ?
    ");
    // NOTE: Az `orderBy` használata indexek nélkül lassú lehet nagy adatbázisnál, de itt demonstrációs célra jó.
    // ORDER BY m.played_at DESC 
    
    $stmt->execute([$userId, $userId]);
    
    // Összes meccs lekérése asszociatív tömbként
    $allMatches = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    // Adatok rendezése PHP-ben az indexelési problémák elkerülése végett
    // Legújabb meccs legyen elöl
    usort($allMatches, function($a, $b) {
        return strtotime($b['played_at']) - strtotime($a['played_at']);
    });

    return $allMatches;
}

?>

