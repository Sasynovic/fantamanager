<?php

class trattative
{

    private $conn;
    private $table_name = "trattative";

    public $id;
    public function __construct($db)
    {
        $this->conn = $db;
    }

    public function count($id_squadra=null,$id_trattativa=null,$ufficializzata=null)
    {
        $query = "SELECT COUNT(*) as total FROM " . $this->table_name . " WHERE 1=1";

        if( $id_squadra !== null) {
            $query .= " AND id_squadra1 = :idSquadra OR id_squadra2 = :idSquadra";
        }

        if( $id_trattativa !== null) {
            $query .= " AND id = :idTrattativa";
        }

        if ($ufficializzata !== null) {
            $query .= " AND ufficializzata = :ufficializzata";
        }


        $stmt = $this->conn->prepare($query);

        if ($id_squadra !== null) {
            $stmt->bindParam(':idSquadra', $id_squadra, PDO::PARAM_INT);
        }

        if ($id_trattativa !== null) {
            $stmt->bindParam(':idTrattativa', $id_trattativa, PDO::PARAM_INT);
        }
        if ($ufficializzata !== null) {
            $stmt->bindParam(':ufficializzata', $ufficializzata, PDO::PARAM_BOOL);
        }


        $stmt->execute();
        $row = $stmt->fetch(PDO::FETCH_ASSOC);


        return $row['total'];
    }

    public function create(int $id_competizione,int  $id_squadra1, int  $id_squadra2, string $descrizione ){
        if ($id_competizione<= 0) {
            throw new InvalidArgumentException("Il id_competizione non può essere vuoto");
        }
        if ($id_squadra1<= 0) {
            throw new InvalidArgumentException("Il id_squadra1 non può essere vuoto");
        }
        if ($id_squadra2<= 0) {
            throw new InvalidArgumentException("Il id_squadra2 non può essere vuoto");
        }if (empty($descrizione)) {
            $descrizione = null;
        }


        $query = "INSERT INTO " . $this->table_name . " 
        (id_competizione,id_squadra1,id_squadra2,descrizione)
        VALUES 
        (:id_competizione,:id_squadra1,:id_squadra2,:descrizione)";

        $stmt = $this->conn->prepare($query);

        $stmt -> bindParam(':id_competizione', $id_competizione, PDO::PARAM_INT);
        $stmt -> bindParam(':id_squadra1', $id_squadra1, PDO::PARAM_INT);
        $stmt -> bindParam(':id_squadra2', $id_squadra2, PDO::PARAM_INT);
        $stmt -> bindParam(':descrizione', $descrizione, PDO::PARAM_STR);

        try {
            $stmt->execute();
            return $this->conn->lastInsertId();
        } catch (PDOException $e) {
            error_log("Errore durante la creazione del presidente: " . $e->getMessage());
            return false;
        }

    }

    public function read($id_squadra=null,$id_trattativa=null,$ufficializzata=null,$limit = 10, $offset = 0)
    {
        $query = "SELECT
            t.id,
            t.descrizione,
            t.id_competizione,
            t.id_squadra1,
            t.id_squadra2,
            t.ufficializzata,
            t.data_creazione,
            
            s1.nome_squadra as nome_squadra1,
            s2.nome_squadra as nome_squadra2
         
              FROM " . $this->table_name . " t
              LEFT JOIN squadre s1 ON t.id_squadra1 = s1.id
              LEFT JOIN squadre s2 ON t.id_squadra2 = s2.id
              WHERE 1=1";
        if( $id_squadra !== null) {
            $query .= " AND (t.id_squadra1 = :idSquadra OR t.id_squadra2 = :idSquadra)";
        }

        if ($id_trattativa !== null) {
            $query .= " AND t.id = :idTrattativa";
        }
        if ($ufficializzata !== null) {
            $query .= " AND t.ufficializzata = :ufficializzata";
        }

        $query .= " ORDER BY t.id ASC";
        $query .= " LIMIT :limit OFFSET :offset";

        $stmt = $this->conn->prepare($query);

        $stmt->bindParam(':limit', $limit, PDO::PARAM_INT);
        $stmt->bindParam(':offset', $offset, PDO::PARAM_INT);
        if ($id_squadra !== null) {
            $stmt->bindParam(':idSquadra', $id_squadra, PDO::PARAM_INT);
        }
        if ($id_trattativa !== null) {
            $stmt->bindParam(':idTrattativa', $id_trattativa, PDO::PARAM_INT);
        }
        if ($ufficializzata !== null) {
            $stmt->bindParam(':ufficializzata', $ufficializzata, PDO::PARAM_BOOL);
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
            'descrizione' => PDO::PARAM_STR,
            'id_competizione' => PDO::PARAM_INT,
            'id_squadra1' => PDO::PARAM_INT,
            'id_squadra2' => PDO::PARAM_INT,
            'ufficializzata' => PDO::PARAM_BOOL
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

    public function delete($id): bool
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