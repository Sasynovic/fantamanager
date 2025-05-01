<?php
session_start();
if (!isset($_SESSION['admin_id'])) {
    header("Location: login.php"); // reindirizza se non loggato
    exit;
}
?>

<!DOCTYPE html>
<html lang="it">
<head>
    <meta charset="UTF-8">
    <title>Dashboard Admin</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <style>
        :root {
            --primary: #0057ff;
            --bg: #f4f4f4;
            --text: #333;
        }

        body {
            margin: 0;
            font-family: 'Segoe UI', sans-serif;
            background-color: var(--bg);
        }

        .navbar {
            background-color: var(--primary);
            color: white;
            display: flex;
            justify-content: space-between;
            padding: 1rem 2rem;
            align-items: center;
        }

        .navbar h1 {
            margin: 0;
            font-size: 1.5rem;
        }

        .menu {
            display: flex;
            gap: 1.5rem;
        }

        .menu a {
            color: white;
            text-decoration: none;
            font-weight: bold;
        }

        .menu a:hover {
            text-decoration: underline;
        }

        .content {
            padding: 2rem;
        }

        @media (max-width: 768px) {
            .menu {
                flex-direction: column;
                gap: 1rem;
                align-items: flex-start;
            }

            .navbar {
                flex-direction: column;
                align-items: flex-start;
            }
        }
    </style>
</head>
<body>

<div class="navbar">
    <h1>Admin Dashboard</h1>
    <div class="menu">
        <a href="gestione_squadre.php">Gestione Squadre</a>
        <a href="scambi_admin.php">Visualizza Scambi</a>
        <a href="logout.php">Logout</a>
    </div>
</div>

<div class="content">
    <h2>Benvenuto Admin!</h2>
    <p>Da qui puoi gestire squadre, scambi e molto altro.</p>
</div>

</body>
</html>
