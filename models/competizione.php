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

    public function read($id_divisione = null)
    {
        $query =  "
                        SELECT  
                            c.id, 
                            c.id_divisione AS id_divisione, 
                            c.nome_competizione, 
                            d.nome_divisione
                        FROM " . $this->table_name . " c
                        LEFT JOIN divisione d ON c.id_divisione = d.id
                        ";
        if ($id_divisione) {
            $query .= " WHERE c.id_divisione = :id_divisione";
        }
        $query .= " ORDER BY c.id ASC";

        $stmt = $this->conn->prepare($query);
        if ($id_divisione) {
            $stmt->bindParam(':id_divisione', $id_divisione);
        }
        $stmt->execute();


        return $stmt;
    }

}