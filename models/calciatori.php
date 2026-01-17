<?php

class calciatori{
    private $conn;
    private $table_name = "calciatori";

    public $id;

    public function __construct($db)
    {
        $this->conn = $db;
    }

    public function create(int $id, string $ruolo, string $nome, string $squadra, int $fvm){
        if (empty($id)) {
            throw new InvalidArgumentException("Il campo id non può essere vuoto");
        }
        if (empty($ruolo)) {
            throw new InvalidArgumentException("Il campo ruolo non può essere vuoto");
        }
        if(empty($nome)){
            throw new InvalidArgumentException("Il campo nome non può essere vuoto");
        }
        if(empty($squadra)){
            throw new InvalidArgumentException("Il campo squadra non può essere vuoto");
        }
        if(empty($fvm)){
            throw new InvalidArgumentException("Il campo fvm non può essere vuoto");
        }

        $query = "INSERT INTO calciatori
        (id, ruolo, nome, squadra, fvm)
        VALUES 
        (:id, :ruolo, :nome, :squadra, :fvm);";

        $stmt = $this->conn->prepare($query);

        $stmt->bindParam(':id', $id);
        $stmt->bindParam(':ruolo', $ruolo);
        $stmt->bindParam(':nome', $nome);
        $stmt->bindParam(':squadra', $squadra);
        $stmt->bindParam(':fvm', $fvm);

        try {
            return $stmt->execute();
        } catch (PDOException $e) {
            error_log("Errore durante la creazione della competizione: " . $e->getMessage());
            return false;
        }
    }

    public function read()
    {
        $query = "SELECT
                       *
                        FROM " . $this->table_name . " ";

        $stmt = $this->conn->prepare($query);
        $stmt->execute();

        return $stmt;
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
            'fvm' => PDO::PARAM_INT,
            'fuori_listone' => PDO::PARAM_INT,
            'ruolo' => PDO::PARAM_STR,
            'nome' => PDO::PARAM_STR,
            'squadra' => PDO::PARAM_STR
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