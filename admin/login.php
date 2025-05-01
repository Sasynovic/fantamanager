<!DOCTYPE html>
<html lang="it">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login Admin</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600&display=swap" rel="stylesheet">
    <style>
        :root {
            --primary: #0066cc;
            --background: #f0f4f8;
            --card-bg: #ffffff;
            --border: #ccc;
            --danger: #e74c3c;
        }

        * {
            box-sizing: border-box;
        }

        body {
            margin: 0;
            font-family: 'Inter', sans-serif;
            background: var(--background);
            display: flex;
            align-items: center;
            justify-content: center;
            height: 100vh;
        }

        .login-container {
            background: var(--card-bg);
            padding: 2rem;
            border-radius: 12px;
            box-shadow: 0 4px 12px rgba(0,0,0,0.1);
            max-width: 400px;
            width: 100%;
        }

        h2 {
            margin-top: 0;
            margin-bottom: 1.5rem;
            text-align: center;
            color: var(--primary);
        }

        label {
            font-weight: 600;
            display: block;
            margin-bottom: 0.3rem;
        }

        input[type="text"], input[type="password"] {
            width: 100%;
            padding: 0.75rem;
            margin-bottom: 1rem;
            border: 1px solid var(--border);
            border-radius: 8px;
            font-size: 1rem;
        }

        button {
            width: 100%;
            padding: 0.75rem;
            background: var(--primary);
            color: white;
            border: none;
            border-radius: 8px;
            font-size: 1rem;
            cursor: pointer;
            transition: background 0.3s ease;
        }

        button:hover {
            background: #004c99;
        }

        .error {
            color: var(--danger);
            margin-bottom: 1rem;
            text-align: center;
        }

        @media (max-width: 480px) {
            .login-container {
                margin: 1rem;
            }
        }
    </style>
</head>
<body>

<div class="login-container">
    <h2>Area Admin</h2>
    <div id="error" class="error"></div>
    <form id="loginForm">
        <label for="username">Username</label>
        <input type="text" id="username" name="username" required>

        <label for="password">Password</label>
        <input type="password" id="password" name="password" required>

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

        fetch('https://barrettasalvatore.it/endpoint/admin/login.php', {
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
