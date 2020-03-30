<?php
// database connection
include "php/includes/dbc.php";
session_start(); // use if user is already logged in

$mail_userName = '';
$remeberMe = '';
// login error message
$spanErr_msg = '';
/* --------------------------------------------------------------------------------------------- */
// INSTANT REDIRECTION ----------------------------------------------------------------------------
/* --------------------------------------------------------------------------------------------- */

// redirect to console if session or cookie is set
if (isset($_SESSION["loggedin"]) && $_SESSION["loggedin"] === true) {
    header("Location: console/");
    exit;
} elseif ( isset($_COOKIE['rememberme'] )){
    // Decrypt cookie variable value
    // $userid = password_verify($_COOKIE['rememberme']);
    $userid = $_COOKIE['rememberme'];
    // query db if id is valid by number of rows returned
    $stmt = $conn->prepare (
        "SELECT id_user, name, mail, password
        FROM users
        WHERE id_user = ? "
    );
    //fetching result
    $stmt->bind_param("i", $userid);    
    if (!$stmt->execute()) {
        $spanErr_msg = "error logging to server";
    } else {
        $stmt->store_result();
        if ( $stmt->num_rows != 1 ) {
            $spanErr_msg = "invalid data from cookie";
        } else {
            $stmt->bind_result($id_attr, $username_attr, $mail_attr, $hashedPassword_attr);
            if ( $stmt->fetch() ){
                // set session
                $_SESSION["id"]       = $id_attr;
                $_SESSION["username"] = $username_attr;
                $_SESSION["loggedin"] = true;
                // ..and redirect to console
                header("Location: console/");
                exit;
            }
        }
    }
}

/* --------------------------------------------------------------------------------------------- */
// LOGIN SUBMIT ----------------------------------------------------------------------------------
/* --------------------------------------------------------------------------------------------- */

class ReturnAppMsg {
    public $msg  = '';
    public $id_user  = '';
    public $userName  = '';
    public $bool = true;
}
$returnObj = new ReturnAppMsg();

