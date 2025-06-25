<!DOCTYPE html>
<html lang="it">
<head>
    <meta charset="UTF-8">
    <title>Dashboard Admin</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" href="style2.css">
    <style>
        .navbar {
            background-color: var(--primary);
            color: white;
            display: flex;
            justify-content: space-between;
            padding: 1rem 2rem;
            align-items: center;
        }

        .navbar h1 {
            color: white;
            margin: 0;
            font-size: 1.5rem;
        }

        .menu {
            display: flex;
            gap: 1.5rem;
        }

        .menu > div,
        .menu a {
            position: relative;
            color: white;
            text-decoration: none;
            font-weight: bold;
        }

        .menu a:hover {
            text-decoration: underline;
        }

        /* Dropdown */
        .dropdown {
            cursor: pointer;
        }

        .dropdown-content {
            display: none;
            position: absolute;
            background-color: #333;
            min-width: 180px;
            z-index: 1;
            top: 100%;
            left: 0;
            border-radius: 5px;
            box-shadow: 0 2px 6px rgba(0,0,0,0.3);
        }

        .dropdown-content a {
            display: block;
            padding: 10px 15px;
            color: white;
            text-decoration: none;
        }

        .dropdown-content a:hover {
            background-color: #444;
        }

        .dropdown:hover .dropdown-content {
            display: block;
        }

        @media (max-width: 1024px) {
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
    <a href="dashboard_admin.php">Admin Dashboard</a>
    <div class="menu">
        <a href="gestione_news.php">Gestione News</a>

        <div class="dropdown">
            <span>Squadre ▾</span>
            <div class="dropdown-content">
                <a href="gestione_squadre.php">Gestione Squadre</a>
                <a href="gestione_presidenti.php">Gestione Presidenti</a>
                <a href="setFVM.php">Gestione FVM</a>
            </div>
        </div>

        <a href="gestione_competizioni.php">Gestione Competizioni</a>

        <a href="approva_trattativa.php">Approva Trattativa</a>
        <a href="logout.php">Logout</a>
    </div>
</div>
</body>
</html>

<script>
    let timeout = 120000; // 600.00 ms = 1 minuto
    let timer;

    function resetTimer() {
        clearTimeout(timer);
        timer = setTimeout(() => {
            // Redirige alla pagina di logout
            window.location.href = "logout.php";
        }, timeout);
    }

    // Eventi che indicano attività
    window.onload = resetTimer;
    document.onmousemove = resetTimer;
    document.onkeypress = resetTimer;
</script>
