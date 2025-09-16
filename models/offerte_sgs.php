<?php

class offerte_sgs
{

    private $conn;

    public $id;
    public $nome_squadra;

    public function __construct($db)
    {
        $this->conn = $db;
    }

    public function create(int $id_squadra, int $id_associazione_g, int $valore_offerta): bool {

        // Validazione input
        if (!is_int($id_squadra) || $id_squadra <= 0) {
            throw new InvalidArgumentException("L'ID della squadra non è valido");
        }
        if (!is_int($id_associazione_g) || $id_associazione_g <= 0) {
            throw new InvalidArgumentException("L'ID dell'associazione non è valido");
        }
        if (!is_int($valore_offerta) || $valore_offerta < 0) {
            throw new InvalidArgumentException("Il valore dell'offerta non è valido");
        }

        // Query SQL con named parameters
        $query = "INSERT INTO offerte_g
        (id_squadra, id_associazione_g,  valore_offerta)
        VALUES 
        (:id_squadra, :id_associazione_g, :valore_offerta)";

        $stmt = $this->conn->prepare($query);

        // Binding parametri con tipi espliciti
        $stmt->bindParam(':id_squadra', $id_squadra, PDO::PARAM_INT);
        $stmt->bindParam(':id_associazione_g', $id_associazione_g, PDO::PARAM_INT);
        $stmt->bindParam(':valore_offerta', $valore_offerta, PDO::PARAM_INT);

        try {
            return $stmt->execute();
        } catch (PDOException $e) {
            http_response_code(400);
            echo json_encode([
                "message" => "Errore DB: " . $e->getMessage(),
                "success" => false
            ]);
            return false;
        }
    }

    public function read($assegnato = null,$id_divisione = null, $id_squadra = null, $ruolo = null, $squadra_reale = null)
    {
        $query = "SELECT 
            ag.id,
            ag.id_calciatore_g,
            ag.id_divisione,
            
            cg.cognome,
            cg.nome,
            cg.squadra,
            cg.ruolo,
            
            d.nome_divisione,
              
            og.id AS id_offerta,
            og.id_squadra,
            og.id_associazione_g,
            og.valore_offerta,
            og.assegnato
            
            FROM associazioni_g ag
            LEFT JOIN calciatori_g  cg ON ag.id_calciatore_g = cg.id
            LEFT JOIN divisione d ON d.id = ag.id_divisione
            LEFT JOIN offerte_g og ON og.id_associazione_g = ag.id
            WHERE 1=1";

        // Aggiunta dinamica dei filtri
        $params = [];
        if ($id_divisione !== null) {
            $query .= " AND ag.id_divisione = :id_divisione";
            $params[':id_divisione'] = $id_divisione;
        }
        if ($id_squadra !== null) {
            $query .= " AND og.id_squadra = :id_squadra";
            $params[':id_squadra'] = $id_squadra;
        }
        if ($ruolo !== null) {
            $query .= " AND cg.ruolo = :ruolo";
            $params[':ruolo'] = $ruolo;
        }
        if ($squadra_reale !== null) {
            $query .= " AND cg.squadra = :squadra_reale";
            $params[':squadra_reale'] = $squadra_reale;
        }
        if ($assegnato !== null) {
            $query .= " AND og.assegnato = :assegnato";
            $params[':assegnato'] = (int)$assegnato;
        }
        $stmt = $this->conn->prepare($query);
        //Bind dei parametri
        foreach ($params as $key => $value) {
            $stmt->bindValue($key, $value);
        }
        $stmt->execute();
        return $stmt;
    }

    public function update($id, $data): bool
    {
        // Validazione dell'ID
        if (!is_numeric($id) || $id <= 0) {
            throw new InvalidArgumentException("L'ID della trattativa non è valido");
        }

        // Verifica che $data sia un array
        if (!is_array($data)) {
            throw new InvalidArgumentException("I dati per l'aggiornamento devono essere un array");
        }

        // Costruzione della query dinamica
        $fields = [];
        $params = [':id' => $id];

        // Lista dei campi aggiornabili
        $allowedFields = [
            'valore_offerta' => PDO::PARAM_INT,
        ];

        foreach ($allowedFields as $field => $type) {
            if (isset($data[$field])) {
                $fields[] = "$field = :$field";
                $params[":$field"] = $data[$field];
            }
        }

        if (empty($fields)) {
            return false;
        }

        $query = "UPDATE offerte_g
              SET " . implode(', ', $fields) . " 
              WHERE id = :id";

        $stmt = $this->conn->prepare($query);

        // Bind dei parametri
        foreach ($params as $key => $value) {
            $type = $allowedFields[str_replace(':', '', $key)] ?? PDO::PARAM_STR;
            $stmt->bindValue($key, $value, $type);
        }

        try {
            $stmt->execute();
            return ($stmt->rowCount() > 0);
        } catch (PDOException $e) {
            error_log("Errore durante l'aggiornamento della trattativa: " . $e->getMessage());
            return false;
        }
    }


    public function delete($id)
    {
        $query = "DELETE FROM offerte_g WHERE id = :id";
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

