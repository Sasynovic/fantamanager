<?php

class scambi
{
    private $conn;
    private $table_name = "scambi";

    public $id;

    public function __construct($db)
    {
        $this->conn = $db;
    }

    public function read($squadra_coinvolta = null)
    {
        $query = "
                        SELECT  
                            s.id,
                            s.id_trattativa AS id_trattativa,
                            s.id_squadra_cedente AS id_squadra_cedente,
                            s.id_squadra_ricevente AS id_squadra_ricevente,
                            s.id_calciatore AS id_calciatore,
                            s.debito_credito AS debito_credito, 
                            sc.nome_squadra AS nome_squadra_cedente,
                            sc.id AS id_squadra_cedente,
                            sr.nome_squadra AS nome_squadra_ricevente,
                            sr.id AS id_squadra_ricevente,
                            c.nome AS nome_calciatore
                        FROM " . $this->table_name . " s
                        LEFT JOIN trattative t ON s.id_trattativa = t.id
                        LEFT JOIN squadre sc ON s.id_squadra_cedente = sc.id
                        LEFT JOIN squadre sr ON s.id_squadra_ricevente = sr.id
                        LEFT JOIN calciatori c ON s.id_calciatore = c.id
                        ";
        if ($squadra_coinvolta) {
            $query .= " WHERE s.id_squadra_cedente = :squadra_coinvolta OR s.id_squadra_ricevente = :squadra_coinvolta";
        }
        $query .= " ORDER BY s.id_trattativa DESC";
        $stmt = $this->conn->prepare($query);
        if ($squadra_coinvolta) {
            $stmt->bindParam(':squadra_coinvolta', $squadra_coinvolta);
        }
        $stmt->execute();
        return $stmt;

    }

}