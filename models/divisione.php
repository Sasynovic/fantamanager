<?php

class divisione
{

    private $conn;
    private $table_name = "divisione";

    public $id;
    public $nome_divisione;
    public $bandiera;

    public function __construct($db)
    {
        $this->conn = $db;
    }

    public function read()
    {
        $query = "SELECT id, nome_divisione, bandiera FROM " . $this->table_name . " ORDER BY nome_divisione ASC";

        $stmt = $this->conn->prepare($query);
        $stmt->execute();

        return $stmt;
    }
}