<?php

/**
 * Description of DBConnect
 * Database connection class
 * @author robert wilkinson
 */
class DBConnect {
    private $conn;
    
    public function __construct() {  
    }
    
    /**
     * Establishing database connection
     * @return database connection handler
     **/
    public function connect() {
        include_once dirname(__FILE__) . './Config.php';
        
        //connect to MySQL database
        try {
            $dbh = new PDO('DB_DSN', 'DB_USERNAME', 'DB_PASSWORD');
        } catch (PDOException $e) {
            echo 'Connection failed: ' . $e->getMessage();
        }
        
    }
}
