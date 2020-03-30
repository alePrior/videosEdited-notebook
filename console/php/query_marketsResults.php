<?php
include "../../php/includes/dbc.php";
session_start();
/**
* Returns the final query string to search between the given day and the next day dates
* $dateString = day date string (format: 'Y-m-d', ex: 2019-11-24);
*/
function getDateQuery ($dateString, $db_insrtdAttr = "inserted") {
    
    $date1 = date_create ($dateString);
    $date2 = date_create ($dateString);
    
    date_add ($date2, date_interval_create_from_date_string ("1 day"));
    
    $date1 = date_format ($date1, "Y-m-d");
    $date2 = date_format ($date2, "Y-m-d");

    return "($db_insrtdAttr BETWEEN '$date1' AND '$date2')";
}

/* ------------------------------------------------------------------------------------ */

if (isset ($_POST['searchHint'])) {
    // from AJAX requests
    $searchHint = mysqli_real_escape_string($conn, $_POST['searchHint']);
    $startDate = $searchHint;
    $dateLog = date_create ($startDate);
} else {
    // todays videos 
    $startDate = "Y-m-d";
    $dateLog = date_create ();
}
// today's/current date
$today = date("Y-m-d");
$searchDate = date ($startDate);

// determine whether to view markets videos on previous dates
$hideVideosClass = "";
$spanVideosNumstyle = "style=''";
// if ($searchDate != $today) {
//     $hideVideosClass = "hide_marketlistCont";
//     $spanVideosNumstyle = "style='display:block;'";
// }

$dateLog_day = date_format ($dateLog, "l");
$dateLog_dateFormat = date_format ($dateLog, "j F Y");

// sort search if requested
$sort = false;
if (isset($_POST['sort']) && ($_POST['sort'] === 'true')) {
        $sort = true;
}
/* REMOTE EXPORTS DB CONNECTION ------------------------------- */
// fill date selected export queue to array
// store each video as object
class videoExpData {
    public $videoName = "";
    public $toExport  = "";
    public $exporting = "";
    public $exported  = "";
    public $inserted  = "";
}
$remoteDB_rslt_arr = array(); // array with object values
$remoteDb_sql = "SELECT * FROM videos WHERE " . getDateQuery($searchDate);
if($result = mysqli_query ($remote_conn, $remoteDb_sql)) {
    if (mysqli_num_rows($result) > 0) {
        while ($row = mysqli_fetch_assoc($result)) {
            $videoData = new videoExpData();
            $videoData->videoName = $row['export_videoName'];
            $videoData->toExport  = $row['toExport'];
            $videoData->exporting = $row['exporting'];
            $videoData->exported  = $row['exported'];
            $videoData->inserted  = $row['inserted'];

            $remoteDB_rslt_arr[] = $videoData;
        }
    }
}
// print_r($remoteDB_rslt_arr);
?>
<!-- date info -->
<div id="dateContainer" class="infoCont">
    <div id="inner_calendar_imgCont">
        <i class="fas fa-calendar"></i>
    </div>
    <div id="inner_calendar_dateCont">
        <label><?php echo $dateLog_day?><br><?php echo $dateLog_dateFormat?></label>
    </div>
