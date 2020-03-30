<?php
include "php/includes/dbc.php";
$errorMsg = '';

if (isset($_POST['submit'])) {
    $userName = $_POST['userName'];
    $type     = $_POST['type'];
    $mail     = $_POST['mail'];
    $active   = ($_POST['active'] == 'checked') ? 1 : 0;
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);

    if( (empty ($userName) || empty ($mail)) && empty($password) ) {
        $errorMsg = 'missing data';
    } else {
        $stmt = $conn->prepare(
            "INSERT INTO multilanguage_videos.users
            ( name, type_id, mail, password, active )
            VALUES
            ( ?, ?, ?, ?, ? )
            ;"
        );
        $stmt->bind_param( "sissi", $userName, $type, $mail, $password, $active );
        
        if( $stmt->execute() === false ) {
            die ('ERR: ' . $stmt->error);
        }
        $stmt->close();
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
    <title>Admin signup</title>
</head>
<body>
    <form action="adminSubmit.php" method='post'>
        <label for="name">Username: <br>
        <input type="text" name="userName" autocomplete="off"><br>

        <label for="name">type: <br>
        <input type="text" name="type" autocomplete="off"><br>
        
        <label for="mail">mail: <br>
        <input type="text" name="mail" autocomplete="off"><br>

        <label for="name">active:
        <input type="checkbox" name="active" autocomplete="off" checked><br>

        <label for="name">password: <br>
        <input type="password" name="password" autocomplete="off"><br>

        <button name="submit">Signup</button> <br>
        <span><? echo $errorMsg ?></span>
    </form>
</body>
</html>