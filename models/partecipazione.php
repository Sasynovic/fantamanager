<?php

class partecipazione
{

    private $conn;
    private $table_name = "partecipazione";

    public $id;
    public $id_competizione;
    public $id_squadra;

    public function __construct($db)
    {
        $this->conn = $db;
    }

    public function read($id_squadra=null,$id_competizione = null)
    {
        $query =  "
                        SELECT  
                            p.id, 
                            p.id_squadra AS id_squadra,
                            p.id_competizione AS id_competizione, 
                            p.Pos AS Pos,
                            p.Pen AS Pen,
                            p.G AS G,
                            p.V AS V,
                            p.N AS N,
                            p.P AS P,
                            p.Gf AS Gf,
                            p.Gs AS Gs,
                            p.Dr AS Dr,
                            p.Pt AS Pt,
                            p.PtTotali AS PtTotali,
                            p.girone AS girone,
                                
                            c.nome_competizione AS nomeCompetizione,
                            d.nome_divisione AS nomeDivisione,
                            
                            s.id AS id_squadra,
                            s.nome_squadra AS nome_squadra,
                            s.id_pres AS id_pres,
                            s.id_vice AS id_vice,
                            s.rate AS rate,
                            s.credito AS credito,
                            
                            pres.nome AS nome_pres,
                            pres.cognome AS cognome_pres,
                            vice.nome AS nome_vice,
                            vice.cognome AS cognome_vice
                            
                            
                        FROM " . $this->table_name . " p
                        LEFT JOIN squadre s ON p.id_squadra = s.id
                        LEFT JOIN competizione c ON p.id_competizione = c.id
                        LEFT JOIN presidenti pres ON s.id_pres = pres.id
                        LEFT JOIN presidenti vice ON s.id_vice = vice.id
                        LEFT JOIN divisione d ON c.id_divisione = d.id
                        WHERE 1=1
                        ";
        if ($id_competizione) {
            $query .= " AND p.id_competizione = :id_competizione";
        }
        if($id_squadra) {
            $query .= " AND p.id_squadra = :id_squadra";
                        $query .= " AND p.id_competizione <41";
        }
        $query .= " ORDER BY p.id ASC";

        $stmt = $this->conn->prepare($query);
        if ($id_competizione) {
            $stmt->bindParam(':id_competizione', $id_competizione);
        }
        if($id_squadra) {
            $stmt->bindParam(':id_squadra', $id_squadra);
        }
        $stmt->execute();

        return $stmt;
    }

}