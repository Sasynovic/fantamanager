<?php

class associazioni
{

    private $conn;
    private $table_name = "associazioni";

    public $id;
    public $id_squadra;
    public $id_calciatore;
    public $costo_calciatore;

    public function __construct($db)
    {
        $this->conn = $db;
    }

    public function read($id_squadra_filter = null, $fuori_listone_filter = null, $prelazione_filter = null)
    {
        $query = "
            SELECT  
                a.id,
                a.id_squadra AS id_squadra,
                a.id_calciatore,
                a.costo_calciatore,
                a.n_movimenti,
                a.scambiato,
                a.prelazione,
                
                s.nome_squadra AS nome_squadra,
                
                c.nome AS nome_calciatore,
                c.squadra AS nome_squadra_calciatore,
               c.fvm,
                c.eta,
                c.ruolo AS ruolo_calciatore,
                c.fuori_listone AS fuori_listone
            FROM  " . $this->table_name . " a
            LEFT JOIN squadre s ON a.id_squadra = s.id
            LEFT JOIN calciatori c ON a.id_calciatore = c.id";

        $conditions = [];
        $params = [];

        if ($id_squadra_filter !== null) {
            $conditions[] = "a.id_squadra = :id_squadra";
            $params[':id_squadra'] = $id_squadra_filter;
        }

        if ($fuori_listone_filter !== null) {
            $conditions[] = "c.fuori_listone = :fuori_listone";
            $params[':fuori_listone'] = $fuori_listone_filter;
        }

        if ($prelazione_filter !== null) {
            $conditions[] = "a.prelazione = :prelazione";
            $params[':prelazione'] = $prelazione_filter;
        }

        if (count($conditions) > 0) {
            $query .= " WHERE " . implode(" AND ", $conditions);
        }

        $query .= " ORDER BY a.id_squadra, c.ruolo, c.nome";

        $stmt = $this->conn->prepare($query);

        // Bind dei parametri
        foreach ($params as $key => $val) {
            $stmt->bindValue($key, $val, is_int($val) ? PDO::PARAM_INT : PDO::PARAM_STR);
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
            'id_squadra' => PDO::PARAM_INT,
            'scambiato' => PDO::PARAM_INT,
            'n_movimenti' => PDO::PARAM_INT,
            'prelazione' => PDO::PARAM_INT
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

        $query = "UPDATE " . $this->table_name . " 
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

}
           

