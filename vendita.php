<!DOCTYPE html>
<html lang="it">
<head>
    <meta charset="UTF-8">
    <title>Squadre in Vendita</title>
    <style>
        * {
            box-sizing: border-box;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }
        body {
            margin: 0; padding: 40px;
            background: linear-gradient(135deg, #1e1e2f, #323251);
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            color: #fff; display: flex; flex-direction: column; align-items: center;
        }

        h1 {
            font-size: 2em; margin-bottom: 30px;
            text-align: center;
            text-shadow: 0 2px 4px rgba(0, 0, 0, 0.4);
        }

        .table-container {
            width: 90%; max-width: 1000px;
            background: rgba(255, 255, 255, 0.1);
            border-radius: 20px; padding: 20px;
            box-shadow: 0 8px 32px rgba(0, 0, 0, 0.25);
            backdrop-filter: blur(10px);
            overflow-x: auto;
        }

        table { width: 100%; border-collapse: collapse; color: #fff; }
        th, td { padding: 12px 15px; border-bottom: 1px solid rgba(255, 255, 255, 0.2); }
        th { background-color: rgba(255, 255, 255, 0.15); font-weight: 600; }
        tr:hover { background-color: rgba(255, 255, 255, 0.05); }

        .loading, .error {
            font-size: 1.2em;
            text-align: center;
        }

        button {
            background: rgba(255, 255, 255, 0.15);
            border: none;
            border-radius: 10px;
            padding: 8px 12px;
            margin: 0 5px;
            color: white;
            font-weight: bold;
            cursor: pointer;
            transition: background 0.3s;
        }

        button:hover { background: rgba(255, 255, 255, 0.3); }

        .modal {
            display: none; position: fixed; z-index: 999;
            left: 0; top: 0; width: 100%; height: 100%;
            overflow: auto; background-color: rgba(0, 0, 0, 0.8);
        }

        .modal-content {
            background-color: #29293d; margin: 10% auto;
            padding: 20px; border: 1px solid #888;
            width: 80%; max-width: 700px;
            border-radius: 15px; color: #fff;
        }

        .modal-content h2 { margin-top: 0; }
        .close {
            color: #aaa; float: right;
            font-size: 28px; font-weight: bold;
        }

        .close:hover, .close:focus { color: #fff; text-decoration: none; cursor: pointer; }

        .filters {
            margin-bottom: 20px;
            display: flex;
            gap: 20px;
            flex-wrap: wrap;
            justify-content: center;
        }

        .filters label {
            display: flex;
            flex-direction: column;
            font-weight: bold;
        }

        .filters select, .filters input {
            padding: 6px 8px;
            border-radius: 6px;
            border: none;
            margin-top: 5px;
        }
    </style>
</head>
<body>

<button onclick="window.location.href='index.php'">Torna alla Home</button>
<h1>🏆 Squadre attualmente in vendita</h1>

<div class="filters">
    <label>
        Prezzo max (€)
        <input type="number" id="filtroPrezzo" min="0" />
    </label>
    <label>
        Rate
        <select id="filtroRate">
            <option value="">Tutte</option>
            <option value="5">★★★★★</option>
            <option value="4">★★★★</option>
            <option value="3">★★★</option>
            <option value="2">★★</option>
            <option value="1">★</option>
        </select>
    </label>
    <label>
        Lega
        <select id="filtroLega">
        </select>
    </label>
</div>

<div class="table-container">
    <div id="output" class="loading">Caricamento in corso...</div>
</div>

<div id="modaleDettagli" class="modal">
    <div class="modal-content">
        <span class="close" onclick="document.getElementById('modaleDettagli').style.display='none'">&times;</span>
        <div id="contenutoModale"></div>
    </div>
</div>

<script src="script/vendita.js" defer></script>
</body>
</html>
