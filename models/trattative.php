<?php

class trattative
{

    private $conn;
    private $table_name = "trattative";

    public $id;
    public function __construct($db)
    {
        $this->conn = $db;
    }

    public function count()
    {
        $query = "SELECT COUNT(*) as total FROM " . $this->table_name . " WHERE 1=1";

        $stmt = $this->conn->prepare($query);

        $stmt->execute();
        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        return $row['total'];
    }

    public function create(int $id_competizione,int  $id_squadra1, int  $id_squadra2){
        if ($id_competizione<= 0) {
            throw new InvalidArgumentException("Il id_competizione non può essere vuoto");
        }
        if ($id_squadra1<= 0) {
            throw new InvalidArgumentException("Il id_squadra1 non può essere vuoto");
        }
        if ($id_squadra2<= 0) {
            throw new InvalidArgumentException("Il id_squadra2 non può essere vuoto");
        }

        $query = "INSERT INTO " . $this->table_name . " 
        (id_competizione,id_squadra1,id_squadra2)
        VALUES 
        (:id_competizione,:id_squadra1,:id_squadra2)";

        $stmt = $this->conn->prepare($query);

        $stmt -> bindParam(':id_competizione', $id_competizione, PDO::PARAM_INT);
        $stmt -> bindParam(':id_squadra1', $id_squadra1, PDO::PARAM_INT);
        $stmt -> bindParam(':id_squadra2', $id_squadra2, PDO::PARAM_INT);

        try {
            $stmt->execute();
            return $this->conn->lastInsertId();
        } catch (PDOException $e) {
            error_log("Errore durante la creazione del presidente: " . $e->getMessage());
            return false;
        }

    }

    public function read($limit = 10, $offset = 0)
    {
        $query = "SELECT
            t.id,
            t.descrizione,
            t.id_competizione,
            t.id_squadra1,
            t.id_squadra2,
            t.ufficializzata,
            t.data_creazione,
            
            s1.nome_squadra as nome_squadra1,
            s2.nome_squadra as nome_squadra2
         
              FROM " . $this->table_name . " t
              LEFT JOIN squadre s1 ON t.id_squadra1 = s1.id
              LEFT JOIN squadre s2 ON t.id_squadra2 = s2.id
              WHERE 1=1";

        $query .= " ORDER BY t.id ASC";
        $query .= " LIMIT :limit OFFSET :offset";

        $stmt = $this->conn->prepare($query);

        $stmt->bindParam(':limit', $limit, PDO::PARAM_INT);
        $stmt->bindParam(':offset', $offset, PDO::PARAM_INT);

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