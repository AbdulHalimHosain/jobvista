<?php 

namespace Framework;

use PDO;
use PDOException;
use PDOStatement;
use Exception;

class Database {
    public $conn;
    /**
     * Constructor for Database class
     * @param array $config
     * 
     */
    public function __construct($config) {
        $dsn = "mysql:host={$config['host']};port={$config['port']};dbname={$config['dbname']}";
        $options =[
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_OBJ
        ];
        try {
            $this->conn = new PDO($dsn, $config['username'], $config['password'], $options);
        } catch (PDOException $e) {
            throw new Exception("Database connection error: {$e->getMessage()}");
        }
    }

    /**
     * Query the database
     * 
     * @param string $query
     * @return PDOStatement
     * @THROWS PDOException
     */
    public function query($query, $params = [])  {
        try {
            $sth = $this->conn->prepare($query);

            //Bind named param
            foreach ($params as $param => $value) {
                $sth->bindValue(':'. $param, $value);
                
            }
            $sth->execute();
            return $sth;
        } catch (PDOException $e) {
            throw new Exception("Database query error: {$e->getMessage()}");

        }
}
}
