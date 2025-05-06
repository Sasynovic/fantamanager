<?php

class stadio
{

    private $conn;
    private $table_name = "stadio";

    public $id;
    public $nome_stadio;
    public function __construct($db)
    {
        $this->conn = $db;
    }

    public function read()
    {
        $query = "SELECT
                       *
                        FROM stadio";

        $stmt = $this->conn->prepare($query);
        $stmt->execute();

        return $stmt;
    }
}