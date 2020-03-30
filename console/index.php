<?php
// database connection
include "../php/includes/dbc.php";
include "php/includes/genFuncs.php";
session_start();

// if no session was started (no login was donw) then return to login
if ( !isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true ) {
    header("Location: ../index.php");
    exit;
}
// logout button action
if ( isset($_POST['logOut']) ) {
    // unset session
    $_SESSION = array(); // Unset all of the session variables
    session_destroy(); // Destroy the session.
    // unset 'remeberMe' cookie
    setcookie ("rememberme", "", time() - 3600, '/');
    // Redirect to login page
    header("Location: ../index.php");
    exit;
}
// pass user info to host
if ( isset ($_POST['userInfo'])) {
    $user = new stdClass();
    $user->id   = $_SESSION['id'];
    $user->name = $_SESSION['username'];
    echo json_encode ($user);
    exit;
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
    <link rel="stylesheet" type="text/css" href="../css/main.css">
    <link href="https://fonts.googleapis.com/css?family=Nunito&display=swap" rel="stylesheet">
    <link rel="icon" href="../images/icon_notepad_white.png">
    <script defer src="https://use.fontawesome.com/releases/v5.0.6/js/all.js"></script>
    <title>Edited videos</title>
</head>
<body>
    <div id="userFixedNotif">
        <p id="textLog">Error occured</p><span id="closeUpperNotif">X</span>
    </div>
    <div id="dark_overlay"></div>
    <div id="videoCommentsForm" class="infoCont">
        <div id="videoCommentsForm_innerCont">
            <div id="closeFormCont">
                <p>X</p>
                <div id="closeForm_hoverCirc"></div>
            </div>
            <div id="videoCommentsForm_innerCont_titleCont">
                <h1 id="videoCommentsForm_marketIDTitle" class="hoverableTitle" data-marketid=""></h1>
                <h1 id="videoCommentsForm_videoIDTitle" data-videoid="">9999</h1>
            </div>
            <div id="marketSelectMenu" class="infoCont">
                <ul>
                    <?php
                    $marketSql =
                    "SELECT *
                    FROM markets
                    "
                    ;
            
                    if ($result = mysqli_query ($conn, $marketSql)) {
                        $resultCheck = mysqli_num_rows($result);
                        if ($resultCheck <= 0) {
                            echo ("<li>no markets found</li>");
                        } else {
                            while ($row = mysqli_fetch_assoc($result)) {
                                ?><li class="<?php echo $row['domain_id'] == 1 ? "gcvTitle" : "curioTitle" ?>" data-market_id=<?php echo $row['id_market'] ?> data-domain_id=<?php echo $row['domain_id'] ?> ><?php echo $row['name'] ?></li><?php
                            }
                        }
                    }
                    ?>
                </ul>
            </div>
            <div id="form_dividerLine"></div>
            <div id="videoCommentsForm_innerCont_inputsCont">
                <div id="commentsLabel">
                    <h1>Update a comment..</h1>
                </div>
                <!-- warning textarea -->
                <div class="videoCommentsForm_textareaConts">
                    <i class="fas fa-exclamation-triangle commentsForm_specificNoteDet"></i>
                    <textarea id="specComment_textarea" class="videoCommentsForm_textarea" data-marketcomm_id="" data-videocomm_id="" maxlength="70" placeholder="video-market warning"></textarea>
                </div>
                <!-- alert textarea -->
                <div class="videoCommentsForm_textareaConts">
                    <i class="fas fa-exclamation-triangle commentsForm_generalNoteDet"></i>
                    <textarea id="genComment_textarea" class="videoCommentsForm_textarea" data-videocommgen_id="" maxlength="70" placeholder="video general alert"></textarea>
                </div>
                <button id="commentsVidSubmit" class="navBtns"><i class="fas fa-check"></i></button>
            </div>
        </div>
    </div>
    <div id="clipBrd_logDiv" class="infoCont">copied to clipboard</div>
    <div id="mainCont">
        <?php
        // get todays date details
        $dateToCreate = ''; // changes calendar date - testing purposes
        $today_date = date_create($dateToCreate);
        $today_dateTimestamp = date_timestamp_get($today_date);
        $today = getdate($today_dateTimestamp);
        $year = $today['year'];
        $month = $today['month'];
        $monthNum = $today['mon'];
        $dayNum = $today['mday'];

        // get current months first day date details
        $currMonthFirstDay_date = date_create($year.'-'.$month.'-01');                 
        $currMonthFirstDay_dateTimestamp = date_timestamp_get($currMonthFirstDay_date);
        $firstMonth_date = getdate($currMonthFirstDay_dateTimestamp);
        $firstMonth_weekDay = $firstMonth_date['wday'];
        $lastDayOfMonth = date('t', $currMonthFirstDay_dateTimestamp);
        
        // Determine data-hint attributes in buttons for AJAX requests
        $date = date ("Y-m-d");
        $today = date_create ($date);
        $yesterday = date_create ($date);
        date_add ($yesterday, date_interval_create_from_date_string ("-1 day"));
        $today = date_format ($today, "Y-m-d");
        $yesterday = date_format ($yesterday, "Y-m-d");

        // for the current day '.infoCont' box
        $dateLog = $today_date;
        $dateLog_day = date_format ($dateLog, "l");
        $dateLog_dateFormat = date_format ($dateLog, "j F Y");
        ?>
        <div id="mainCont_upperCont" class="infoCont">
            <!-- <div class="mainCont_searchFieldsConts">
                <i id="search_fontIcon" class="fas fa-search"></i>
                <input type="text" placeholder="Search market..">
            </div> -->
            <div class="mainCont_searchFieldsConts">
                <i id="search_fontIcon" class="inputs_fontIcon fas fa-search"></i>
                <input id="searchVideo_input" type="text" autocomplete="off" placeholder="Search video..">
            </div>
            <div id="upperCont_rightBtnsCont">
                <span id="userNameLabel" data-userid=<?php echo $_SESSION["id"] ?>><i class="fas fa-user"></i> <?php echo $_SESSION["username"] ?></span>
                <button id="warningsBtn_gen" class="navBtns notifBtns stats_videoWarnings">
                    <label><i class="fas fa-exclamation-triangle"></i></label>
                    <div class="btns_notify"></div>
                </button>
                <button id="alertsBtn_gen" class="navBtns notifBtns stats_videoErrors">
                    <label><i class="fas fa-exclamation-triangle"></i></label>
                    <div class="btns_notify"></div>
                </button>
                <!-- old logout -->
                <!-- <form action="" method="post" style="display: none">
                    <button id="logOutBtn" class="navBtns" name="logOut">
                        <div id="userNameLabel" data-userid="<?php echo $_SESSION["id"] ?>"><p><?php echo $_SESSION["username"] ?></p></div>
                        <label><i class="fas fa-sign-out-alt"></i></label>
                    </button>
                </form> -->
                <button id="options_dots" class="">
                    <label><i class="fas fa-ellipsis-h"></i></label>
                </button>
            </div>
        </div>
        <!-- three dots options menu -->
        <div id="dots_menu" class="infoCont">
            <ul>
                <li data-option="notes"><i class="fas fa-sticky-note"></i> notes</li>
                <li data-option="unloaded"><i class="fas fa-times"></i> unloaded</li>
                <li data-option="blocked"><i class="fas fa-ban"></i> blocked</li>
                <hr>
                <li data-option="logout"><i class="fas fa-sign-out-alt"></i> logout</li>
            </ul>
        </div>
        <!-- eventual other container. For now it just makes space -->
        <div id="inner_rightCont" class="inner_conts ">
            <div id="navBtnsCont" class="infoCont">
                <div id="innerBtnsCont">
                    <button id="calendar-selectBtn" class="navBtns"><i class="far fa-calendar-alt"></i></button>
                    <!-- <button class="navBtns" data-hint="<?php echo $yesterday;?>"><label>Yesterday</label></button> -->
                    <button class="navBtns selectedBtn todayBtn" data-hint="<?php echo $today;?>"><label>Today</label></button>
                    <!-- sort button -->
                    <button id ="sort_btn" class="navBtns"><div><i class="fas fa-exchange-alt"></i></div></button>
                    <!-- sort button -->
                    <div id ="viewStats_onhover"><i class="fas fa-question-circle"></i></div>
                </div>
            </div>
            <div id="sideStats_cont" class="infoCont">
                <h2 id="stats_market" class="marketTitle"><span id="loaded"></span> / <span id="allMarks"></span> <label></label></h2>
                <hr>
                <div id="stats_videos" class="stats_videosGeneral"><span id="loaded"></span> / <span id="allVids"></span> <label></label></div>
                <!-- <div id="stats_loaded_videos" class="stats_videosGeneral"><span>18</span> <label>loaded</label></div> -->
                <div id="stats_specificVideosErrors" class="stats_videosGeneral stats_videoWarnings"><i id="stats_specificErrors_icon" class="fas fa-exclamation-triangle"></i> <span></span> <label>No warnings here</label></div>
                <div id="stats_videosErrors" class="stats_videosGeneral stats_videoErrors"><i id="stats_generalErrors_icon" class="fas fa-exclamation-triangle"></i> <span></span> <label></label></div>
            </div>
        </div>
        <div id="inner_middleCont" class="inner_conts ">
            <!-- calendar -->
            <div id="calendarMainContainer" class="infoCont" data-date_info="<?php echo $today?>">
                <!-- --------------------------------------------- -->
                <!-- <div id="monthNameCont">
                    <h4><?php echo $year?> <?php echo $month?></h4>
                    <div id="calendar_arrowsCont">
                        <i class="fas fa-caret-left calendar_arrows"></i>
                        <i class="fas fa-caret-right calendar_arrows"></i>
                    </div>
                </div> -->
                <!-- --------------------------------------------- -->
                <div id="monthNameCont">
                    <i class="fas fa-caret-left calendar_arrows"></i>
                    <h4><?php echo $year?> <?php echo $month?></h4>
                    <i class="fas fa-caret-right calendar_arrows"></i>
                    <!-- <div id="calendar_arrowsCont"> -->
                    <!-- </div> -->
                </div>
                <div id="dayNamesCont">
                    <div class="dayNamesDivs">MON</div>
                    <div class="dayNamesDivs">TUE</div>
                    <div class="dayNamesDivs">WED</div>
                    <div class="dayNamesDivs">THU</div>
                    <div class="dayNamesDivs">FRI</div>
                    <div class="dayNamesDivs">SAT</div>
                    <div class="dayNamesDivs">SUN</div>
                </div>
                <div id="dayNumsCont">
                    <div id="calendar_slider"> 
                        <div id="inner_dayNumsCont" class="monthSlide currentSlide" data-slideindex="1">
                            <?php
                            // echo calendar days numbers
                            $d = 0;
                            $dayNumStr = 0;
                            // sundays will be numbered with 7 so week days will be numbered:
                            // 1-7 / Monday-Sunday
                            if ($firstMonth_weekDay == 0) {
                                $firstMonth_weekDay = 7;
                            } 
                            
                            for ($i = 1; $i <= 42; $i++) {
                                // determine days to display
                                $currDayID = '';
                                $emptyDaysClass = '';
                                $futureDaysClass = '';

                                if (($i >= $firstMonth_weekDay) && (($d + 1) <= $lastDayOfMonth)) {
                                    $dayNumStr = ++$d;
                                    // echo data-attribute for AJAX requests
                                    $dataDateAttrObj = date_create ($year . "-" . $monthNum . "-" . $d);
                                    $dataDateAttr = date_format ($dataDateAttrObj, "Y-m-d");
                                    // determine current day
                                    if ($d == $dayNum) {
                                        $currDayID = 'id="currentDay"';
                                    } elseif ($d > $dayNum) {
                                        $futureDaysClass = 'nextDays';
                                    }
                                } else {
                                    $dayNumStr = '';
                                    $dataDateAttr = '';
                                    $emptyDaysClass = 'emptyDays';
                                }
                                // determine sundays
                                $sundayClass = "";
                                if (!($i % 7)) {
                                    $sundayClass = "sundaysDays";
                                }
                                ?>
                                <div <?php echo $currDayID?> class="dayNumsDivs <?php echo $emptyDaysClass?> <?php echo $futureDaysClass?> <?php echo $sundayClass?>" data-qHint="<?php echo $dataDateAttr?>"><label><?php echo $dayNumStr?></label></div>
                                <?php
                            }
                            ?>
                        </div>
                    </div>
                </div>
            </div>
            <div id="dataCont">
                <!-- reqeusted data content here -->
            </div>
        </div>
        <!-- RIGHT      SIDE NOTES -->
        <div id="inner_rigthNoteCont" class="inner_conts">
            <div id="inner_innerNoteContainer">
                <!-- <p style="font-style: italic; color: red; font-size: 80%; margin-bottom: 0;"><?php echo "note side container must be opimized; <br> containers margins top"?></p> -->
                <!-- <div class="infoCont">
                    <p style="color: #fb5454">*For now only this note is working, don't delete it!</p>
                </div> -->
                <?php
                $id_user = $_SESSION['id'];
                $sql =
                "SELECT *
                FROM notes
                WHERE user_id = $id_user;";

                if($result = mysqli_query ($conn, $sql)) {
                    $resultCheck = mysqli_num_rows($result);
                    if ($resultCheck > 0) {
                        while ($row = mysqli_fetch_assoc($result)) {
                            $note = $row['note'];
                            $id_note = $row['id_note'];
                            $noNote_class = "";
                            if ($id_note && $note) {
                                ?>
                                <div class="infoCont noteMainCont" data-noteid="<?php echo $id_note?>">
                                    <div class="noteBtns_cont">
                                        <div class="editNoteBtn videosBtns"><i class="fas fa-edit"></i></div>
                                        <div class="removeNoteBtn videosBtns" style="display: block"><i class="fas fa-trash-alt"></i></div>
                                    </div>
                                    <div>
                                        <p class="noteTextarea <?php echo $noNote_class?>"><?php echo $note?></p>
                                        <textarea class="noteTextarea" style="display:none" placeholder="Write something.."></textarea>
                                    </div>
                                    <div class="note_inserted inner_videoRows_insertedVideo">
                                        <p><?php echo setDateFormat($row['inserted'])?></p>
                                    </div>
                                    <div>
                                        <button class="submitNoteBtn navBtns"><i class="fas fa-check"></i></button>
                                    </div>
                                </div>
                                <?php
                            }
                        }
                    } else {
                        ?>
                        <div class="infoCont noteMainCont" data-noteid="">
                            <div class="noteBtns_cont">
                                <div class="editNoteBtn videosBtns"><i class="fas fa-edit"></i></div>
                                <div class="removeNoteBtn videosBtns" style="display: none"><i class="fas fa-trash-alt"></i></div>
                            </div>
                            <div>
                                <p class="noteTextarea">I am the first note, edit me or add more notes!</p>
                                <textarea class="noteTextarea" style="display:none" placeholder="Write something.."></textarea>
                            </div>
                            <div>
                                <button class="submitNoteBtn navBtns"><i class="fas fa-check"></i></button>
                            </div>
                        </div>
                        <?php
                    }
                }
                ?>
            </div>
            <button id="textArea_addBtn" class="navBtns"><label>Add</label></button>
        </div>
    </div>
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.4.1/jquery.min.js"></script>
    <script type="text/javascript" src="js/script.js"></script>
</body>
</html>