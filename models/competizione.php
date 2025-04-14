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

    public function read()
    {
        $query =  "
                        SELECT  *
                        FROM " . $this->table_name . " c
                        LEFT JOIN `divisione` d ON c.id_divisione = d.id
                        ORDER BY c.id ASC";

        $stmt = $this->conn->prepare($query);
        $stmt->execute();

        return $stmt;
    }

    public function readDivision($id_divisione)
    {
        $query =  "
                        SELECT  *
                        FROM " . $this->table_name . " c
                        LEFT JOIN `divisione` d ON c.id_divisione = d.id
                        LEFT JOIN `squadra` s ON c.id = s.id_competizione
                        WHERE c.id_divisione = :id_divisione
                        ORDER BY c.id ASC";

        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id_divisione', $id_divisione);
        $stmt->execute();

        return $stmt;
    }
}