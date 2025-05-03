<?php

class settore_giovanile
{

    private $conn;
    private $table_name = "settore_giovanile";

    public $id;
    public $nome_squadra;

    public function __construct($db)
    {
        $this->conn = $db;
    }

    public function read($id_squadra_filter = null)
    {
        $query = "SELECT 
        s.id,
        s.prima_squadra,
        s.fuori_listone,
        s.nome_calciatore,
        sq.nome_squadra,
        ss.stagione AS anno_stagione
        FROM " . $this->table_name . " s
        LEFT JOIN squadre sq ON s.id_squadra = sq.id
        LEFT JOIN stagioni_sportive ss ON s.id_stagione = ss.id
        WHERE 1=1";

        // Aggiunta dinamica dei filtri
        $params = [];
        if ($id_squadra_filter !== null) {
            $query .= " AND s.id_squadra = :id_squadra";
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

