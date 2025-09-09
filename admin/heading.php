<!DOCTYPE html>
<html lang="it">
<head>
    <meta charset="UTF-8">
    <title>Dashboard Admin</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" href="style2.css">
</head>
<body>
<div class="sidebar">
    <!-- <img src="public/background/logo.png" class="logo" width="80px" height="80px"> -->
    <!-- Qui ci mettiamo il logo -->
    <h2 id="nome-admin">Fantamanager Pro</h2>
    <ul class="sidebar-menu">
        <li><a href="dashboard_admin.php">Admin Dashboard</a></li>
        <li><a href="gestione_news.php">Gestione News</a></li>
        <li><a href="download.php">Sezione download</a></li>
        <li><a href="gestione_squadre.php">Gestione Squadre</a></li>
        <li><a href="gestione_presidenti.php">Gestione Presidenti</a></li>
        <li><a href="setFVM.php">Gestione FVM</a></li>
        <li><a href="setFinanze.php">Gestione Finanze</a></li>
        <li><a href="setRose.php">Gestione Rose</a></li>
        <li><a href="gestione_competizioni.php">Gestione Competizioni</a></li>
        <li><a href="approva_trattativa.php">Approva Trattativa</a></li>
        <li><a href="logout.php">Logout</a></li>
    </ul>
</div>
<div class="app-container">
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
