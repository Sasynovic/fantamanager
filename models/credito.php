<?php

class credito
{

    private $conn;
    private $table_name = "credito";

    public $id;
    public function __construct($db)
    {
        $this->conn = $db;
    }

    public function count($id_trattativa = null)
    {
        $query = "SELECT COUNT(*) as total FROM " . $this->table_name . " WHERE 1=1";
        if ($id_trattativa) {
            $query .= " AND id_trattativa = :id_trattativa";
        }


        $stmt = $this->conn->prepare($query);
        if ($id_trattativa) {
            $stmt->bindParam(':id_trattativa', $id_trattativa, PDO::PARAM_INT);
        }

        $stmt->execute();
        $row = $stmt->fetch(PDO::FETCH_ASSOC);


        return $row['total'];
    }

    public function create(int $id_squadra,int  $id_trattativa, int  $id_fm, int $credito=null){
        if (empty($id_squadra)) {
            throw new InvalidArgumentException("Il id_squadra non può essere vuoto");
        }
        if (empty($id_trattativa)) {
            throw new InvalidArgumentException("Il id_trattativa non può essere vuoto");
        }
        if (empty($id_fm)) {
            throw new InvalidArgumentException("Il id_fm non può essere vuoto");
        }


        $query = "INSERT INTO " . $this->table_name . "
        (id_squadra,id_trattativa,id_fm,credito)
        VALUES
        (:id_squadra,:id_trattativa,:id_fm,:credito)";

        $stmt = $this->conn->prepare($query);

        $stmt -> bindParam(':id_squadra', $id_squadra, PDO::PARAM_INT);
        $stmt -> bindParam(':id_trattativa', $id_trattativa, PDO::PARAM_INT);
        $stmt -> bindParam(':id_fm', $id_fm, PDO::PARAM_INT);
        $stmt -> bindParam(':credito', $credito, PDO::PARAM_INT);

        try {
            return $stmt->execute();
        } catch (PDOException $e) {
            error_log("Errore durante la creazione del presidente: " . $e->getMessage());
            return false;
        }

    }

    public function read($id_trattativa = null,$limit = null, $offset = null)
    {
        $query = "SELECT
                c.id,
                c.id_squadra,
                c.id_fm,
                c.id_trattativa,
                c.credito,
                
                s.nome_squadra,
                
                fm.nome
                
              FROM " . $this->table_name . " c
              LEFT JOIN squadre s ON s.id = c.id_squadra
              LEFT JOIN finestra_mercato fm ON fm.id = c.id_fm
              
             
              WHERE 1=1";

        if ($id_trattativa) {
            $query .= " AND c.id_trattativa = :id_trattativa";
        }

        $query .= " ORDER BY c.id ASC";
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

        if ($id_trattativa) {
            $stmt->bindParam(':id_trattativa', $id_trattativa, PDO::PARAM_INT);
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