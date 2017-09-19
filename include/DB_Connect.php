<?php
class DB_Connect {
 
    // constructor
    function __construct() {
         
    }
 
    // destructor
    function __destruct() {
        // $this->close();
    }
 
    // Connecting to database
    public function connect() {
        require_once 'Config.php';
        //connecting to mysql
        $con = mysqli_connect(DB_HOST, DB_USER, DB_PASSWORD,DB_DATABASE);
        // Check connection
        if (!$con) {
            die("Connection failed: " . mysqli_connect_error());
        }
        // return database handler
        return $con;
    }
 
    // Closing database connection
    public function close() {
        mysql_close();
        //mysqli_close($con);
    }
 
}


?>
