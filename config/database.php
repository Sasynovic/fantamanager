<?php

namespace component;

class database
{

    private $host= 'localhost';
    private $db_name = '-';
    private $db_user = '-' ;
    private $user_password = '-';

    private $conn = null;

    public function __construct()
    { }

    public function getConnection()
    {
        if ($this->conn != null) {
            return $this->conn;
        }
        try {
            $this->conn = new \PDO("mysql:host=" . $this->host . ";dbname=" . $this->db_name, $this->db_user, $this->user_password);
            $this->conn->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION);
        } catch (\PDOException $exception) {
            echo "Connection error: " . $exception->getMessage();
        }

        return $this->conn;
    }

}