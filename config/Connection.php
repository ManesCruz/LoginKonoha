<?php
    class Connection {
        private $host = 'localhost';
        private $port =  '3308'; // Cambia esto si tu puerto es diferente
        private $dbname = 'login_konoha';   
        private $username = 'root';     
        private $password = ""; 

        public function getConnection(){
            try {
                $dsn ="mysql:host={$this->host};port={$this->port};dbname={$this->dbname}";
                $options = [
                    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                    PDO::ATTR_EMULATE_PREPARES => false,
                ];
                return new PDO($dsn, $this->username, $this->password, $options);
                
            } catch (PDOException $e) {
                echo "conexion fallida " . $e->getMessage();
                exit;
            }
        }
    }