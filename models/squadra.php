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

    public function count($prezzo=null,$rate=null,$vendita_filter = null, $search = null, $nome_presidente_filter = null, $id_squadra_filter = null)
    {
        $query = "SELECT COUNT(*) as total 
                        FROM " . $this->table_name . "  s
                        LEFT JOIN presidenti pres ON s.id_pres = pres.id
                        LEFT JOIN presidenti vice ON s.id_vice = vice.id
                        LEFT JOIN stadio st ON s.id_stadio = st.id
                        WHERE 1=1
                        ";

        if($prezzo !== null) {
            $query .= " AND s.costo_iscrizione <= :prezzo";
        }

        if($rate !== null) {
            $query .= " AND s.rate = :rate";
        }
        // Aggiunta dinamica dei filtri
        if ($vendita_filter !== null) {
            $query .= " AND vendita = :vendita";
        }
        if ($id_squadra_filter !== null) {
            $query .= " AND s.id = :id_squadra";
        }
        if (!empty($search)) {
            $query .= " AND nome_squadra LIKE :search";
        }
        if (!empty($nome_presidente_filter)) {
            $query .= " AND (pres.nome LIKE :nome_presidente OR pres.cognome LIKE :nome_presidente)";
        }

        $stmt = $this->conn->prepare($query);

        if($prezzo !== null) {
            $stmt->bindParam(':prezzo', $prezzo, PDO::PARAM_INT);
        }

        if($rate !== null) {
            $stmt->bindParam(':rate', $rate, PDO::PARAM_INT);
        }

        if ($search) {
            $search_term = "%$search%";
            $stmt->bindParam(':search', $search_term, PDO::PARAM_STR);
        }
        if (!empty($nome_presidente_filter)) {
            $search_term = "%$nome_presidente_filter%";
            $stmt->bindParam(':nome_presidente', $search_term, PDO::PARAM_STR);
        }
        if ($vendita_filter !== null) {
            $stmt->bindParam(':vendita', $vendita_filter, PDO::PARAM_BOOL);
        }
        if ($id_squadra_filter !== null) {
            $stmt->bindParam(':id_squadra', $id_squadra_filter, PDO::PARAM_INT);
        }

        $stmt->execute();
        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        return $row['total'];
    }

    public function create(string $nome_squadra,int $id_pres, int $id_vice= null , bool $vendita, int $id_stadio, int $valore_fvm): bool {

        if (empty($nome_squadra)) {
            throw new InvalidArgumentException("Il nome_squadra non può essere vuoto");
        }
        if ($id_pres <= 0) {
            throw new InvalidArgumentException("ID presidente non valido");
        }
        if ($id_vice <= 0 && $id_vice !== null) {
            throw new InvalidArgumentException("ID vice presidente non valido");
        }
        if ($vendita !== true && $vendita !== false) {
            throw new InvalidArgumentException("Il campo vendita deve essere un booleano");
        }
        if( $id_stadio <= 0) {
            throw new InvalidArgumentException("ID stadio non valido");
        }
        if ($valore_fvm <= 0) {
            throw new InvalidArgumentException("Il valore_fvm deve essere maggiore di zero");
        }

        // Query SQL con named parameters
        $query = "
        INSERT INTO " . $this->table_name . " 
        (nome_squadra, id_pres, id_vice, vendita ,id_stadio, valore_fvm)
        VALUES 
        (:nome_squadra, :id_pres, :id_vice, :vendita, :id_stadio, :valore_fvm)
    ";

        $stmt = $this->conn->prepare($query);

        // Binding parametri con tipi espliciti
        $stmt->bindParam(':nome_squadra', $nome_squadra, PDO::PARAM_STR);
        $stmt->bindParam(':id_pres', $id_pres, PDO::PARAM_INT);
        if ($id_vice === null) {
            $stmt->bindValue(':id_vice', null, PDO::PARAM_NULL);
        } else {
            $stmt->bindValue(':id_vice', $id_vice, PDO::PARAM_INT);
        }
        $stmt->bindParam(':vendita', $vendita, PDO::PARAM_BOOL);
        $stmt->bindParam(':id_stadio', $id_stadio, PDO::PARAM_INT);
        $stmt->bindParam(':valore_fvm', $valore_fvm, PDO::PARAM_INT);

        try {
            return $stmt->execute();
        } catch (PDOException $e) {
            // Log dell'errore (in un sistema reale)
            error_log("Errore durante la creazione della news: " . $e->getMessage());
            return false;
        }
    }

    public function read($prezzo=null,$rate=null,$vendita_filter = null, $search = null, $nome_presidente_filter = null, $id_squadra_filter = null, $limit = 10, $offset = 0)
    {
        $query =  "
                            SELECT 
                                s.id,
                                s.nome_squadra,
                                s.vendita,
                                s.rate,
                                s.costo_iscrizione,
                                s.valore_fvm,
                                s.credito,
                                
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

        if($prezzo !== null) {
            $query .= " AND s.costo_iscrizione <= :prezzo";
            $params[':prezzo'] = $prezzo;
        }

        if ($rate !== null) {
            $query .= " AND s.rate = :rate";
            $params[':rate'] = $rate;
        }

        if ($vendita_filter !== null) {
            $query .= " AND s.vendita = :vendita";
            $params[':vendita'] = $vendita_filter;
        }

        if (!empty($search)) {
            $query .= " AND s.nome_squadra LIKE :search";
            $params['search'] = "%" . $search . "%";
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
        $query .= " LIMIT :limit OFFSET :offset";

        $stmt = $this->conn->prepare($query);

        foreach ($params as $key => $val) {
            $stmt->bindValue($key, $val);
        }
        $stmt->bindParam(':limit', $limit, PDO::PARAM_INT);
        $stmt->bindParam(':offset', $offset, PDO::PARAM_INT);

        if ($stmt->execute()) {
            return $stmt;
        }
        return null;
    }

    public function delete($id)
    {
        $query = "DELETE FROM " . $this->table_name . " WHERE id = :id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);

        try {
            if (!$stmt->execute()) {
                return false;
            }

            // Verifica se effettivamente è stata eliminata una riga
            return ($stmt->rowCount() > 0);

        } catch (PDOException $e) {
            // Log dell'errore (opzionale)
            error_log("Errore eliminazione: " . $e->getMessage());
            return false;
        }
    }

}
