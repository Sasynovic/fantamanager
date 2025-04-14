<?php

class partecipazione
{

    private $conn;
    private $table_name = "partecipazione";

    public $id;
    public $id_competizione;
    public $id_squadra;

    public function __construct($db)
    {
        $this->conn = $db;
    }

    public function read()
    {
        $query =  "
                        SELECT  *
                        FROM " . $this->table_name . " p
                        LEFT JOIN `competizione` c ON p.id_competizione = c.id
                        LEFT JOIN `squadre` s ON p.id_squadra = s.id
                        ORDER BY p.id ASC";

        $stmt = $this->conn->prepare($query);
        $stmt->execute();

        return $stmt;
    }

    public function readCompetizione($id_competizione)
    {
        $query =  "
                        SELECT  *
                        FROM " . $this->table_name . " p
                        LEFT JOIN `competizione` c ON p.id_competizione = c.id
                        LEFT JOIN `squadre` s ON p.id_squadra = s.id
                        WHERE p.id_competizione = :id_competizione
                        ORDER BY p.id ASC";

        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id_competizione', $id_competizione);
        $stmt->execute();

        return $stmt;
    }
}