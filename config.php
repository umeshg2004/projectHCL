<?php
class Database {
    private $mysql_host = 'localhost';
    private $mysql_username = 'root';
    private $mysql_password = '';
    private $mysql_database = 'user_system';
    
    private $mongodb_host = 'localhost';
    private $mongodb_port = 27017;
    private $mongodb_database = 'user_profiles';
    
    private $redis_host = '127.0.0.1';
    private $redis_port = 6379;
    
    public $mysql_conn;
    public $mongodb_conn;
    public $redis_conn;
    
    public function __construct() {
        $this->connectMySQL();
        $this->connectMongoDB();
        $this->connectRedis();
    }
    
    private function connectMySQL() {
        try {
            $this->mysql_conn = new PDO("mysql:host={$this->mysql_host};dbname={$this->mysql_database}", 
                                       $this->mysql_username, $this->mysql_password);
            $this->mysql_conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        } catch(PDOException $e) {
            die("MySQL Connection failed: " . $e->getMessage());
        }
    }
    
    private function connectMongoDB() {
        try {
            $this->mongodb_conn = new MongoDB\Driver\Manager("mongodb://{$this->mongodb_host}:{$this->mongodb_port}");
        } catch(Exception $e) {
            die("MongoDB Connection failed: " . $e->getMessage());
        }
    }
    
    private function connectRedis() {
        try {
            $this->redis_conn = new Redis();
            $this->redis_conn->connect($this->redis_host, $this->redis_port);
        } catch(Exception $e) {
            die("Redis Connection failed: " . $e->getMessage());
        }
    }
}
?>