<!DOCTYPE html>
<html lang="it">
<head>
    <meta charset="UTF-8">
    <title>Dashboard Admin</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" href="style2.css">
</head>

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

    .menu a {
        color: white;
        text-decoration: none;
        font-weight: bold;
    }

    .menu a:hover {
        text-decoration: underline;
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
<div class="navbar">
    <a href="dashboard_admin.php">Admin Dashboard</a>
    <div class="menu">
        <a href="gestione_news.php">Gestione News</a>
        <a href="gestione_presidenti.php">Gestione Presidenti</a>
        <a href="gestione_competizioni.php">Gestione Competizioni</a>
        <a href="logout.php">Logout</a>
    </div>
</div>

