<?php

class tipologia_scambio
{


    private $conn;
    private $table_name = "tipologia_scambio";

    public $id;


    public function __construct($db)
    {
        $this->conn = $db;
    }

    public function count($search = null)
    {
        $query = "SELECT COUNT(*) as total
                    FROM " . $this->table_name . " ts
                    LEFT JOIN finestra_mercato fm ON ts.id_finestra_mercato = fm.id
                    LEFT JOIN stagioni_sportive ss ON fm.id_stagione = ss.id
                    WHERE 1=1";

        if ($search) {
            $query .= " AND ts.tipologia LIKE :search";
        }

        $stmt = $this->conn->prepare($query);

        if ($search) {
            $search_term = "%$search%";
            $stmt->bindParam(':search', $search_term, PDO::PARAM_STR);
        }

        $stmt->execute();
        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        return $row['total'];
    }

    public function read($search = null,$limit = 10, $offset = 0)
    {
        $query = "SELECT 
                    ts.id as id_tipologia,
                    ts.id_metodo,
                    ts.id_finestra_mercato,
                    
                    
                    ms.nome as nome_metodo,
                    
                    fm.id,
                    fm.nome,
                    fm.data_inizio,
                    fm.data_fine,
                    fm.id_stagione,
                    
                    ss.stagione,
                    ss.attiva
                    
                    FROM " . $this->table_name . " ts
                    LEFT JOIN metodi_scambio ms ON ts.id_metodo = ms.id
                    LEFT JOIN finestra_mercato fm ON ts.id_finestra_mercato = fm.id
                    LEFT JOIN stagioni_sportive ss ON fm.id_stagione = ss.id
                    WHERE 1=1
                    ";

        if ($search) {
            $query .= " AND ts.tipologia LIKE :search";
        }

        $query .= " ORDER BY ts.id ASC";
        $query .= " LIMIT :limit OFFSET :offset";



        $stmt = $this->conn->prepare($query);

        if ($search) {
            $search_term = "%$search%";
            $stmt->bindParam(':search', $search_term, PDO::PARAM_STR);
        }

        $stmt->bindParam(':limit', $limit, PDO::PARAM_INT);
        $stmt->bindParam(':offset', $offset, PDO::PARAM_INT);

        $stmt->execute();

        return $stmt;
    }

}