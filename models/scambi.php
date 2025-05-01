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
                            s.id_squadra_c AS id_squadra_cedente,
                            s.id_squadra_r AS id_squadra_ricevente,
                            s.id_calciatore AS nome_calciatore,
                            s.credito_debito AS credito_debito, 
                            sc.nome_squadra AS nome_squadra_cedente,
                            sc.id AS id_squadra_cedente,
                            sr.nome_squadra AS nome_squadra_ricevente,
                            sr.id AS id_squadra_ricevente,
                            t.descrizione AS descrizione,
                            t.data_inizio AS data_inizio,
                            t.data_fine AS data_fine
                        FROM " . $this->table_name . " s
                        LEFT JOIN trattative t ON s.id_trattativa = t.id
                        LEFT JOIN squadre sc ON s.id_squadra_c = sc.id
                        LEFT JOIN squadre sr ON s.id_squadra_r = sr.id
                        ";
        if ($squadra_coinvolta) {
            $query .= " WHERE s.id_squadra_c = :squadra_coinvolta OR s.id_squadra_r = :squadra_coinvolta";
        }
        $query .= " ORDER BY s.id ASC";
        $stmt = $this->conn->prepare($query);
        if ($squadra_coinvolta) {
            $stmt->bindParam(':squadra_coinvolta', $squadra_coinvolta);
        }
        $stmt->execute();
        return $stmt;

    }

}