</div>
<!-- filter view controls -->
<div id="viewCtrls" class="infoCont">
    <div id="arrow_ctrlsViewer" class="filterCtrls_toggler">
        <i class="fas fa-chevron-left"></i>
        <div id="closeForm_hoverCirc"></div>
    </div>
    <div id="viewCtrls_label" class="filterCtrls_toggler">
        <h4><i class="inputs_fontIcon fas fa-filter"></i> filter</h4>
    </div>
    <div id="viewCtrls_cont">
        <div id="sortUsersView_input" class="searchFieldsConts">
            <i class="inputs_fontIcon fas fa-user"></i>
            <span>users: 1</span>
            <i id="usersDropDown" class="inputs_fontIcon dropMenu_icon fas fa-sort-down"></i>
        </div>
        <div id="dropDownMenu" class="searchFieldsConts">
            <ul>
                <li id="allUserSelect">select all</li>
                <?php
                // construct users filter selection list
                $clientsReq_sql = 'SELECT * FROM multilanguage_videos.users;';
                $usersColors = array(); // array will store users corresponding color
            
                if($result = mysqli_query ($conn, $clientsReq_sql)) {
                    $resultCheck = mysqli_num_rows($result);
                    while ($row = mysqli_fetch_assoc($result)) {
                        // get name of user type
                        $type_id = $row['type_id'];
                        $userTypeSql = "SELECT type FROM multilanguage_videos.users_type WHERE id_type = $type_id;";
                        if($inner_result = mysqli_query ($conn, $userTypeSql)) {
                            $inner_resultCheck = mysqli_num_rows($inner_result);
                            while ($inner_row = mysqli_fetch_assoc($inner_result)) {
                                $userType = $inner_row['type'];
                            }
                        }
                        ?>
                        <li class='<?php echo ($row['id_user'] == $_SESSION ['id']) ? 'userSelected' : '' ?>' data-userid='<?php echo $row['id_user'] ?>'>
                            <?php
                            // show colored dot only on different session users
                            $usersColors[$row['id_user']] = $row['color'];
                            if ($row['id_user'] != $_SESSION['id']) {
                                ?>
                                <span class="userColorDot" style="background-color: #<?php echo $row['color']?>"></span>
                                <?php
                            }
                            ?>
                            <?php echo $row['name'] ?>
                            <?php
                            // display 'you' on current user selection list row
                            if ($row['id_user'] == $_SESSION['id']) {
                                ?>
                                <span style="
                                    font-size: 60%;
                                    opacity: 0.5;
                                    margin: 0 5px;
                                ">you</span>
                                <?php
                            }
                            ?>
                        </li>
                        <?php
                    }
                }
                ?>
            </ul>
        </div>
        <hr>
        <!-- video/market filters -->
        <div class="searchFieldsConts">
            <i class="inputs_fontIcon fas fa-filter"></i>
            <input id="sortMarketView_input" type="text" placeholder="market">
        </div>
        <div class="searchFieldsConts">
            <i class="inputs_fontIcon fas fa-filter"></i>
            <input id="sortVideoView_input" type="text" placeholder="video">
        </div>
        <hr>
        <!-- loaded/unloaded buttons -->
        <div id="filterCtrl_loadedVidsBtnsCont">
            <button id="loadedFiltBtn" class="navBtns loadUnloadBtns">
                <label><i class="fas fa-check"></i></label>
                <div class="btns_notify"></div>
            </button>
            <button id="unloadedFiltBtn" class="navBtns loadUnloadBtns">
                <label><i class="fas fa-times"></i></label>
                <div class="btns_notify"></div>
            </button>
            <button id="resetFilt" class="navBtns loadUnloadBtns">
                <label><i class="fas fa-redo-alt"></i></label>
                <div class="btns_notify"></div>
            </button>
        </div>
    </div>
</div>
<?php

$queryString = "AND " . getDateQuery($searchDate);

$sort_queryString = "";
if ($sort) {
    $sort_queryString = 'ORDER BY inserted DESC';
}

$sql =
"SELECT DISTINCT markets.*, videos_edited.market_id, languages.*
FROM markets, videos_edited, languages
WHERE markets.id_market = videos_edited.market_id
AND markets.language_id = languages.id_language
$queryString
$sort_queryString
;";

if(!$result = mysqli_query ($conn, $sql)) {
    echo "<p>Sorry, no data retrieved</p>";
    exit;
}

