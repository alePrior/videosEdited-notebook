<?php
/**
* DATABASE CONNECTION - MULTILANGUAGE
*/

// NAS
// $dbServername   = "192.168.1.23";
// $dbUsername     = "videosConsole";
// $dbPassword     = "Oj3JsdiCUQFQanHT";

// XAMPP
$dbServername   = "localhost";
$dbUsername     = "root";
$dbPassword     = "";
$dbName         = "multilanguage_videos";

try {
    if ($conn = @mysqli_connect($dbServername, $dbUsername, $dbPassword, $dbName)) {
    // do something
    } else {
        throw new Exception('Unable to connect to database');
    }
}
catch(Exception $e) {
    ?>
    <div id="errorCont" class="infoCont">
        <!-- <image src="images/cloud_connection_error.png"> -->
        <p>
            <?php 
            echo $e->getMessage();
            // exit;
            ?>
        </p>
    </div>
    <?php
}

/**
* DATABASE CONNECTION - REMOTE EXPORTS
*/

$remote_dbName = "remote_exports_videos";

if ($remote_conn = @mysqli_connect($dbServername, $dbUsername, $dbPassword, $remote_dbName)) {
    // do something
} else {
    echo "error connecting to: " . $remote_dbName;
}