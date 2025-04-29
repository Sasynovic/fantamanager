<?php

class albo
{

    private $conn;
    private $table_name = "albo";

    public $id;
    public $anno;
    public $id_competizione;

    public function __construct($db)
    {
        $this->conn = $db;
    }

    public function read($id_squadra_filter = null, $anno_filter = null, $id_competizione_filter = null)
    {
        $query = "
            SELECT  
                a.id,
                a.anno,
                a.id_competizione,
                a.id_squadra,
                c.nome_competizione AS nome_competizione,
               s.nome_squadra AS nome_squadra
            FROM  " . $this->table_name . " a
            LEFT JOIN competizione c ON a.id_competizione = c.id
            LEFT JOIN squadre s ON a.id_squadra = s.id";

        if ($id_squadra_filter !== null) {
            $query .= " WHERE a.id_squadra = :id_squadra";
        }
        if ($anno_filter !== null) {
            $query .= " WHERE a.anno = :anno";
        }
        if ($id_competizione_filter !== null) {
            $query .= " WHERE a.id_competizione = :id_competizione";
        }
        $query .= " ORDER BY anno DESC, nome_competizione ASC";
        $stmt = $this->conn->prepare($query);

        if ($id_squadra_filter !== null) {
            $stmt->bindParam(":id_squadra", $id_squadra_filter, PDO::PARAM_INT);
        }
        if ($anno_filter !== null) {
            $stmt->bindParam(":anno", $anno_filter, PDO::PARAM_INT);
        }
        if ($id_competizione_filter !== null) {
            $stmt->bindParam(":id_competizione", $id_competizione_filter, PDO::PARAM_INT);
        }
        $stmt->execute();
        return $stmt;
    }}


