<?php

class stagioni_sportive
{


    private $conn;
    private $table_name = "stagioni_sportive";

    public $id;


    public function __construct($db)
    {
        $this->conn = $db;
    }

    public function read()
    {
        $query = "SELECT 
                    id, 
                    stagione
                    FROM " . $this->table_name . " 
                    ORDER BY id ASC";

        $stmt = $this->conn->prepare($query);
        $stmt->execute();

        return $stmt;
    }

}