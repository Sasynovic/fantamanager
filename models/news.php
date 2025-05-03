<?php

class news
{
    private $conn;
    private $table_name = "news";

    public $id;

    public function __construct($db)
    {
        $this->conn = $db;
    }

    public function read($id_competizione = null, $limit = null) {
        $query = "
        SELECT  
            n.id,
            n.titolo,
            n.contenuto,
            n.data_pubblicazione,
            n.autore,
            n.id_competizione,
            n.visibile
        FROM " . $this->table_name . " n
        LEFT JOIN competizione c ON n.id_competizione = c.id
        WHERE 1=1
    ";

        if ($id_competizione !== null) {
            $query .= " AND n.id_competizione = :id_competizione";
        }

        $query .= " ORDER BY n.data_pubblicazione DESC";

        if ($limit !== null) {
            $query .= " LIMIT " . intval($limit);
        }


        $stmt = $this->conn->prepare($query);

        if ($id_competizione !== null) {
            $stmt->bindParam(':id_competizione', $id_competizione, PDO::PARAM_INT);
        }

        if ($stmt->execute()) {
            return $stmt;
        }

        return null;
    }



}