// checking if data has been fetched
$resultCheck = mysqli_num_rows($result);
// create table
if ($resultCheck > 0) {
    while ($row = mysqli_fetch_assoc($result)) {
        $marketName = $row['name'];
        if (is_bool(stripos($marketName, 'Curioctopus'))) {
            $h2Class = "gcvTitle";
        } else {
            $h2Class = "curioTitle";
        }
        ?>
        <div class="infoCont marketVidsCont" data-marketname="<?php echo str_replace(' ', '', strtolower($row['name']))?>">
            <div class="marketTitleSpan_cont">
                <div class="inner_marketTitleLanguage_cont">
                    <h2 class="<?php echo $h2Class?> marketTitle" data-marketId="<?php echo $row['id_market']?>"><?php echo $row['name']?></h2>
                    <p class="languagePar"><?php echo $row['language']?></p>
                </div>
                <span class="videosNum" <?php echo $spanVideosNumstyle ?>></span>
            </div>
            <div class="marketlistCont <?php echo $hideVideosClass?>">
                <hr>
                <ul class="market_videosID_list">
                    <?php
                    $market_id = $row['market_id'];

                    $innerSql =
                    "SELECT videos_edited.*, videos.*
                    FROM videos_edited, videos
                    WHERE videos_edited.market_id = $market_id
                    AND videos_edited.video_id = videos.id_video
                    AND videos_edited.blocked = 0
                    AND videos.blocked_gen = 0
                    $queryString
                    ORDER BY inserted
                    ;";

                    $innerResult = mysqli_query ($conn, $innerSql);
                    $innerResultCheck = mysqli_num_rows($innerResult);

                    if ($innerResultCheck > 0) {
                        while ($innerRow = mysqli_fetch_assoc($innerResult)) {
                            if ($innerRow['loaded']) {
                                $checked = "checked";
                            } else {
                                $checked = "";
                            }
                            // show middle hr only if both comments are present
                            $hrStyle = "none";
                            if ($innerRow['comment'] && $innerRow['comment_general']) {
                                $hrStyle = "block";
                            }
                            // do not display blocked videos
                            if ($innerRow['blocked']) continue;
                            ?>
                            <li
                                class="videoRow checkable <?php echo $checked?> <?php if ($innerRow['comment'] || $innerRow['comment_general']) echo "highlightRow" ?>"
                                data-id_market="<?php echo $innerRow['market_id'] ?>"
                                data-id_video="<?php echo $innerRow['video_id'] ?>"
                                data-videoid="<?php echo $innerRow['videoID'] ?>"
                                data-userid="<?php echo $innerRow['user_id'] ?>"
                                style="display: <?php echo ($innerRow['user_id'] == $_SESSION['id']) || !$innerRow['user_id']   ? 'block' : 'none' ?>;"
                            >
                                <input type="checkbox" <?php echo $checked?>>
                                <?php
                                if ($innerRow['user_id'] != $_SESSION['id']) {
                                    ?>
                                    <span class="userColorDot" style="background-color: <?php echo $innerRow['user_id'] ? '#' . $usersColors[$innerRow['user_id']] : '' ?>"></span>
                                    <?php
                                }
                                ?>
                                <span id="videoID" data-queryid="<?php echo $innerRow['video_id']?>" data-videoid="<?php echo $innerRow['videoID']?>"><?php echo $innerRow['videoID']?></span>
                                <?php
                                if ($innerRow['user_id'] != $_SESSION['id']) {
                                    ?>
                                    <span style="
                                        font-size: 60%;
                                        opacity: 0.5;
                                        margin: 0 5px;
                                    "><?php
                                    /* 
                                        // user name next to videoID
                                        if ($innerRow['user_id']) {
                                            // get username
                                            $userIDforQuery = $innerRow['user_id'];
                                            $userSql = "SELECT name FROM users WHERE id_user = $userIDforQuery";
                                            $userResult = mysqli_query ($conn, $userSql);
                                            while ($userRow = mysqli_fetch_assoc($userResult)) {
                                                $userName = $userRow['name'];
                                            }
                                            echo $userName;
                                        } */
                                    ?>
                                    </span>
                                    <?php
                                }
                                ?>
                                <!-- video status -->
                                <?php
                                // find export info from remote_exports array
                                $icon_toExp   = "";
                                $icon_Expting = "";
                                $icon_Expted  = "";
                                for ($i = count($remoteDB_rslt_arr) - 1; $i >= 0; $i--) { // loop array in reverse so it will loop from most to least recent (according to 'inserted' db_attr time val)
                                    // must check according to id and language
                                    $objVal = $remoteDB_rslt_arr[$i];
                                    $videoName = $objVal->videoName;
                                    $videoName_exp = explode('.', $videoName); 
                                    if (count($videoName_exp) == 2) {
                                        $videoID_rmt  = $videoName_exp[0]; // videoID
                                        $language_rmt = $videoName_exp[1]; // language
                                        if (is_numeric($videoID_rmt)) {
                                            if ($videoID_rmt == $innerRow['videoID'] && $language_rmt == $row['language']) {
                                                // assign values for <data-active=''> HTML props
                                                $icon_toExp   = $objVal->toExport  == 1 ? "true" : "" ;
                                                $icon_Expting = $objVal->exporting == 1 ? "true" : "" ;
                                                $icon_Expted  = $objVal->exported  == 1 ? "true" : "" ;

                                                break;
                                            }
                                        }
                                    }
                                }
                                ?>
                                <div class="status_icons_cont">
                                    <i data-active="<?php echo $icon_toExp ?>" class="status_icons fas fa-clock"></i>
                                    <i data-active="<?php echo $icon_Expting ?>" class="status_icons fas fa-cogs"></i>
                                    <i data-active="<?php echo $icon_Expted ?>" class="status_icons fas fa-sign-out-alt"></i>
                                </div>
                                <div class="videoRows_btnsCont">
                                    <div class="toggleBlockVideo videosBtns"><i class="fas fa-ban"></i></div>
                                    <div class="editComment_btn videosBtns"><i class="fas fa-edit"></i></div>
                                    <div class="clipBrdBtnCont videosBtns"><i class="fas fa-copy"></i></div>
                                </div>
                                <div class="videoRows_notes">
                                    <div class="comment_editCont">
                                        <div class="specEditCont_inner">
                                            <p class="inner_videoRows_specificNotes" data-marketcomm_id="<?php echo $innerRow['market_id']?>" data-videocomm_id="<?php echo $innerRow['video_id']?>"><?php echo $innerRow['comment']?></p>
                                            <i style="display: <?php echo empty($innerRow['comment']) ? "none" : "block"?>" class="fas fa-check specResolve resolveComment_btn videosBtns"></i>
                                            <!-- <i id="editBtn_specific" class="fas fa-edit editComment_btn"></i> -->
                                        </div>
                                        <hr style="display: <?php echo $hrStyle ?>" class="inner_videoRows_hrNotesDivider">
                                        <!-- <div class="comment_editCont"> -->
                                        <div class="genEditCont_inner">   
                                            <p class="inner_videoRows_generalNotes" data-videocommgen_id="<?php echo $innerRow['id_video']?>"><?php echo $innerRow['comment_general']?></p>
                                            <i style="display: <?php echo empty($innerRow['comment_general']) ? "none" : "block"?>" class="fas fa-check genResolve resolveComment_btn videosBtns"></i>
                                        </div>
                                    </div>
                                    <!-- <i id="editBtn_general" class="fas fa-edit editComment_btn videosBtns"></i> -->
                                    <!-- <i class="fas fa-check specResolve resolveComment_btn videosBtns"></i> -->
                                    <!-- <i id="editBtn_general" class="fas fa-pen-square editComment_btn videosBtns"></i> -->
                                </div>
                            </li>
                        <?php
                        }
                    }
                    ?>
                </ul>
            </div>
        </div>
        <?php
    }
} else {
    ?>
    <div class="infoCont">
        <h2>No videos here..</h2>
    </div>
    <?php
}
?>