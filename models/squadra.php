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

    public function read($vendita_filter = null, $nome_squadra_filter = null, $nome_presidente_filter = null, $id_squadra_filter = null)
    {
        $query =  "
    SELECT 
        s.id,
        s.nome_squadra,
        s.vendita,
        s.rate,
        pres.nome AS nome_pres,
        pres.cognome AS cognome_pres,
        vice.nome AS nome_vice,
        vice.cognome AS cognome_vice,
        st.nome_stadio,
        st.livello_stadio,
        st.costo_manutenzione,
        st.bonus_casa_n,
        st.bonus_casa_u,
        st.guadagno_crediti_coppa,
        st.guadagno_crediti_campionato
    FROM " . $this->table_name . " s
    LEFT JOIN presidenti pres ON s.id_pres = pres.id
    LEFT JOIN presidenti vice ON s.id_vice = vice.id
    LEFT JOIN stadio st ON s.id_stadio = st.id
    WHERE 1=1";

        // Aggiunta dinamica dei filtri
        $params = [];

        if ($vendita_filter !== null) {
            $query .= " AND s.vendita = :vendita";
            $params[':vendita'] = $vendita_filter;
        }

        if (!empty($nome_squadra_filter)) {
            $query .= " AND s.nome_squadra LIKE :nome_squadra";
            $params[':nome_squadra'] = "%" . $nome_squadra_filter . "%";
        }

        if (!empty($nome_presidente_filter)) {
            $query .= " AND (pres.nome LIKE :nome_presidente OR pres.cognome LIKE :nome_presidente)";
            $params[':nome_presidente'] = "%" . $nome_presidente_filter . "%";
        }
        if ($id_squadra_filter !== null) {
            $query .= " AND s.id = :id_squadra";
            $params[':id_squadra'] = $id_squadra_filter;
        }

        $query .= " ORDER BY s.id ASC";

        $stmt = $this->conn->prepare($query);

        foreach ($params as $key => $val) {
            $stmt->bindValue($key, $val);
        }

        $stmt->execute();
        return $stmt;
    }


    public function create()
    {
        $query = "INSERT INTO " . $this->table_name . " 
            (nome_squadra, id_pres, id_vice, id_stadio, vendita, rate)
            VALUES (:nome_squadra, :id_pres, :id_vice, :id_stadio, :vendita, :rate)";

        $stmt = $this->conn->prepare($query);

        $stmt->bindParam(":nome_squadra", $this->nome_squadra);
        $stmt->bindParam(":id_pres", $this->id_pres);
        $stmt->bindParam(":id_vice", $this->id_vice);
        $stmt->bindParam(":id_stadio", $this->id_stadio);
        $stmt->bindParam(":vendita", $this->vendita);
        $stmt->bindParam(":rate", $this->rate);

        return $stmt->execute();
    }

    public function update()
    {
        $query = "UPDATE " . $this->table_name . " 
            SET nome_squadra = :nome_squadra, 
                id_pres = :id_pres, 
                id_vice = :id_vice, 
                id_stadio = :id_stadio,
                vendita = :vendita,
                rate = :rate
            WHERE id = :id";

        $stmt = $this->conn->prepare($query);

        $stmt->bindParam(":nome_squadra", $this->nome_squadra);
        $stmt->bindParam(":id_pres", $this->id_pres);
        $stmt->bindParam(":id_vice", $this->id_vice);
        $stmt->bindParam(":id_stadio", $this->id_stadio);
        $stmt->bindParam(":vendita", $this->vendita);
        $stmt->bindParam(":rate", $this->rate);
        $stmt->bindParam(":id", $this->id);

        return $stmt->execute();
    }

    public function delete()
    {
        $query = "DELETE FROM " . $this->table_name . " WHERE id = :id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":id", $this->id);
        return $stmt->execute();
    }



}
