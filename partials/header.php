<!DOCTYPE html>
<html lang="hu">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Online Sakk Adatbázissal</title>
    <!-- Egyszerű stílus, hogy nézzen ki valahogy -->
    <style>
        body { font-family: Arial, sans-serif; line-height: 1.6; background-color: #f4f4f4; margin: 0; padding: 0; }
        .container { max-width: 960px; margin: auto; padding: 0 20px; }
        header { background: #333; color: #fff; padding: 1rem 0; }
        header h1 { text-align: center; margin: 0; padding-bottom: 10px; }
        nav { background: #444; }
        nav ul { padding: 0; margin: 0; list-style: none; display: flex; justify-content: center; }
        nav ul li a { color: #fff; padding: 15px 20px; display: block; text-decoration: none; }
        nav ul li a:hover, nav ul li a.active { background: #555; }
        main { background: #fff; padding: 20px; margin-top: 20px; border-radius: 5px; min-height: 300px; }
        footer { text-align: center; padding: 20px; margin-top: 20px; color: #777; }

        /* Sakk tábla stílusok (a chess.php-ból) */
        #sakktabla { margin-left: auto; margin-right: auto; }
        #sakktabla td {
            width: 60px;
            height: 60px;
            border: 1px solid black;
            text-align: center;
            vertical-align: middle;
            font-size: 40px;
            cursor: pointer;
        }
        .lehetseges-lepes { background-color: rgba(0, 255, 0, 0.4) !important; border-radius: 50%; }
        .kijelolt-cella { background-color: rgba(255, 255, 0, 0.6) !important; }
        .sakkban-cella { background-color: rgba(255, 0, 0, 0.5) !important; }
        #jatekVegeOverlay { display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100%; background-color: rgba(0, 0, 0, 0.7); justify-content: center; align-items: center; z-index: 1000; }
        #jatekVegeModal { background-color: white; padding: 30px; border-radius: 10px; text-align: center; font-size: 1.5em; }

        /* Űrlap stílusok */
        form { max-width: 400px; }
        form div { margin-bottom: 15px; }
        form label { display: block; margin-bottom: 5px; font-weight: bold; }
        form input[type="text"],
        form input[type="email"],
        form input[type="password"] { width: 100%; padding: 8px; box-sizing: border-box; }
        .error { color: red; font-weight: bold; }
        .success { color: green; font-weight: bold; }

    </style>
</head>
<body>

    <header>
        <div class="container">
            <h1>Online Sakk</h1>
        </div>
        <nav>
            <div class="container">
                <ul>
                    <!-- Aktív menüpont kiemelése -->
                    <?php $page = $_GET['page'] ?? 'home'; ?>
                    
                    <li><a href="index.php?page=home" class="<?php echo ($page == 'home') ? 'active' : ''; ?>">Kezdőlap</a></li>
                    <li><a href="index.php?page=chess" class="<?php echo ($page == 'chess') ? 'active' : ''; ?>">Sakk Játék</a></li>

                    <?php if (isset($_SESSION['user'])): ?>
                        <!-- Bejelentkezett felhasználó menüje -->
                        <li><a href="index.php?page=profile" class="<?php echo ($page == 'profile') ? 'active' : ''; ?>">Profil (<?php echo htmlspecialchars($_SESSION['user']['username']); ?>)</a></li>
                        <li><a href="index.php?page=logout">Kijelentkezés</a></li>
                    <?php else: ?>
                        <!-- Kijelentkezett felhasználó menüje -->
                        <li><a href="index.php?page=login" class="<?php echo ($page == 'login') ? 'active' : ''; ?>">Bejelentkezés</a></li>
                        <li><a href="index.php?page=register" class="<?php echo ($page == 'register') ? 'active' : ''; ?>">Regisztráció</a></li>
                    <?php endif; ?>
                </ul>
            </div>
        </nav>
    </header>

    <main class="container">
        <!-- A dinamikus tartalom ide lesz beillesztve -->