// index page and app submits
if (isset($_POST['login_submit']) || (isset($_POST['fromApp_loginSubmit']))) { 
    $mail_userName = isset($_POST['mail_userName']) ? trim($_POST['mail_userName']) : ''; 
    $password      = isset($_POST['password'])      ? $_POST['password']            : '';
    $remeberMe     = isset($_POST['remember_me'])   ? $_POST['remember_me']         : '';

    // Check if username is empty
    if( empty( $mail_userName ) ) {
        $spanErr_msg = "Please enter username";
    }
    // Check if password is empty
    if( empty( $password ) ) {
        $spanErr_msg = "Please enter password.";
    }

    // if no error message then validate data
    if (empty($spanErr_msg))  {
        // prepare statement for validating user data
        $sql =
        "SELECT id_user, name, mail, password
        FROM users
        WHERE name = ?
        OR mail = ?"
        ;

        if(!$stmt = $conn->prepare($sql)) {
            echo "error preparing data";
        } else {
            // Bind variables to the prepared statement as parameters
            $stmt->bind_param("ss", $mail_userName, $mail_userName);
            
            // Attempt to execute the prepared statement
            if( !$stmt->execute() ){
                echo "Something went wrong. Please try again later.";
            } else {
                // Store result
                $stmt->store_result();
                
                // Check if username exists, if yes then verify password
                if( $stmt->num_rows != 1 ) {
                    // Display an error message if username doesn't exist
                    $spanErr_msg = "User not found";
                } else {
                    // Bind result variables
                    $stmt->bind_result($id_attr, $username_attr, $mail_attr, $hashedPassword_attr);
                    if($stmt->fetch()){
                        if( !password_verify($password, $hashedPassword_attr) ) {
                            // Display an error message if password is not valid
                            $spanErr_msg = "Invalid password";
                        } else {
                            $returnObj->id_user = $id_attr; // for the app to detect user id for queries
                            $returnObj->userName = $username_attr; // for the app to detect user id for queries
                            // SESSION ------------------------------
                            if (isset($_POST['login_submit'])) {
                            // Username and password are correct, so start a new session
                            session_start();
                            // Store data in session variables
                            $_SESSION["id"]       = $id_attr;
                            $_SESSION["username"] = $username_attr;
                            $_SESSION["loggedin"] = true;

                            
                            // COOKIE -------------------------------
                            // on 'Remember me' checked, set cookie
                            if( !empty($remeberMe) ){
                                // Set cookie variables
                                // $value = password_hash( $id_attr, PASSWORD_DEFAULT );
                                $value = $id_attr;

                                $days = 30; // expiration time days
                                setcookie ("rememberme", $value, ($days * 24 * 60 * 60 * 1000), '/');
                            }
                            // redirect to page
                            header("Location: console/");
                            exit;
                            
                            } elseif (isset($_POST['fromApp_loginSubmit'])) {
                                $returnObj->msg = $id_attr;
                                echo json_encode ($returnObj);
                                exit;
                            }
                        }
                    }
                }
            }
        }
    }
    if (isset($_POST['fromApp_loginSubmit'])) {
        $returnObj->msg = $spanErr_msg;
        $returnObj->bool = false;

        echo json_encode($returnObj);
        exit;
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <meta name="theme-color" content="#383838">
    <meta name="viewport" content="user-scalable=no, width=device-width">
    <!-- <link rel="stylesheet" type="text/css" href="css/main.css"> -->
    <link rel="stylesheet" type="text/css" href="css/main.css">
    <link href="https://fonts.googleapis.com/css?family=Nunito&display=swap" rel="stylesheet">
    <link rel="icon" href="images/icon_notepad_white.png">
    <script defer src="https://use.fontawesome.com/releases/v5.0.6/js/all.js"></script>
    <title>Login</title>
</head>
<body>
    <div id="login_cont" class="infoCont">
        <div id="login_imgCont" class="login_innerCont">
            <img src="images/psycodeLogo_white.png">
            <p>Videos notebook</p>
        </div>
        <div id="vert_line"></div>
        <hr id="mobile_dividerLine">
        <div id="formCont" class="login_innerCont">
            <form action="index.php" method="post">
            <!-- mail -->
            <div class="searchFieldsConts">
                <i class="far fa-envelope"></i>
                <input type="text" name="mail_userName" placeholder="mail - username" autocomplete="off" value="<?php echo empty($mail_userName) ? '' : $mail_userName?>">
            </div>
            <!-- password -->
            <div class="searchFieldsConts">
                <i class="fas fa-lock"></i>
                <input type="password" name="password" placeholder="password">
            </div>
            <!-- remeber me -->
            <label id="remb_meCont">
                <input id="remember_meChckBx" type="checkbox" name="remember_me" <?php echo empty($remeberMe) ? '' : 'checked'?>>
                <span id="chkBx_alias">
                    <svg width="16  " height="16" viewBox="0 0 11 8" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <path d="M3.7845 5.67375L1.90782 3.72544C1.6987 3.50834 1.25045 3.67722 1.08546 3.84221C0.920467 4.0072 0.791208 4.4445 1.00032 4.6616L3.4098 7.15683C3.61891 7.37393 3.95671 7.37393 4.16583 7.15683L10.0003 0.999992C10.2094 0.782894 10.1118 0.434677 9.90264 0.21758C9.69353 0.000482216 9.30192 -0.0542824 9.09281 0.162815L3.7845 5.67375Z" fill="#272727"/>
                    </svg>
                </span>
                <span id="rememberMe_labelTxt">Remember me</span>
            </label>
            <!-- submit -->
            <button id="login_submitBtn" class="navBtns" type="submit" name="login_submit">Login</button>
            <span id="logMsg"><?php echo $spanErr_msg ?></span>
        </div>
    </div>
    <!-- <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.4.1/jquery.min.js"></script> -->
</body>
</html>