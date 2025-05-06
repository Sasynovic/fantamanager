<?php

class competizione
{
    private $conn;
    private $table_name = "competizione";

    public $id;
    public $id_divisione;
    public $nome_competizione;

    public function __construct($db)
    {
        $this->conn = $db;
    }

    public function create(int $id_divisione, string $nome_competizione, int $id_sta){

        if (empty($id_divisione)) {
            throw new InvalidArgumentException("Il campo idDivisione non può essere vuoto");
        }
        if (empty($nome_competizione)) {
            throw new InvalidArgumentException("Il campo nomeCompetizione non può essere vuoto");
        }
        if (empty($idStagione)) {
            throw new InvalidArgumentException("Il campo idStagione non può essere vuoto");
        }

        $query = "INSERT INTO " . $this->table_name . " 
        (id_divisione, nome_competizione, id_stagione)
        VALUES 
        (:id_divisione, :nome_competizione, :idStagione);";

        $stmt = $this->conn->prepare($query);

        $stmt->bindParam(':id_divisione', $id_divisione);
        $stmt->bindParam(':nome_competizione', $nome_competizione);
        $stmt->bindParam(':idStagione', $idStagione);

        try {
            return $stmt->execute();
        } catch (PDOException $e) {
            error_log("Errore durante la creazione della competizione: " . $e->getMessage());
            return false;
        }
    }


    public function read($id_divisione = null, $limit = null, $search = null)
    {
        $query =  "
                        SELECT  
                            c.id, 
                            c.id_divisione AS id_divisione, 
                            c.nome_competizione, 
                            
                            d.nome_divisione,
                            ss.stagione AS anno
                        FROM " . $this->table_name . " c
                        LEFT JOIN divisione d ON c.id_divisione = d.id
                        LEFT JOIN stagioni_sportive ss ON c.id_stagione = ss.id
                        WHERE 1=1
                        ";
        if ($id_divisione) {
            $query .= " AND c.id_divisione = :id_divisione";
        }
        if ($search) {
            $query .= " AND c.nome_competizione LIKE :search";
        }
        $query .= " ORDER BY c.id ASC";

        if ($limit !== null) {
            $query .= " LIMIT " . intval($limit); // safe cast
        }

        $stmt = $this->conn->prepare($query);

        if ($id_divisione !== null) {
            $stmt->bindParam(':id_divisione', $id_divisione, PDO::PARAM_INT);
        }
        if ($search) {
            $search = "%$search%";
            $stmt->bindParam(':search', $search, PDO::PARAM_STR);
        }


        if ($stmt->execute()) {
            return $stmt;
        }

        return null;
    }

}