<!DOCTYPE html>
<html lang="it">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login Admin</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="style-login.css">
</head>
<body>

<div class="login-container">
    <h2>Area Admin</h2>
    <div id="error" class="error"></div>
    <form id="loginForm">
        <div class="username-container">
         <label for="username">Username</label>
         <input type="text" id="username" name="username" required>
        </div>
        <div class="password-container">
        <label for="password">Password</label>
        <input type="password" id="password" name="password" required>
        </div>
        <button type="submit">Accedi</button>
    </form>
</div>

<script>
    const form = document.getElementById('loginForm');
    const errorBox = document.getElementById('error');

    form.addEventListener('submit', function(e) {
        e.preventDefault();

        const username = form.username.value.trim();
        const password = form.password.value;

        if (!username || !password) {
            errorBox.textContent = "Inserisci tutti i campi.";
            return;
        }

//  fetch(`${window.location.protocol}//${window.location.host}//endpoint/admin/login.php`, {
    fetch('/fantam/fantamanager/endpoint/admin/login.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json'
            },
            body: JSON.stringify({ username, password })
        })
            .then(res => res.json())
            .then(data => {
                if (data.success) {
                    window.location.href = 'dashboard_admin.php';
                } else {
                    errorBox.textContent = "Credenziali non valide.";
                }
            })
            .catch(err => {
                errorBox.textContent = "Errore di rete.";
                console.error(err);
            });
    });
</script>

</body>
</html>
