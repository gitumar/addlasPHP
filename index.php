<?php

/**
 * File to handle all API requests
 * Accepts GET and POST
 * 
 * Each request will be identified by TAG
 * Response will be JSON data
 
  /**
 * check for POST request 
 */
if (isset($_POST['tag']) && $_POST['tag'] != '') {
    // get tag
    $tag = $_POST['tag'];
 
    // include db handler
    require_once 'include/DB_Functions.php';
    $db = new DB_Functions();
 
    // response Array
    $response = array("tag" => $tag, "error" => FALSE);
    
    if ($tag == 'forgotpass'){
        $forgot_password = $_POST['forgotpassword'];
         
        $randomcode = $db->random_string();
        $hash = $db->hashSSHA($randomcode);
        $encrypted_password = $hash["encrypted"]; // encrypted password
        $salt = $hash["salt"];
        $subject = "Password Recovery";
        
        $from = "zeroguiltllc.com";
        $headers = "From:" . $from;
        if ($db->isUserExisted($forgot_password)) {
            //$username = $db->getUserName($forgot_password);
            
            //$message = "Hello $username,\nYour password has been sucessfully changed. Your new password is $randomcode . Login with your new password to change it. \n\nRegards,\nZero Guilt.";

            $message = "Hello,\nYour password has been sucessfully changed. Your new password is $randomcode . Login with your new password to change it. \n\nRegards,\nZero Guilt.";


            $user = $db->forgotPassword($forgot_password, $encrypted_password, $salt);
            if ($user) {
                $response["success"] = 1;
                mail($forgot_password,$subject,$message,$headers);
                echo json_encode($response);
            }else {
                $response["error"] = 1;
                echo json_encode($response);
            }
                 // user is already existed - error response
        }else {
         
            $response["error"] = 2;
            $response["error_msg"] = "User does not exist";
            echo json_encode($response);
         
        }
 
    }// check for tag type
    elseif ($tag == 'login') {
        // Request type is check Login
        $email = $_POST['email'];
        $password = $_POST['password'];
 
        // check for user
        $user = $db->getUserByEmailAndPassword($email, $password);
        if ($user != false) {
            // user found
            $response["error"] = FALSE;
            $response["uid"] = $user["unique_id"];
            $response["user"]["firstname"] = $user["firstname"];
            $response["user"]["lastname"] = $user["lastname"];
            $response["user"]["email"] = $user["email"];
            $response["user"]["created_at"] = $user["created_at"];
            echo json_encode($response);
        } else {
            // user not found
            // echo json with error = 1
            $response["error"] = TRUE;
            $response["error_msg"] = "Incorrect email/password or email not verified yet!";
            echo json_encode($response);
        }
    } else if ($tag == 'register') {
        // Request type is Register new user
        $first_name = $_POST['first_name'];
        $last_name = $_POST['last_name'];
        $email = $_POST['email'];
        $password = $_POST['password'];
        $device = $_POST['device'];
        $confirmed_code = mt_rand(0,9999999999);
 
        // check if user is already existed
        if ($db->isUserExisted($email)) {
            // user is already existed - error response
            $response["error"] = TRUE;
            $response["error_msg"] = "User already existed";
            echo json_encode($response);
        } else {
            // store user
            $user = $db->storeUser($first_name, $last_name, $email, $password, $confirmed_code, $device);
            //need to be on server for this mail to work
            $message = "Hello $first_name,\n\nClick the link below to verify your account. \n\n http://zeroguiltllc.com/addlas/emailconfirm.php?email=$email&code=$confirmed_code \n\n Regards,\nZero Guilt.";
            mail($email,"Addlas Confirmation Email", $message,"From: DoNotReply@zeroguiltllc.com");
            if (!empty($user)) {
                $no_of_rows = mysqli_num_rows($user);
            }
           // if ($user) {
            if (!empty($no_of_rows) > 0) {
                // user stored successfully
                $response["error"] = FALSE;
                $response["uid"] = $user["unique_id"];
                $response["user"]["firstname"] = $user["firstname"];
                $response["user"]["lastname"] = $user["lastname"];
                $response["user"]["email"] = $user["email"];
                $response["user"]["created_at"] = $user["created_at"];

                //need to be on server for this mail to work
                $message = "Hello $first_name,\nClick the link below to verify your account. \n http://zeroguiltllc.com/addlas/emailconfirm.php?email=$email&code=$confirmed_code \n Regards,\nZero Guilt.";
                mail($email,"Addlas Confirmation Email", $message,"From: DoNotReply@zeroguiltllc.com");

                echo json_encode($response);
            } else {
                // user failed to store
                $response["error"] = TRUE;
                $response["error_msg"] = "Error occurred in Registration";
                echo json_encode($response);
            }
        }
    }else if ($tag == 'changepassword') {

        $email = $_POST['email'];
        $newpassword = $_POST['newpass'];

        $hash = $db->hashSSHA($newpassword);
        $encrypted_password = $hash["encrypted"]; // encrypted password
        $salt = $hash["salt"];
        $subject = "Change Password Notification";
        $message = "Hello User,\n\nYour Password is successfully changed.\n\nRegards,\nZero Guilt LLC.";
        $from = "DoNotReply@zeroguiltllc.com";
        $headers = "From:" . $from;
        if ($db->isUserExisted($email)) {

            $user = $db->forgotPassword($email, $encrypted_password, $salt);
            if ($user) {
                $response["success"] = 1;
                mail($email,$subject,$message,$headers);
                echo json_encode($response);
            }
            else {
                $response["error"] = 1;
                echo json_encode($response);
            }

            // user is already existed - error response
        } else {

            $response["error"] = 2;
            $response["error_msg"] = "User not exist";
            echo json_encode($response);
        }

    } else {
        // user failed to store
        $response["error"] = TRUE;
        $response["error_msg"] = "Unknown 'tag' value. It should be either 'login' or 'register'";
        echo json_encode($response);
    }

    
} else {
    $response["error"] = TRUE;
    $response["error_msg"] = "Required parameter 'tag' is missing!";
    echo json_encode($response);
}

?>