<?php

namespace component;

class database
{

    private $host= 'localhost';
    private $db_name = 'ybarretj_db';
    private $db_user = 'ybarretj_dbu' ;
    private $user_password = 'Q7jM6!]ooD_s';

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