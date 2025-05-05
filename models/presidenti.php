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

    public function read($limit = null, $search = null )
    {
        $query =  "SELECT
                        p.id,
                        p.nome,
                        p.cognome
                        FROM " . $this->table_name . " p
                         WHERE 1=1";
        if ($search) {
            $query .= " AND (p.nome LIKE :search OR p.cognome LIKE :search)";
        }
        $query .= " ORDER BY p.id ASC";
        if ($limit) {
            $query .= " LIMIT :limit";
        }


        $stmt = $this->conn->prepare($query);
        if ($search) {
            $search = "%$search%";
            $stmt->bindParam(':search', $search, PDO::PARAM_STR);
        }
        if ($limit) {
            $stmt->bindParam(':limit', $limit, PDO::PARAM_INT);
        }
        $stmt->execute();

        return $stmt;
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