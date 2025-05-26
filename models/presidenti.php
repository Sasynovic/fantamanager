<?php

class presidenti
{

    private $conn;
    private $table_name = "presidenti";

    public $id;
    public function __construct($db)
    {
        $this->conn = $db;
    }

    public function count($id= null,$search = null)
    {
        $query = "SELECT COUNT(*) as total FROM " . $this->table_name . " WHERE 1=1";

        if ($id !== null) {
            // Assicurati che l'ID sia un numero valido
            if (!is_numeric($id) || $id <= 0) {
                throw new InvalidArgumentException("L'ID del presidente non è valido");
            }else{
            $query .= " AND id = :id";
            }
        }
        if ($search) {
            $query .= " AND (nome LIKE :search OR cognome LIKE :search)";
        }

        $stmt = $this->conn->prepare($query);

        // Se l'ID è stato fornito, bindalo come parametro
        if ($id !== null) {
            $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        }
        if ($search) {
            $search_term = "%$search%";
            $stmt->bindParam(':search', $search_term, PDO::PARAM_STR);
        }

        $stmt->execute();
        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        return $row['total'];
    }

    public function create(string $nome,string $cognome){
        if (empty($nome)) {
            throw new InvalidArgumentException("Il nome non può essere vuoto");
        }
        if (empty($cognome)) {
            throw new InvalidArgumentException("Il cognome non può essere vuoto");
        }

        $query = "INSERT INTO " . $this->table_name . " 
        (nome, cognome)
        VALUES 
        (:nome, :cognome)";

        $stmt = $this->conn->prepare($query);

        $stmt->bindParam(':nome', $nome, PDO::PARAM_STR);
        $stmt->bindParam(':cognome', $cognome, PDO::PARAM_STR);

        try {
            return $stmt->execute();
        } catch (PDOException $e) {
            error_log("Errore durante la creazione del presidente: " . $e->getMessage());
            return false;
        }

    }

    public function read($limit = null, $offset = null, $id= null,$search = null)
    {
        $query = "SELECT
                p.id,
                p.nome,
                p.cognome
              FROM " . $this->table_name . " p
              WHERE 1=1";

        // Se l'ID è stato fornito, aggiungi la condizione alla query
        if ($id !== null) {
            // Assicurati che l'ID sia un numero valido
            if (!is_numeric($id) || $id <= 0) {
                throw new InvalidArgumentException("L'ID del presidente non è valido");
            }else{
                $query .= " AND p.id = :id";
            }
        }

        if ($search) {
            $query .= " AND (p.nome LIKE :search OR p.cognome LIKE :search)";
        }

        $query .= " ORDER BY p.id ASC";
        // Aggiungi la limitazione e l'offset solo se sono stati forniti
        if ($limit !== null && $offset !== null) {
            $limit = (int)$limit;
            $offset = (int)$offset;
            $query .= " LIMIT :limit OFFSET :offset";
        }

        $stmt = $this->conn->prepare($query);

        // Se sono stati forniti limit e offset, bindali come parametri
        if ($limit !== null && $offset !== null) {
            $stmt->bindParam(':limit', $limit, PDO::PARAM_INT);
            $stmt->bindParam(':offset', $offset, PDO::PARAM_INT);
        }
        // Se l'ID è stato fornito, bindalo come parametro
        if ($id !== null) {
            // Assicurati che l'ID sia un numero valido
            if (!is_numeric($id) || $id <= 0) {
                throw new InvalidArgumentException("L'ID del presidente non è valido");
            }
            $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        }

        if ($search) {
            $search_term = "%$search%";
            $stmt->bindParam(':search', $search_term, PDO::PARAM_STR);
        }

        $stmt->execute();

        return $stmt;
    }

    public function update($id, $data): bool
    {
        // Validazione dell'ID
        if (!is_numeric($id) || $id <= 0) {
            throw new InvalidArgumentException("L'ID della trattativa non è valido");
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
            'nome' => PDO::PARAM_STR,
            'cognome' => PDO::PARAM_STR,
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

    public function delete($id)
    {
        $query = "DELETE FROM " . $this->table_name . " WHERE id = :id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);

        try {
            if (!$stmt->execute()) {
                return false;
            }

            // Verifica se effettivamente è stata eliminata una riga
            return ($stmt->rowCount() > 0);

        } catch (PDOException $e) {
            // Log dell'errore (opzionale)
            error_log("Errore eliminazione: " . $e->getMessage());
            return false;
        }
    }

}