<?php


class finanze_squadra
{

    private $conn;
    private $table_name = "finanze_squadra";

    public $id;
    public $nome_squadra;


    public function __construct($db)
    {
        $this->conn = $db;
    }

    public function read($id_squadra_filter = null)
    {
        $query = "SELECT *
        FROM " . $this->table_name . " f
        LEFT JOIN squadre sq ON f.id_squadra = sq.id
        WHERE 1=1";

        // Aggiunta dinamica dei filtri
        $params = [];
        if ($id_squadra_filter !== null) {
            $query .= " AND f.id_squadra = :id_squadra";
            $params[':id_squadra'] = $id_squadra_filter;
        }
        $stmt = $this->conn->prepare($query);
        // Bind dei parametri
        foreach ($params as $key => $value) {
            $stmt->bindValue($key, $value);
        }
        $stmt->execute();
        return $stmt;
    }
}

