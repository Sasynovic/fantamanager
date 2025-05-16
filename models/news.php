<?php

class news
{
    private $conn;
    private $table_name = "news";

    public $id;

    public function __construct($db)
    {
        $this->conn = $db;
    }

    public function count($visibile=null,$id=null,$id_competizione = null, $search = null)
    {
        $query = "SELECT COUNT(*) as total 
                        FROM " . $this->table_name . " n
                        LEFT JOIN competizione c ON n.id_competizione = c.id
                        LEFT JOIN divisione d ON c.id_divisione = d.id
                         WHERE 1=1";

        if ($visibile !== null) {
            $query .= " AND n.visibile = :visibile";
        }
        if ($id_competizione !== null) {
            $query .= " AND n.id_competizione = :id_competizione";
        }
        if ($search !== null) {
            $query .= " AND (n.titolo LIKE :search OR n.contenuto LIKE :search)";
        }
        if ($id !== null) {
            $query .= " AND n.id = :id";
        }


        $stmt = $this->conn->prepare($query);

        if ($visibile !== null) {
            $stmt->bindParam(':visibile', $visibile, PDO::PARAM_BOOL);
        }

        if ($id !== null) {
            $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        }
        if ($search) {
            $search_term = "%$search%";
            $stmt->bindParam(':search', $search_term, PDO::PARAM_STR);
        }
        if ($id_competizione !== null) {
            $stmt->bindParam(':id_competizione', $id_competizione, PDO::PARAM_INT);
        }


        $stmt->execute();
        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        return $row['total'];
    }

    public function create(string $titolo,string $contenuto,int $id_competizione,string $autore,bool $visibile = true): bool {

        if (empty($titolo)) {
            throw new InvalidArgumentException("Il titolo non può essere vuoto");
        }
        if (empty($contenuto)) {
            throw new InvalidArgumentException("Il contenuto non può essere vuoto");
        }
        if ($id_competizione <= 0) {
            throw new InvalidArgumentException("ID competizione non valido");
        }
        if (empty($autore)) {
            throw new InvalidArgumentException("L'autore non può essere vuoto");
        }

        // Query SQL con named parameters
        $query = "
        INSERT INTO " . $this->table_name . " 
        (titolo, contenuto, autore, id_competizione, visibile, data_pubblicazione)
        VALUES 
        (:titolo, :contenuto, :autore, :id_competizione, :visibile, NOW())
    ";

        $stmt = $this->conn->prepare($query);

        // Binding parametri con tipi espliciti
        $stmt->bindParam(':titolo', $titolo, PDO::PARAM_STR);
        $stmt->bindParam(':contenuto', $contenuto, PDO::PARAM_STR);
        $stmt->bindParam(':autore', $autore, PDO::PARAM_STR);
        $stmt->bindParam(':id_competizione', $id_competizione, PDO::PARAM_INT);
        $stmt->bindParam(':visibile', $visibile, PDO::PARAM_BOOL);

        try {
            return $stmt->execute();
        } catch (PDOException $e) {
            // Log dell'errore (in un sistema reale)
            error_log("Errore durante la creazione della news: " . $e->getMessage());
            return false;
        }
    }

    public function read($visibile=null,$id=null,$id_competizione = null, $search = null, $limit = null, $offset = 0) {
        $query = "
                        SELECT  
                            n.id,
                            n.titolo,
                            n.contenuto,
                            n.data_pubblicazione,
                            n.autore,
                            n.id_competizione,
                            n.visibile,
                            c.nome_competizione,
                            d.nome_divisione
                        
                        FROM " . $this->table_name . " n
                        LEFT JOIN competizione c ON n.id_competizione = c.id
                        LEFT JOIN divisione d ON c.id_divisione = d.id
                        WHERE 1=1
                    ";

        if ($visibile !== null) {
            $query .= " AND n.visibile = :visibile";
        }

        if ($id !== null) {
            $query .= " AND n.id = :id";
        }

        if ($id_competizione !== null) {
            $query .= " AND n.id_competizione = :id_competizione";
        }
        if ($search !== null) {
            $query .= " AND (n.titolo LIKE :search OR n.contenuto LIKE :search)";
        }

        $query .= " ORDER BY n.data_pubblicazione DESC";
        $query .= " LIMIT :limit OFFSET :offset";

        $stmt = $this->conn->prepare($query);

        if ($visibile !== null) {
            $stmt->bindParam(':visibile', $visibile, PDO::PARAM_BOOL);
        }

        if ($id !== null) {
            $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        }
        if ($id_competizione !== null) {
            $stmt->bindParam(':id_competizione', $id_competizione, PDO::PARAM_INT);
        }
        if ($search !== null) {
            $search = "%$search%";
            $stmt->bindParam(':search', $search, PDO::PARAM_STR);
        }

        $stmt->bindParam(':limit', $limit, PDO::PARAM_INT);
        $stmt->bindParam(':offset', $offset, PDO::PARAM_INT);

        if ($stmt->execute()) {
            return $stmt;
        }

        return null;
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
            'titolo' => PDO::PARAM_STR,
            'contenuto' => PDO::PARAM_STR,
            'autore' => PDO::PARAM_STR,
            'data_pubblicazione' => PDO::PARAM_INT,
            'id_competizione' => PDO::PARAM_INT,
            'visibile' => PDO::PARAM_BOOL
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

    public function delete($id) {
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