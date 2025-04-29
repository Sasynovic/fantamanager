<?php

class associazioni
{

    private $conn;
    private $table_name = "associazioni";

    public $id;
    public $id_squadra;
    public $id_calciatore;
    public $costo_calciatore;

    public function __construct($db)
    {
        $this->conn = $db;
    }

    public function read($id_squadra_filter = null)
    {
        $query = "
            SELECT  
                a.id,
                a.id_squadra,
                a.id_calciatore,
                a.costo_calciatore,
                s.nome_squadra AS nome_squadra,
                c.nome AS nome_calciatore,
                c.ruolo AS ruolo_calciatore
            FROM  " . $this->table_name . " a
            LEFT JOIN squadre s ON a.id_squadra = s.id
            LEFT JOIN calciatori c ON a.id_calciatore = c.id";

        if ($id_squadra_filter !== null) {
            $query .= " WHERE a.id_squadra = :id_squadra";
        }
        $query .= " ORDER BY ruolo_calciatore DESC, nome_calciatore ASC";

        $stmt = $this->conn->prepare($query);

        if ($id_squadra_filter !== null) {
            $stmt->bindParam(":id_squadra", $id_squadra_filter, PDO::PARAM_INT);
        }

        $stmt->execute();
        return $stmt;
    }
}
           

