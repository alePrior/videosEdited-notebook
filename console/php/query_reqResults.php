<?php
include "../../php/includes/dbc.php";
include "includes/genFuncs.php";

/* ---------------------------------------------------------- */
// FILE IS SHARED FROM THE WARNINGS, BLOCKED AND UNLOADED REQS
// BECAUSE LIST IS VISUALLY THE SAME BUT ONLY QUERIES CHANGE
/* ---------------------------------------------------------- */

if (isset ($_POST['warnings_req']) || isset ($_POST['blocked_req']) || isset ($_POST['unloaded_req'])) {
    // set page titles 
    if (isset ($_POST['warnings_req'])) {
        ?>
        <div class="infoCont">
            <h3 class="stats_videoWarnings"><i class="fas fa-exclamation-triangle"></i> Warnings</h3>
        </div>
        <?php
    }
    elseif (isset ($_POST['blocked_req'])) {
        ?>
        <div class="infoCont">
            <h3 class="blockVideo"><i class="blockVideo fas fa-ban"></i> Blocked videos</h3>
        </div>
        <?php
    }
    elseif (isset ($_POST['unloaded_req'])) {
        ?>
        <div class="infoCont">
            <h3><i class="fas fa-times"></i> Unloaded videos</h3>
            <p class="inner_videoRows_insertedVideo" style="font-size: 80%;">previous days</p>
        </div>
        <?php
    }

    // in blocked videos page display the globally blocked videos
    $genBlocked_arr = array(); // 
    if (isset ($_POST['blocked_req'])) {
        $genBlocked_query =
        "SELECT *
        FROM multilanguage_videos.videos
        WHERE blocked_gen = 1";
        
        $genBlocked_result = mysqli_query($conn, $genBlocked_query);
        if (mysqli_num_rows($genBlocked_result) > 0) {
            while ($genBlockedReq_row = mysqli_fetch_assoc($genBlocked_result)) {
                // fill general blocked videos array
                $genBlocked_arr[] = $genBlockedReq_row['videoID'];
                ?>
                <div class="infoCont marketVidsCont genBlockedCont">
                    <div class="marketTitleOptions_cont">
                        <div class="marketTitleSpan_cont infoCont_videoIDTitle">
                            <div class="inner_marketTitleLanguage_cont">
                                <h2 class="marketTitle" data-videoId="<?php echo $genBlockedReq_row['videoID']?>"><?php echo $genBlockedReq_row['videoID']?></h2>
                            </div>
                            <!-- <span class="videosNum"></span> -->
                        </div>
                        <!-- <i class="fas fa-edit genCommentBtn videosBtns"></i> -->
                        <div class="genBlockVideo videosBtns" style="float: right">
                            <!-- <i class="blockVideo fas fa-ban"></i> -->
                            <i class="unblockVideo fas fa-check-circle"></i>
                        </div>
                    </div>
                    <?php
                    $display_editbtn = !empty($genBlockedReq_row['comment_general']) ? "block" : "none";
                    ?>
                    <div class="genAlert_cont videoRows_notes">
                        <p id="videoSearched_genAlertPar" class="inner_videoRows_generalNotes" data-videocommgen_id="<?php echo $genBlockedReq_row['video_id']?>"><?php echo $genBlockedReq_row['comment_general']?></p>
                        <!-- <i id="editBtn_general" class="fas fa-edit onlyGenComm_editBtn editComment_btn videosBtns" style="display: <?php echo $display_editbtn?>"></i> -->
                    </div>
                </div>
                <?php
            }
        }
    }
    
    // WARNINGS QUERY
    if (isset ($_POST['warnings_req'])) {
        $warnings_sql =
        'SELECT DISTINCT videos_edited.video_id, videos.videoID, videos.comment_general
        FROM videos_edited, videos
        WHERE videos_edited.comment != ""
        AND videos_edited.blocked = 0
        AND videos.blocked_gen = 0
        AND videos_edited.video_id = videos.id_video
        ;';
    }
    // BLOCKED VIDEOS QUERY
    elseif (isset ($_POST['blocked_req'])) {

        $warnings_sql =
        'SELECT DISTINCT videos_edited.video_id, videos.videoID, videos.comment_general
        FROM videos_edited, videos
        WHERE videos_edited.blocked = 1
        AND videos_edited.video_id = videos.id_video
        ;';
    }
    // UNLOADED VIDEOS QUERY - only for previous days, not current
    elseif (isset ($_POST['unloaded_req'])) {
        // get todays date
        $today_date = date_create();
        $today = date_format($today_date,"Y-m-d");

        $warnings_sql =
        "SELECT DISTINCT videos_edited.video_id, videos.videoID, videos.comment_general
        FROM videos_edited, videos
        WHERE videos_edited.loaded = 0
        AND videos_edited.blocked = 0
        AND videos.blocked_gen = 0
        AND videos_edited.inserted < '$today'
        AND videos_edited.video_id = videos.id_video
        ;";
    }
    else { exit; }

    // store all "videos_edit" rows in array
    $warnings_result = mysqli_query($conn, $warnings_sql);
    $warningsCheck = mysqli_num_rows($warnings_result);

    if ($warningsCheck <= 0 && count($genBlocked_arr) == 0) {
        if (isset ($_POST['warnings_req'])) {
            ?>
            <div class="infoCont">
                <h2>Luckily no warnings here..</h2>
            </div>
            <?php
        }
        elseif (isset ($_POST['blocked_req'])) {
            ?>
            <div class="infoCont">
                <h2>No blocked videos..</h2>
            </div>
            <?php
        }
        elseif (isset ($_POST['unloaded_req'])) {
            ?>
            <div class="infoCont">
                <h2>No unloaded videos..</h2>
            </div>
            <?php
        }
    } else {
        while ($row = mysqli_fetch_assoc($warnings_result)) {
            // check if current video is a geneeral blocked video.
            // If true, do not display markets since it has already been displayed.
            // get general blocked videos in array so later videos in 
            if ( !in_array($row['videoID'], $genBlocked_arr)) {
                ?>
                <div class="infoCont marketVidsCont">
                    <div class="marketTitleOptions_cont">
                        <div class="marketTitleSpan_cont infoCont_videoIDTitle">
                            <div class="inner_marketTitleLanguage_cont">
                                <h2 class="marketTitle" data-videoId="<?php echo $row['videoID']?>"><?php echo $row['videoID']?></h2>
                            </div>
                            <!-- <span class="videosNum"></span> -->
                        </div>
                        <div class="genBlockVideo videosBtns" style="float: right">
                            <i class="blockVideo fas fa-ban"></i>
                        </div>
                        <!-- <i class="fas fa-edit genCommentBtn videosBtns"></i> -->
                        <!-- <div class="genBlockVideo videosBtns" style="float: right">
                            <i class="blockVideo fas fa-ban"></i>!
                        </div> -->
                    </div>
                    <?php
                    // display the general comment only if present
                    // $display_editbtn = !empty($row['comment_general']) || isset ($_POST['blocked_req']) ? "block" : "none";
                    $display_editbtn = !empty($row['comment_general']) ? "block" : "none";
                    // $display_editbtn = "block";
                    ?>
                    <div class="genAlert_cont videoRows_notes">
                        <p id="videoSearched_genAlertPar" class="inner_videoRows_generalNotes" data-videocommgen_id="<?php echo $row['video_id']?>"><?php echo $row['comment_general']?></p>
                        <!-- <i id="editBtn_general" class="fas fa-edit onlyGenComm_editBtn editComment_btn videosBtns" style="display: <?php echo $display_editbtn?>"></i> -->
                    </div>
                    <div class="marketlistCont warnings_listCont">
                        <!-- <hr> -->
                        <ul class="market_videosID_list">
                            <?php
                            $queriIdVideo = $row['video_id'];

                            // WARNINGS QUERY
                            if (isset ($_POST['warnings_req'])) {
                                $warnings_innserSql = 
                                "SELECT  markets.*, videos_edited.*, videos.*, users.name AS userName, users.color
                                FROM markets, videos_edited, videos, users
                                WHERE videos_edited.comment != ''
                                AND videos_edited.video_id = $queriIdVideo
                                -- AND videos_edited.blocked = 0
                                AND videos_edited.video_id = videos.id_video
                                AND videos_edited.market_id = markets.id_market
                                AND videos_edited.user_id = users.id_user
                                ;";
                            }
                            // BLOCKED QUERY
                            elseif (isset ($_POST['blocked_req'])) {
                                $warnings_innserSql = 
                                "SELECT  markets.*, videos_edited.*, videos.*, users.name AS userName, users.color
                                FROM markets, videos_edited, videos, users
                                WHERE videos_edited.blocked = 1
                                AND videos_edited.video_id = $queriIdVideo
                                -- AND videos_edited.blocked = 0
                                AND videos_edited.video_id = videos.id_video
                                AND videos_edited.market_id = markets.id_market
                                AND videos_edited.user_id = users.id_user
                                ;";
                            }
                            // UNLOADED QUERY
                            elseif (isset ($_POST['unloaded_req'])) {
                                $warnings_innserSql = 
                                "SELECT  markets.*, videos_edited.*, videos.*, users.name AS userName, users.color
                                FROM markets, videos_edited, videos, users
                                WHERE videos_edited.loaded = 0
                                AND videos_edited.video_id = $queriIdVideo
                                AND videos_edited.inserted < '$today'
                                -- AND videos_edited.blocked = 0
                                AND videos_edited.video_id = videos.id_video
                                AND videos_edited.market_id = markets.id_market
                                AND videos_edited.user_id = users.id_user
                                ;";
                            }
                            $innerResult = mysqli_query ($conn, $warnings_innserSql);
                            $innerResultCheck = mysqli_num_rows($innerResult);
        
                            if ($innerResultCheck > 0) {
                                while ($innerRow = mysqli_fetch_assoc($innerResult)) {
                                    // print all markets and determine which one is related to video
                                    $marketName = $innerRow['name'];
                                    $marketColor = is_bool(stripos($marketName, 'Curioctopus')) ? "gcvTitle" : "curioTitle";
                                    // determine whether market has searched video edited
                                    $className = "";
                                    if ($innerRow['loaded']) $className = "checked";
                                    ?>
                                    <li
                                        class="videoMarketRow highlightRow uncolored checkable <?php echo $marketColor?> <?php echo $className?> <?php if ($innerRow['blocked']) echo "blockedVid" ?>"
                                        data-id_market ="<?php echo $innerRow['market_id'] ?>"
                                        data-id_video  ="<?php echo $innerRow['video_id'] ?>"
                                        data-videoid   ="<?php echo $innerRow['videoID'] ?>"
                                        data-userid    ="<?php echo $innerRow['user_id'] ?>"
                                        style="pointer-events: all"
                                    >
                                        <input type="checkbox" <?php echo $innerRow['loaded'] ? "checked" : "" ?>>   
                                        <div class="marketTitle_ctrls">
                                            <span id="videoID" data-marketid=<?php echo $innerRow['market_id']?>><?php echo $marketName?> <?php /* if ($innerRow['blocked'])  echo '<i class="blockVideo fas fa-ban"></i>' */ ?></span>
                                            <div class="marketRowOptionsCont" style="float: right">
                                                <div class="toggleBlockVideo videosBtns"><i class="fas fa-ban"></i></div>
                                                <div class="editComment_btn videosBtns"><i class="fas fa-edit"></i></div>
                                            </div>
                                        </div>
                                        <?php
                                        if ($innerRow['blocked'] && false) { // "&& false" for not displaying it, remove it to display it
                                            ?>
                                            <div class="unblockVideo videosBtns" style="float: right">
                                                <!-- <i class="blockVideo fas fa-ban"></i> -->
                                                <i class="unblockVideo fas fa-check-circle"></i>
                                            </div>
                                            <?php
                                        }
                                        ?>
                                        <!-- <p class="inner_videoRows_insertedVideo"><?php echo setDateFormat($innerRow['inserted']) ?><?php /* <span class="userColorDot" style="background-color: #<?php echo $innerRow['color']?>"> */ ?> - <?php echo$innerRow['userName'] ?></p> -->
                                        <!-- <hr> -->
                                        <?php
                                        // Display speficic comment/video specificis warning.
                                        // On blocked reqs the controls are always available so comments can be inserted from here
                                        if (!empty($innerRow['comment']) || isset ($_POST['blocked_req'])) {
                                            ?>
                                            <div class="videoRows_notes" style="display: flex">
                                                <div class="comment_editCont">
                                                    <div class="specEditCont_inner">    
                                                        <p class="inner_videoRows_specificNotes" data-marketcomm_id="<?php echo $innerRow['market_id']?>" data-videocomm_id="<?php echo $innerRow['video_id']?>"><?php echo $innerRow['comment']?></p>
                                                        <i style="display: <?php echo empty($innerRow['comment']) ? "none" : "block"?>" class="fas fa-check specResolve resolveComment_btn videosBtns"></i>
                                                    </div>
                                                </div>
                                            </div>
                                            <?php
                                        }
                                        ?>
                                        <p class="inner_videoRows_insertedVideo"><?php echo setDateFormat($innerRow['inserted']) ?><?php /* <span class="userColorDot" style="background-color: #<?php echo $innerRow['color']?>"> */ ?> - <?php echo$innerRow['userName'] ?></p>
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
        }
    }
}
?>