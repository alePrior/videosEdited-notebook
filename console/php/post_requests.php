<?php 
if ($_POST) {
    include "../../php/includes/dbc.php";

    $sql = "";

    if (isset($_POST['loaded'])) {
        /*
        * clicked market video row
        */
        $market_id = mysqli_real_escape_string($conn, $_POST['market_id']);
        $id_video = mysqli_real_escape_string($conn, $_POST['id_video']);
        $loaded = mysqli_real_escape_string($conn, $_POST['loaded']);
    
        $sql =
        "UPDATE videos_edited
        SET loaded = $loaded
        WHERE videos_edited.market_id = $market_id AND videos_edited.video_id = $id_video;";
    
    }
    // VIDEO COMMENTS - WARNINGS ALERTS --------------------------------------------------
    elseif (isset($_POST['reqCommentsData'])) {
        /*
        * Send data for comments
        */
        $id_market = strval($_POST['id_market']);
        $id_video  = strval($_POST['id_video']);

        class Response {
            public $reqRow  = array();
            public $vidDoms = array();
            public $msg     = '';
            public $bool    = false;
        }
        $response = new Response();

        // validate data
        if (!is_numeric($id_market) && !is_numeric($id_video)) {
            $response->message = "Invalid data";
            $response->bool = false;
        } else {
            // get all worked markets names 
            $doms_stmt = $conn->prepare(
            "SELECT
            markets.name AS marketName
            FROM videos_edited, markets
            WHERE videos_edited.video_id = ?
            AND videos_edited.market_id = markets.id_market"
            );
            $doms_stmt->bind_param("i", $id_video);
            $doms_stmt->execute();
            $doms_result = $doms_stmt->get_result();
            if($doms_result->num_rows > 1) {
                while($row = $doms_result->fetch_assoc()) {
                    $response->vidDoms[] = $row['marketName'];
                }
            }
            $doms_stmt->close();

            // if all went good, then fetch row/market result and send data back
            $stmt = $conn->prepare(
            "SELECT
                domains.name as domainName,
                markets.name as marketName,
                videos_edited.market_id,
                videos_edited.video_id,
                videos.videoID,
                videos.comment_general AS alert,
                videos_edited.comment AS warning
            FROM videos, videos_edited, markets, domains
            WHERE videos.id_video = videos_edited.video_id
            AND videos_edited.market_id = markets.id_market
            AND markets.domain_id = domains.id_domain
            AND (videos_edited.video_id = ?) AND (videos_edited.market_id = ?)
            "
            );
            $stmt->bind_param("ii", $id_video, $id_market);
            $stmt->execute();
            $result = $stmt->get_result();
            if($result->num_rows !== 1) {
                // no rows
                $response->msg  = 'no data from db';
                $response->bool = false;
            } else {
                while($row = $result->fetch_assoc()) {
                    $response->reqRow = $row;
                    $response->bool   = true;
                }
            }
            $stmt->close();
        }
        echo json_encode($response);

    } elseif (isset($_POST['submitComments'])) {
        /*
        * video specific/general comments
        */
        $id_video = mysqli_real_escape_string($conn, $_POST['id_video']);
        $market_id = mysqli_real_escape_string($conn, $_POST['market_id']);

        $specComment = mysqli_real_escape_string($conn, $_POST['specComment']);
        $genComment = mysqli_real_escape_string($conn, $_POST['genComment']);

        // host manipulates comments strings with "__NO_UPDATE_VALUE" if the received comment SHOULD'T BE UPDATED.
        // this because single comments can be edited
        if ($specComment !== "__NO_UPDATE_VALUE") {
            // specific comment
            $specComm_sql =
            "UPDATE videos_edited
            SET comment = '$specComment'
            WHERE videos_edited.video_id = $id_video AND videos_edited.market_id = $market_id;";
            $result = mysqli_query ($conn, $specComm_sql);
            // notify host of sent data
            print_r($result);
        }

        if ($genComment !== "__NO_UPDATE_VALUE") {
            // general comment
            $genComm_sql =
            "UPDATE videos
            SET comment_general = '$genComment'
            WHERE videos.id_video = $id_video;";
            $result = mysqli_query ($conn, $genComm_sql);
            // notify host of sent data
            print_r($result);
        }
        exit;
    }
    // USER SIDE NOTES ---------------------------------------------------------
    elseif (isset($_POST['updateNote'])) {
        /*
        * modified note
        */
        $noteText = mysqli_real_escape_string($conn, $_POST['updateNote']);
        $id_Note = mysqli_real_escape_string($conn, $_POST['id_Note']);

        // $sql ="INSERT INTO notes (note) VALUES ('$note');";
        $sql =
        "UPDATE notes
        SET note = '$noteText'
        WHERE id_note = $id_Note;";

    } elseif (isset($_POST['insertNote'])) {
        /*
        * insert new note
        */
        $noteText = mysqli_real_escape_string($conn, $_POST['insertNote']);
        $user_id = mysqli_real_escape_string($conn, $_POST['user_id']);

        $insertNote_sql = "INSERT INTO notes (note, user_id) VALUES ('$noteText', $user_id);";

        mysqli_query ($conn, $insertNote_sql);

        // send back last note id
        $lastNote_id_query = "SELECT MAX(id_note) FROM notes;";
        if($result = mysqli_query ($conn, $lastNote_id_query)) {
            $resultCheck = mysqli_num_rows($result);
            $row = mysqli_fetch_assoc($result);
            foreach ($row as $key => $value) {
                $lastNote_id_value = $row[$key];
            }
        }
        echo $lastNote_id_value;

        // then give last note id to script so it can append it to new note container data-attribute
    } elseif (isset($_POST['removeNote'])) {

        $id_Note = mysqli_real_escape_string($conn, $_POST['id_Note']);

        $sql = "DELETE FROM notes WHERE id_note = $id_Note;";
    }
    // WARNINGS-ALERTS UPDATE NUMBER VALUE ---------------------------------------------
    elseif (isset($_POST['warningsReq'])) {
        /*
        * warnings header notif button
        */
        $warnings_sql =
        'SELECT videos_edited.*, videos.*
        FROM videos_edited, videos
        WHERE videos_edited.video_id = videos.id_video
        AND comment != ""
        AND blocked = 0
        AND blocked_gen = 0'
        ;

        if ($result = mysqli_query ($conn, $warnings_sql)) {
            echo mysqli_num_rows($result);
        }
    } elseif (isset($_POST['alertsReq'])) {
        /*
        * warnings header notif button
        */
        $alerts_sql =
        'SELECT *
        FROM multilanguage_videos.videos
        WHERE comment_general != ""
        AND videos.blocked_gen = 0
        ;'
        ;

        if ($result = mysqli_query ($conn, $alerts_sql)) {
            echo mysqli_num_rows($result);
        }
    } elseif (isset($_POST['commentDataReq'])) {
        /*
        * return comments data for warnings/alerts client form
        */
        $marketId = mysqli_real_escape_string($conn, $_POST['marketId']);
        $videoId = mysqli_real_escape_string($conn, $_POST['videoId']);
        $idVideo = mysqli_real_escape_string($conn, $_POST['idVideo']);

        // if market is missing then the alert comment is requestes, which only need the id_video/videoID
        // object will contain returned query data (because of possible missing spec/gen comments)
        class Comments {
            public $idMarket;
            public $marketName;
            public $idVideo;
            public $videoID;
            public $specComment;
            public $genComment;
        }
        $comment = new Comments();
        if ($marketId) {
    
            // warning (spec comment)
            $warningSql =
            'SELECT markets.*, videos_edited.comment, videos.*
            FROM markets, videos_edited, videos
            WHERE videos_edited.market_id = '.$marketId.' AND
            videos_edited.video_id = '.$videoId.' AND
            markets.id_market = videos_edited.market_id AND
            videos.id_video = videos_edited.video_id;';
    
            // fill object with data (only one row is queried)
            if($result = mysqli_query ($conn, $warningSql)) {
                $resultCheck = mysqli_num_rows($result);
                while ($row = mysqli_fetch_assoc($result)) {
                    $comment->idMarket = $row['id_market'];
                    $comment->marketName = $row['name'];
                    $comment->idVideo = $row['id_video'];
                    $comment->videoID = $row['videoID'];
                    $comment->specComment = $row['comment'];
                    $comment->genComment = $row['comment_general'];
                }
            }
        } else {
            // warning (spec comment)
            $alertSql =
            'SELECT * FROM multilanguage_videos.videos  WHERE id_video = '.$idVideo.';';
    
            // fill object with data (only one row is queried)
            if($result = mysqli_query ($conn, $alertSql)) {
                $resultCheck = mysqli_num_rows($result);
                while ($row = mysqli_fetch_assoc($result)) {
                    $comment->idVideo = $row['id_video'];
                    $comment->videoID = $row['videoID'];
                    $comment->genComment = $row['comment_general'];
                }
            }
        }
        echo json_encode($comment);
    } elseif (isset($_POST['resolve_specComment'])) {
        /**
        * resolve comments: update note removing text
        */
        $marketId = mysqli_real_escape_string($conn, $_POST['marketId']);
        $idVideo = mysqli_real_escape_string($conn, $_POST['idVideo']);

        $sql =
        "UPDATE videos_edited
        SET comment = ''
        WHERE market_id = $marketId
        AND video_id = $idVideo
        "
        ;
    } elseif (isset($_POST['resolve_genComment'])) {
        /**
        * resolve comments: update note removing text
        */
        $idVideo = mysqli_real_escape_string($conn, $_POST['idVideo']);

        $sql =
        "UPDATE videos
        SET comment_general = ''
        WHERE id_video = $idVideo
        "
        ;
    } elseif (isset($_POST['genBlockVideo'])) {
        /*
        * generally block video in 'videos' table 
        */
        $videoID  = mysqli_real_escape_string($conn, $_POST['videoID']);

        if (!is_numeric($videoID)) {
            echo "Error: Invalid data";
            exit;
        }

        // get value from db 
        $stmt = $conn->prepare(
            "SELECT blocked_gen
            FROM multilanguage_videos.videos
            WHERE videoID = $videoID
            "
        );
        $stmt->execute();
        $result = $stmt->get_result();
        if($result->num_rows === 1) {
            while($row = $result->fetch_assoc()) {
                $blocked_gen = $row['blocked_gen'];
            }
        }
        // invert fetched value 
        $blocked_gen = $blocked_gen == 1 ? 0 : 1;
        // go final query
        $sql =
        "UPDATE videos
        SET blocked_gen = $blocked_gen
        WHERE videoID = $videoID
        "
        ;

    } elseif (isset($_POST['toggleBlockVideo'])) {
        /**
        * block/unblock video
        */
        $market_id = strval(mysqli_real_escape_string($conn, $_POST['market_id']));
        $video_id  = strval(mysqli_real_escape_string($conn, $_POST['video_id']));

        class Res {
            public $msg     = "";
            public $blocked = "";
            public $bool    = false;
        }

        $res = new Res();

        $stmt = $conn->prepare(
            "SELECT *
            FROM multilanguage_videos.videos_edited
            WHERE market_id = ? AND video_id = ?"
        );
        $stmt->bind_param("ii", $market_id, $video_id);
        $stmt->execute();
        if ( $stmt->execute() === false ) {
            $res->msg = 'ERR: ' . $stmt->error;
        } else {
            //fetching result
            $result = $stmt->get_result();

            if($result->num_rows !== 1) {
                // no rows
                $res->msg ='no markets data from db';
            } else {
                // get row data
                $row = $result->fetch_assoc();
                $blocked_inv = $row['blocked'] == 1 ? 0 : 1;
                $res->blocked = $blocked_inv;
            }
        }
        $stmt->close();

        $blockedVidsQuery =
        "UPDATE videos_edited
        SET blocked = $blocked_inv
        WHERE market_id = $market_id
        AND video_id = $video_id
        "
        ;

        mysqli_query ($conn, $blockedVidsQuery) == 1  ? $res->bool = true : $res->msg = "Error during query" ;

        echo json_encode($res);

    } elseif (isset($_POST['unblockVideo'])) {
        /**
        * block video
        */
        $market_id = mysqli_real_escape_string($conn, $_POST['market_id']);
        $videoID  = mysqli_real_escape_string($conn, $_POST['videoID']);

        $sql =
        "UPDATE videos_edited
        SET blocked = 0
        WHERE market_id = $market_id
        AND video_id =
            (SELECT id_video
            FROM videos
            WHERE videoID = $videoID)
        "
        ;
    }
    if ($sql) {
        echo mysqli_query ($conn, $sql);
        exit;
    }

    /* ------------------------------------------------------------------------------------------------- */
    // DESKTOP APPLICATION -----------------------------------------------------------------------------
    /* ------------------------------------------------------------------------------------------------- */

    /**
    * Check if passed market is in db.
    * Market names are compared removing whitespaces since in zip files market names are with no space chars
    * If market is in db then the stored market name is returned (with space chars). 
    * If not false is returned.
    */
    function checkMarket ($market) {
        GLOBAL $conn;

        // validate market
        $stmt = $conn->prepare(
            "SELECT *
            FROM multilanguage_videos.markets"
        );
        $stmt->execute();
        //fetching result
        $result = $stmt->get_result();

        if($result->num_rows === 0) {
            // no rows
            $response->msg = 'no markets data from db';
            $response->bool = false;
        } else {
            // get rows
            while($row = $result->fetch_assoc()) {
                $markets_name = $row['name'];
                $markets_arr[] = $markets_name;
            }
            // check if passed market is in db
            foreach ($markets_arr as $key => $value) {
                // remove whitespaces in string
                $market_noSpace = str_replace(' ', '', $value);
                if (str_replace(' ', '', strtolower($market)) === str_replace(' ', '', strtolower($market_noSpace))) {
                    return $value;
                }
            }
        }
        $stmt->close();
        return false;
    }

    /*
    * Request languages records
    */
    if (isset($_POST['req_langs'])) {
        $langs_arr = array();
        $langs_sql =
        'SELECT * FROM multilanguage_videos.languages;';
            
        // fill object with data (only one row is queried)
        if($result = mysqli_query ($conn, $langs_sql)) {
            $resultCheck = mysqli_num_rows($result);
            while ($row = mysqli_fetch_assoc($result)) {
                array_push($langs_arr, $row['language']);
            }
        }
        echo json_encode($langs_arr);
    }

    /*
    * Check if passed video is blocked or reports comments
    * In case video is blocked, error is returned
    * In case of a general comment on video only message is returned
    */
    if (isset($_POST['req_videoID'])) {
        $videoID = $_POST['videoID'];

        $blocked_gen = 0;
        $comment_general = '';

        class Response {
            public $message = '';
            public $bool    = true;
        }

        $response = new Response();
            
        $stmt = $conn->prepare(
            "SELECT *
            FROM videos
            WHERE videoID = ?
            ;"
        );
        $stmt->bind_param("i", strval($videoID));
        if ( $stmt->execute() === false ) {
            die ('ERR: ' . $stmt->error);
        } else {
            $result = $stmt->get_result();
            if ($result->num_rows == 1) {
                while($row = $result->fetch_assoc()) {
                    $blocked_gen = $row['blocked_gen'];
                    $comment_general = $row['comment_general'];

                    if (strval($blocked_gen) == 1 ) {
                        $response->message = 'video is blocked';
                        $response->bool = false;
                    } else if (!empty($comment_general)) {
                        $response->message = "alert: " . $comment_general;
                    }
                }
            }
        }
        $stmt->close();

        echo json_encode ($response);
    }

    /*
    * Validate market and request corresponding language
    * Market validation is made requesting market names, removing spaces and comparing
    * the whitespaces free market name received from host with all market names.
    */
    if (isset($_POST['req_marketLang'])) {
        // using prepared statements
        $market = str_replace(' ', '', $_POST['market']);
        $markets = array();
        class Response {
            public $data = array();
            public $msg  = '';
            public $bool = true;
        }

        $response = new Response();

        // query langauge
        if ( !($marketToSearch = checkMarket ($market)) ) {
            // passed market is not in DB
            $response->msg = 'invalid market';
            $response->bool = false;
        } else {
            // prepare query
            $inner_stmt = $conn->prepare(
                "SELECT languages.language
                FROM multilanguage_videos.languages
                WHERE languages.id_language =
                    (SELECT markets.language_id
                    FROM multilanguage_videos.markets
                    WHERE markets.name = ?
                    )
                ;"
            );
            $inner_stmt->bind_param("s", $marketToSearch);
            $inner_stmt->execute();
            $inner_stmt->store_result();
            if($inner_stmt->num_rows === 0) {
                // no rows
                $response->msg = 'no languages data from db';
                $response->bool = false;
            } else {
                $inner_stmt->bind_result($language); 
                while($inner_stmt->fetch()) {
                    $response->data = $language;
                }
            }
            $inner_stmt->close();
        }
        echo json_encode($response);
    }

    /**
     * Receive data and query to db
    */
    if (isset($_POST['insertVid'])) {
        $market  = str_replace(' ', '', $_POST['market']);
        $videoID = $_POST['videoID'];
        $user_id = $_POST['user_id'];

        // -- VALIDATE PASSED USER ID HERE BEFORE MAKING ANY QUERIES! --
        if ( !($market_stored = checkMarket ($market)) ) {
            echo "invalid market";
        } else {
            // insert 'videoID' in 'videos'
            // query avoids duplicates with no error report 
            $stmt = $conn->prepare(
                "INSERT INTO multilanguage_videos.videos (videoID)
                SELECT * FROM (SELECT ?) AS tmp
                WHERE NOT EXISTS
                    (SELECT videoID FROM videos WHERE videoID = ? ) LIMIT 1
                ;"
            );
            $stmt->bind_param("ss", $videoID, $videoID);
            if ( $stmt->execute() === false ) {
                die ('ERR: ' . $stmt->error);
            } else {
                // on success: insert 'market name' and 'videoID' in 'videos_edited'
                $inner_stmt = $conn->prepare(
                    "INSERT INTO multilanguage_videos.videos_edited
                    (market_id, video_id, user_id) VALUES
                    -- market name
                    (
                    (SELECT id_market FROM markets WHERE markets.name = ?),
                    -- video ID
                    (SELECT id_video FROM videos WHERE videoID = ?),
                    -- user ID
                    ?
                    )
                    ;"
                );
                $inner_stmt->bind_param("ssi", $market_stored, $videoID, $user_id);
                
                if( $inner_stmt->execute() === false ) {
                    die ('ERR: ' . $inner_stmt->error);
                }
                $inner_stmt->close();
            }
            $stmt->close();
        }
    }

    /**
    * Check year/month path 
    */
    if (isset($_POST['reqVidPath'])) {
        $video_id = $_POST['video_id'];

        class Res {
            public $year = "";
            public $month = "";
        }
        $res = new Res();

        if (is_numeric($video_id)) {
            $stmt = $conn->prepare(
                "SELECT * FROM multilanguage_videos.videos WHERE videoID = ?"
            );
            $stmt->bind_param("i", strval($video_id));
            $stmt->execute();
            $result = $stmt->get_result();
            if($result->num_rows) {
                $row = $result->fetch_assoc();
                $res->year  = $row['path_year'];
                $res->month = $row['path_month'];
            }
            $stmt->close();
        }

        echo json_encode($res);
    }
    // register year/month path info
    if (isset($_POST['regVidPath'])) {
        $videoID = strval($_POST['video_id']);
        $year    = strval($_POST['path_year']);
        $month   = strval($_POST['path_month']);

        // update only if all values are numbers
        if ( is_numeric($videoID) && is_numeric($videoID) && is_numeric($videoID) ) {
            echo "querying db\n";
            $stmt = $conn->prepare(
                "UPDATE multilanguage_videos.videos
                SET path_year = ?, path_month = ?
                WHERE videoID = ?
                ;"
            );
            $stmt->bind_param("iii", $year, $month, $videoID);
            
            if( $stmt->execute() === false ) {
                die ('ERR: ' . $stmt->error);
            }
            if ($stmt->affected_rows === 0) {
                // if no row was updated then Insert the video since host found it as a valid video
                $inner_stmt = $conn->prepare(
                    "INSERT INTO multilanguage_videos.videos
                    (videoID, path_year, path_month) VALUES
                    (?, ?, ?)
                    ;"
                );
                $inner_stmt->bind_param("iii", $videoID, $year, $month );
                
                if( $inner_stmt->execute() === false ) {
                    die ('ERR: ' . $inner_stmt->error);
                } else {
                    echo $videoID . "row inserted";
                }
                $inner_stmt->close();
            } else {
                echo $videoID . "row updated";
            }
            $stmt->close();
        } else {
            echo "invalid data for update/insert";
        }
    }
}