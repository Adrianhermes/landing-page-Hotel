<?php
/**
 * Conexão com Banco de Dados usando PDO
 */

class Conexao {
    private $host = 'localhost'; 
    private $dbname = 'hotel_reservas';
    private $username = 'root';
    private $password = 'root';
    private $pdo;


    public function __construct() {


        try {
            $this->pdo = new PDO(
                "mysql:host={$this->host};dbname={$this->dbname};charset=utf8", 
                $this->username, 
                $this->password,
                [
                    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,

                ]
            );
        } catch(PDOException $e) {
            die("Erro na conexão: " . $e->getMessage());
        }
    }


    public function getPdo() {
        return $this->pdo;
    }

}