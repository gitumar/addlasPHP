<?php

class DB_Functions {
 
    private $db;
    
    // constructor
    function __construct() {
        require_once 'DB_Connect.php';
        // connecting to database
        $this->db = new DB_Connect();
        $this->db->connect();
    }
 
    // destructor
    function __destruct() {
         
    }

    /**
     * Storing new user
     * returns user details
     * @param $first_name
     * @param $last_name
     * @param $email
     * @param $password
     * @param $confirmed_code
     * @param $device
     * @return array|bool|null
     */
    public function storeUser($first_name, $last_name, $email, $password, $confirmed_code, $device) {
        $uid = uniqid('', true);
        $hash = $this->hashSSHA($password);
        $encrypted_password = $hash["encrypted"]; // encrypted password
        $salt = $hash["salt"]; // salt
        //$confirmed_code = mt_rand(2,9999999999);


        $link = $this->db->connect();
        $result = mysqli_query($link,"INSERT INTO myaddlasusers(unique_id, firstname, lastname, email, encrypted_password, salt, confirmed, confirmed_code, created_at, device) VALUES('$uid', '$first_name',
                              '$last_name', '$email', '$encrypted_password', '$salt', '0','$confirmed_code', NOW(), '$device')");

        // check for successful store
        if ($result) {
            // get user details 
            $uid = mysqli_insert_id($link); // last inserted id
            mysqli_query($link, "SELECT * FROM myaddlasusers WHERE unique_id = '$uid'");
            //return details
            return mysqli_fetch_array($result);
        } else {
            return false;
        }
    }
 
    /**
     * Get user by email and password
     */
    public function getUserByEmailAndPassword($email, $password) {
        $link = $this->db->connect();
        $result = mysqli_query($link,"SELECT * FROM myaddlasusers WHERE email = '$email'");
        // check for result 
        $no_of_rows = mysqli_num_rows($result);
        if ($no_of_rows > 0) {
            $result = mysqli_fetch_array($result);
            $confirmed = $result['confirmed'];

            if($confirmed == 0){
                return false;
            }

            $salt = $result['salt'];
            $encrypted_password = $result['encrypted_password'];
            $hash = $this->checkhashSSHA($salt, $password);
            // check for password equality
            if ($encrypted_password == $hash) {
                // user authentication details are correct
                return $result;
            }
        } else {
            // user not found
            return false;
        }
    }

    /**
     * Check user is existed or not
     */
    public function isUserExisted($email) {
        $link = $this->db->connect();
        $result = mysqli_query($link,"SELECT email from myaddlasusers WHERE email = '$email'");
        $no_of_rows = mysqli_num_rows($result);
        if ($no_of_rows > 0) {
            // user existed 
            return true;
        } else {
            // user not existed
            return false;
        }
    }
 
    /**
     * Encrypting password
     * @param password
     * returns salt and encrypted password
     */
    public function hashSSHA($password) {
 
        $salt = sha1(rand());
        $salt = substr($salt, 0, 10);
        $encrypted = base64_encode(sha1($password . $salt, true) . $salt);
        $hash = array("salt" => $salt, "encrypted" => $encrypted);
        return $hash;
    }
 
    /**
     * Decrypting password
     * @param salt, password
     * returns hash string
     */
    public function checkhashSSHA($salt, $password) {
 
        $hash = base64_encode(sha1($password . $salt, true) . $salt);
 
        return $hash;
    }
    
       /**
     * Random string which is sent by mail to reset password
     */
 
    public function random_string()
    {
        $character_set_array = array();
        $character_set_array[] = array('count' => 7, 'characters' => 'abcdefghijklmnopqrstuvwxyz');
        $character_set_array[] = array('count' => 1, 'characters' => '0123456789');
        $temp_array = array();
        foreach ($character_set_array as $character_set) {
            for ($i = 0; $i < $character_set['count']; $i++) {
                $temp_array[] = $character_set['characters'][rand(0, strlen($character_set['characters']) - 1)];
            }
        }
        shuffle($temp_array);
        return implode('', $temp_array);
    }
     
    public function forgotPassword($forgotpassword, $newpassword, $salt){
        $link = $this->db->connect();
        $result = mysqli_query($link,"UPDATE myaddlasusers SET encrypted_password = '$newpassword', salt = '$salt' WHERE email = '$forgotpassword'");
     
        if ($result) {
     
            return true;
     
        }else{
            return false;
        }
     
    }
    
    public function getUserName($email){
        $link = $this->db->connect();
        $result = mysqli_query($link,"Select firstname FROM myaddlasusers WHERE email = '$email'");
        $row = mysqli_fetch_array($result);
        
        //$username_return = $row[2];
        
        //return $username_return;
        return $result;
        
    }

    /**
     * @param $email
     * @param $code
     * @return bool
     */
    public function emailConfirmation($email,$code){
        $link = $this->db->connect();
        $query = mysqli_query($link, "SELECT confirmed_code FROM myaddlasusers WHERE email = '$email'");
        //while($row = mysqli_fetch_assoc($query)){
        $row = mysqli_fetch_assoc($query);
        $db_code = $row['confirmed_code'];
        //}
        if($code==$db_code){
            mysqli_query($link,"UPDATE myaddlasusers SET confirmed = '1'");
            mysqli_query($link,"UPDATE myaddlasusers SET confirmed_code = '0'");
            return true;
            //echo "Your email has been confirmed. You can now log into Addlas Thank you";
        }
        else{
            return false ;
        }
    }


}




?>
