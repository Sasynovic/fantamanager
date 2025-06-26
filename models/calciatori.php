<?php

class calciatori{
    private $conn;
    private $table_name = "calciatori";

    public $id;

    public function __construct($db)
    {
        $this->conn = $db;
    }
public function update($id, $data): bool
{
    // Validazione dell'ID
    if (!is_numeric($id) || $id <= 0) {
        throw new InvalidArgumentException("L'ID del calciatore non è valido");
    }

    // Verifica che $data sia un array
    if (!is_array($data)) {
        throw new InvalidArgumentException("I dati per l'aggiornamento devono essere un array");
    }

    // Costruzione della query dinamica
    $fields = [];
    $params = [':id' => $id];

    // Lista dei campi aggiornabili
    $allowedFields = [
        'id' => PDO::PARAM_INT,
        'fvm' => PDO::PARAM_INT
    ];

    foreach ($allowedFields as $field => $type) {
        if (isset($data[$field])) {
            $fields[] = "$field = :$field";
            $params[":$field"] = $data[$field];
        }
    }

    if (empty($fields)) {
        return false;
    }

    $query = "UPDATE " . $this->table_name . " 
              SET " . implode(', ', $fields) . " 
              WHERE id = :id";

    $stmt = $this->conn->prepare($query);

    // Bind dei parametri
    foreach ($params as $key => $value) {
        $type = $allowedFields[str_replace(':', '', $key)] ?? PDO::PARAM_STR;
        $stmt->bindValue($key, $value, $type);
    }

    try {
        $stmt->execute();
        return ($stmt->rowCount() > 0);
    } catch (PDOException $e) {
        error_log("Errore durante l'aggiornamento della trattativa: " . $e->getMessage());
        return false;
    }
}
}