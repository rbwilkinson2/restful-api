<?php

/**
 * Description of DbHandler
 *
 * @author Robert Wilkinson
 */

class DbHandler {
    
    private $conn;
    
    public function __construct() {
        require_once dirname(__FILE__). 'DbConnect.php';
            //open database connection
        $db = new DBConnect();
        $this->conn = $db->connect();
        }
        
        /** Users table method **/
        
        /**  
         * Creating new user
         * @param String $name User full name
         * @param String $email User login email id
         * @param String $password User login password
         * */
        
        public function createUser($name, $email, $password) {
            require_once 'PassHash.php';  
            $response = array();
            //Check if user already exists in DB
            if(!$this->isUserExists($email)) {
                //Generate password hash
                $password_hash = PassHash::hash($password);
                //Generate api key
                $api_key = $this->generateApiKey();
                
                //insert query
                $stmt = $this->conn->prepare("INSERT INTO users(name, email, password_hash, api_key, status) values(?, ?, ?, ?, ?, ?, 1)");
                $stmt->bind_param('ssss', $name, $email, $password_hash, $api_key);
                
                $result = $stmt->execute();
                $stmt->close();
                
               //Insertion check
               if ($result) {
                   //User successfully inserted
                   return USER_CREATED_SUCCESSFULLY;
                   } else {
                   //Failed to create user
                   return USER_CREATE_FAILED;    
                   }
            } else {
                // Email address already exist
                return USER_ALREADY_EXISTS;    
            }
            return $response;
        }
        
            /**
         * Checking for duplicate user by email address
         * @param String $email email to check in db
         * @return boolean
         */
        private function isUserExists($email) {
            $stmt = $this->conn->prepare("SELECT id from users WHERE email = ?");
            $stmt->bind_param("s", $email);
            $stmt->execute();
            $stmt->store_result();
            $num_rows = $stmt->num_rows;
            $stmt->close();
            return $num_rows > 0;
        }
       
        /**
         * Fetch user by email
         * @param String $email User email id
         */
        public function getUserByEmail($email) {
            $stmt = $this->conn->prepare("SELECT name, email, api_key, status, created_at FROM users WHERE email = ?");
            $stmt->bind_param("s", $email);
            if ($stmt->execute()) {
                $user = $stmt->get_result()->fetch_assoc();
                $stmt->close();
                return $user;
            } else {
                return NULL;
            }
        }
        
        /**
         * Fetch user api key
         * @param String $user_id user id primary key in user table
         */
        public function getApiKeyById($user_id) {
            $stmt = $this->conn->prepare("SELECT api_key FROM users WHERE id = ?");
            $stmt->bind_param("i", $user_id);
            if ($stmt->execute()) {
                $api_key = $stmt->get_result()->fetch_assoc();
                $stmt->close();
                return $api_key;
            } else {
                return NULL;
            }
        }
     
        /**
         * Fetching user id by api key
         * @param String $api_key user api key
         */
        public function getUserId($api_key) {
            $stmt = $this->conn->prepare("SELECT id FROM users WHERE api_key = ?");
            $stmt->bind_param("s", $api_key);
            if ($stmt->execute()) {
                $user_id = $stmt->get_result()->fetch_assoc();
                $stmt->close();
                return $user_id;
            } else {
                return NULL;
            }
        }
        
        /**
         * Validating user api key
         * If the api key is there in db, it is a valid key
         * @param String $api_key user api key
         * @return boolean
         */
        public function isValidApiKey($api_key) {
            $stmt = $this->conn->prepare("SELECT id FROM users WHERE api_key = ?");
            $stmt->bind_param("s", $api_key);
            $stmt->execute();
            $stmt->store_result();
            $num_rows = $stmt->num_rows;
            $stmt->close();
            return $num_rows > 0;
        }
        
        /**
         * Generating random Unique MD5 String for user Api key
         */
        private function generateApiKey() {
            return md5(uniqid(rand(), true));
        }
        
        /**
         * Create unique url for email link
         */
        private function createLinkHash($created_at){
            $achieved = ($created_at);
            $link_hash = crypt($achieved);
            return $link_hash;
        }
        
        /**
         * Get unique url for email link
         * @param String 
         */
        private function getLinkHashAsUrl($link_hash) {
            $stmt = $this->conn->prepare("SELECT link_hash FROM achieved WHERE created_at = ?");
            $stmt->bind_param("sss", $link_hash);
            if($stmt->execute()) {
                $unique_link = $stmt->get_result()->fetch_assoc();
                $stmt->close();
                return $unique_link;
            } else {
                return NULL;
            }
            
        } 
  }
