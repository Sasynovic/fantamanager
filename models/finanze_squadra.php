<?php


class finanze_squadra
{

    private $conn;
    private $table_name = "finanze_squadra";

    public $id;
    public $nome_squadra;

    public function __construct($db)
    {
        $this->conn = $db;
    }

    public function read($id_squadra_filter = null)
    {
        $query = "SELECT *
        FROM " . $this->table_name . " f
        LEFT JOIN squadre sq ON f.id_squadra = sq.id
        WHERE 1=1";

        // Aggiunta dinamica dei filtri
        $params = [];
        if ($id_squadra_filter !== null) {
            $query .= " AND f.id_squadra = :id_squadra";
            $params[':id_squadra'] = $id_squadra_filter;
        }
        $stmt = $this->conn->prepare($query);
        // Bind dei parametri
        foreach ($params as $key => $value) {
            $stmt->bindValue($key, $value);
        }
        $stmt->execute();
        return $stmt;
    }

    public function update($id, $data): bool
    {
        // Validazione dell'ID
        if (!is_numeric($id) || $id <= 0) {
            throw new InvalidArgumentException("L'ID della finanza non è valido");
        }

        // Verifica che $data sia un array
        if (!is_array($data)) {
            throw new InvalidArgumentException("I dati per l'aggiornamento devono essere un array");
        }

        // Campi aggiornabili
        $allowedFields = [
            'totale_crediti_bilancio' => PDO::PARAM_INT
        ];

        $fields = [];
        $params = [':id' => $id];

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
            $fieldName = str_replace(':', '', $key);
            $type = $allowedFields[$fieldName] ?? PDO::PARAM_INT;
            $stmt->bindValue($key, $value, $type);
        }

        try {
            $stmt->execute();
            return ($stmt->rowCount() > 0);
        } catch (PDOException $e) {
            error_log("Errore durante l'aggiornamento delle finanze: " . $e->getMessage());
            return false;
        }
    }
}

