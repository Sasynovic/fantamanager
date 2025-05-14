<?php

class operazioni
{

    private $conn;
    private $table_name = "operazioni";

    public $id;
    public function __construct($db)
    {
        $this->conn = $db;
    }

    public function count($id_trattativa = null)
    {
        $query = "SELECT COUNT(*) as total FROM " . $this->table_name . " WHERE 1=1";
        if ($id_trattativa) {
            $query .= " AND id_trattativa = :id_trattativa";
        }


        $stmt = $this->conn->prepare($query);
        if ($id_trattativa) {
            $stmt->bindParam(':id_trattativa', $id_trattativa, PDO::PARAM_INT);
        }

        $stmt->execute();
        $row = $stmt->fetch(PDO::FETCH_ASSOC);


        return $row['total'];
    }

    public function create(int  $id_trattativa,int  $id_associazione, int  $id_tipologia_scambio,int $id_squadra_c, int $id_squadra_r, int $valore_riscatto=null){
        if (empty($id_trattativa)) {
            throw new InvalidArgumentException("Il id_trattativa non può essere vuoto");
        }
        if (empty($id_associazione)) {
            throw new InvalidArgumentException("Il id_associazione non può essere vuoto");
        }
        if (empty($id_tipologia_scambio)) {
            throw new InvalidArgumentException("Il id_tipologia_scambio non può essere vuoto");
        }
        if (empty($id_squadra_c)) {
            throw new InvalidArgumentException("Il id_squadra_c non può essere vuoto");
        }
        if (empty($id_squadra_r)) {
            throw new InvalidArgumentException("Il id_squadra_r non può essere vuoto");
        }


        $query = "INSERT INTO " . $this->table_name . " 
        (id_trattativa,id_associazione,id_tipologia_scambio,valore_riscatto,id_squadra_c,id_squadra_r)
        VALUES 
        (:id_trattativa,:id_associazione,:id_tipologia_scambio,:valore_riscatto,:id_squadra_c,:id_squadra_r)";

        $stmt = $this->conn->prepare($query);

        $stmt -> bindParam(':id_trattativa', $id_trattativa, PDO::PARAM_INT);
        $stmt -> bindParam(':id_associazione', $id_associazione, PDO::PARAM_INT);
        $stmt -> bindParam(':id_tipologia_scambio', $id_tipologia_scambio, PDO::PARAM_INT);
        $stmt -> bindParam(':valore_riscatto', $valore_riscatto, PDO::PARAM_INT);
        $stmt -> bindParam(':id_squadra_c', $id_squadra_c, PDO::PARAM_INT);
        $stmt -> bindParam(':id_squadra_r', $id_squadra_r, PDO::PARAM_INT);

        try {
            return $stmt->execute();
        } catch (PDOException $e) {
            error_log("Errore durante la creazione del presidente: " . $e->getMessage());
            return false;
        }

    }

    public function read($id_trattativa = null,$limit = null, $offset = null)
    {
        $query = "SELECT
                o.id,
                o.id_trattativa,
                o.id_associazione,
                o.id_tipologia_scambio,
                o.valore_riscatto,
                
                
                t.descrizione AS descrizione_trattativa,
                t.id_competizione AS id_competizione,
                t.ufficializzata AS ufficializzata,
                t.data_creazione,
                
                o.id_squadra_c AS id_squadra_c,
                o.id_squadra_r AS id_squadra_r,
                s1.nome_squadra AS nome_squadra1,
                s2.nome_squadra AS nome_squadra2,
                
                a.id_calciatore,
                a.n_movimenti,
                a.scambiato,
                
                c.nome AS nome_calciatore,
                
                m.nome AS nome_metodo_scambio,
                
                fs.nome AS nome_finestra_mercato,
                fs.data_inizio AS data_inizio_finestra,
                fs.data_fine AS data_fine_finestra
                
                
              FROM " . $this->table_name . " o
              
              LEFT JOIN trattative t ON o.id_trattativa = t.id
              
              LEFT JOIN squadre s1 ON  o.id_squadra_c = s1.id
              LEFT JOIN squadre s2 ON o.id_squadra_r = s2.id
              
              LEFT JOIN associazioni a ON o.id_associazione = a.id
              
              LEFT JOIN calciatori c ON a.id_calciatore =c.id
              
              LEFT JOIN tipologia_scambio ts ON o.id_tipologia_scambio = ts.id
              
              LEFT JOIN finestra_mercato fs ON ts.id_finestra_mercato  = fs.id
              
              LEFT JOIN metodi_scambio m ON ts.id_metodo = m.id
             
              WHERE 1=1";

        if ($id_trattativa) {
            $query .= " AND o.id_trattativa = :id_trattativa";
        }

        $query .= " ORDER BY o.id ASC";
        // Aggiungi la limitazione e l'offset solo se sono stati forniti
        if ($limit !== null && $offset !== null) {
            $limit = (int)$limit;
            $offset = (int)$offset;
            $query .= " LIMIT :limit OFFSET :offset";
        }

        $stmt = $this->conn->prepare($query);

        // Se sono stati forniti limit e offset, bindali come parametri
        if ($limit !== null && $offset !== null) {
            $stmt->bindParam(':limit', $limit, PDO::PARAM_INT);
            $stmt->bindParam(':offset', $offset, PDO::PARAM_INT);
        }

        if ($id_trattativa) {
            $stmt->bindParam(':id_trattativa', $id_trattativa, PDO::PARAM_INT);
        }

        $stmt->execute();

        return $stmt;
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