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

    public function count($id)
    {
        $query = "SELECT COUNT(*) as total FROM " . $this->table_name . " WHERE 1=1";

        if($id){
            $query .= " AND id = :id";
        }

        $stmt = $this->conn->prepare($query);
        if($id >=0){
            $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        }

        $stmt->execute();
        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        return $row['total'];
    }

    public function read($id,$limit = 100, $offset = 0)
    {
        $query = "SELECT
                        id, nome_divisione, bandiera 
                        FROM " . $this->table_name . " 
                        WHERE 1=1";

        if($id){
            $query .= " AND id = :id";
        }
        $query .= " ORDER BY id DESC";
        $query .= " LIMIT :limit OFFSET :offset";

        $stmt = $this->conn->prepare($query);
        if($id >=0){
            $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        }
        $stmt->bindParam(':limit', $limit, PDO::PARAM_INT);
        $stmt->bindParam(':offset', $offset, PDO::PARAM_INT);
        $stmt->execute();

        return $stmt;
    }
}