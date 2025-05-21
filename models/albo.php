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

    public function count($id_squadra_filter = null, $anno_filter = null, $id_competizione_filter = null){
        $query = "SELECT COUNT(*) as total 
                         FROM  ".$this->table_name." a
                        LEFT JOIN stagioni_sportive ss ON a.id_stagione = ss.id
                        LEFT JOIN competizione c ON a.id_competizione = c.id
                        LEFT JOIN squadre s ON a.id_squadra = s.id
                        LEFT JOIN divisione d ON c.id_divisione = d.id
                        WHERE 1=1";

        if ($id_squadra_filter !== null) {
            $query .= " AND a.id_squadra = :id_squadra";
        }
        if ($anno_filter !== null) {
            $query .= " AND ss.stagione = :anno";
        }
        if ($id_competizione_filter !== null) {
            $query .= " AND a.id_competizione = :id_competizione";
        }

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
        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        return $row['total'];
    }

    public function read($id_squadra_filter = null, $anno_filter = null, $id_competizione_filter = null,$limit = null, $offset = 0)
    {
        $query = "
            SELECT  
                a.id,
                a.id_competizione,
                a.id_squadra,
                
                c.id_divisione AS id_divisione,
                c.nome_competizione AS nome_competizione,
                
                s.nome_squadra AS nome_squadra,
                d.nome_divisione AS nome_divisione,
                
                ss.stagione as stagione
            
            
            FROM  ".$this->table_name." a
            LEFT JOIN stagioni_sportive ss ON a.id_stagione = ss.id
            LEFT JOIN competizione c ON a.id_competizione = c.id
            LEFT JOIN squadre s ON a.id_squadra = s.id
            LEFT JOIN divisione d ON c.id_divisione = d.id
            WHERE 1=1";

        if ($id_squadra_filter !== null) {
            $query .= " AND a.id_squadra = :id_squadra";
        }
        if ($anno_filter !== null) {
            $query .= " AND ss.stagione = :anno";
        }
        if ($id_competizione_filter !== null) {
            $query .= " AND a.id_competizione = :id_competizione";
        }
        $query .= " ORDER BY ss.stagione DESC, nome_competizione ASC";
        $query .= " LIMIT :limit OFFSET :offset";

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

        $stmt->bindParam(':limit', $limit, PDO::PARAM_INT);
        $stmt->bindParam(':offset', $offset, PDO::PARAM_INT);

        $stmt->execute();
        return $stmt;
    }}


