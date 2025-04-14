<?php

class squadra
{

    private $conn;
    private $table_name = "squadre";

    public $id;
    public $nome_squadra;
    public $id_pres;
    public $id_vice;
    public $id_stadio;

    public function __construct($db)
    {
        $this->conn = $db;
    }

    public function read()
    {
        $query =  "
                        SELECT 
                            s.id,
                            s.nome_squadra,
                            pres.nome AS nome_pres,
                            pres.cognome AS cognome_pres,
                            vice.nome AS nome_vice,
                            vice.cognome AS cognome_vice,
                            st.nome_stadio
                        FROM " . $this->table_name . " s
                        LEFT JOIN presidenti pres ON s.id_pres = pres.id
                        LEFT JOIN presidenti vice ON s.id_vice = vice.id
                        LEFT JOIN stadio st ON s.id_stadio = st.id
                        ORDER BY s.id ASC";

        $stmt = $this->conn->prepare($query);
        $stmt->execute();

        return $stmt;
    }
}