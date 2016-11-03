<?php
/**
 * Created by PhpStorm.
 * User: umarbradshaw
 * Date: 11/1/15
 * Time: 1:29 PM
 */

require_once 'include/DB_Functions.php';
$db = new DB_Functions();

$email = $_GET['email'];
$code = $_GET['code'];

if ($db->emailConfirmation($email,$code)) {
    echo "Your email has been confirmed. You can now log into Addlas. Thank you";
}else{
    echo "There was a problem with email verification code $code. Sorry.";
}


