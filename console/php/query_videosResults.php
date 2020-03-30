<?php
include "../../php/includes/dbc.php";
include "includes/genFuncs.php";

if (isset ($_POST['id_video'])) {
    // from AJAX requests
    $videoIDToSearch = mysqli_real_escape_string($conn, $_POST['id_video']);
    // data-validation: check if it's a 1 to 6 int digits
    if (!preg_match('/^[0-9]*$/', $videoIDToSearch)) {
        ?> 
        <div class="infoCont">
            <h2>Uh oh..<br>I just need a number ;)</h2>
        </div> 
        <?php
    } else {
        // get markets 
        $videoMarkets_sql =
        "SELECT  markets.*, videos_edited.*, videos.*, users.name AS userName, users.color
        FROM markets, videos_edited, videos, users
        WHERE videos_edited.video_id =
            (SELECT
            videos.id_video
            FROM multilanguage_videos.videos
            WHERE videos.videoID = $videoIDToSearch)
        AND videos_edited.video_id = videos.id_video
        AND videos_edited.market_id = markets.id_market
        AND videos_edited.user_id = users.id_user
        ;";

        // store all "videos_edit" rows in array
        $videoMarkets_rowArr = array();
        $comment_general_ID = "";
        $comment_general = "";
        $blocked_gen = "";
        $videoMarkets_result = mysqli_query($conn, $videoMarkets_sql);
        $videoMarketsCheck = mysqli_num_rows($videoMarkets_result);
        if ($videoMarketsCheck > 0) {
            while ($videoEdited_row = mysqli_fetch_assoc($videoMarkets_result)) {
                $videoMarkets_rowArr[] = $videoEdited_row;
            }
            $comment_general_ID = $videoMarkets_rowArr[0]['id_video'];
            $comment_general = $videoMarkets_rowArr[0]['comment_general'];
            $blocked_gen = $videoMarkets_rowArr[0]['blocked_gen'];
        } else {
            // query for NOT edited videos with any market
            $alt_sql = "SELECT * FROM videos WHERE videoID = $videoIDToSearch";
            $videoMarkets_result = mysqli_query($conn, $alt_sql);
            $videoMarketsCheck = mysqli_num_rows($videoMarkets_result);
            if ($videoMarketsCheck > 0) {
                while ($row = mysqli_fetch_assoc($videoMarkets_result)) {
                    $comment_general_ID = $row['id_video'];
                    $comment_general    = $row['comment_general'];
                    $blocked_gen        = $row['blocked_gen'];
                }
            }
        }

        // query markets
        $markets_sql = "SELECT * FROM markets";
        $markets_result = mysqli_query($conn, $markets_sql);
        $marketsCheck = mysqli_num_rows($markets_result);
        if ($marketsCheck <= 0) {
            ?> 
            <div class="infoCont">
                <h2>No markets were found!</h2> <?php // parenthesis give problem to brakcets extension, don't get confused ?>
            </div>
            <?php
            // exit;
        }

        if ($videoMarketsCheck > 0) {
            ?>
            <div class="infoCont marketVidsCont <?php if ($blocked_gen == 1) echo "genBlockedCont" ?>">
                <div class="marketTitleOptions_cont">
                    <div class="marketTitleSpan_cont infoCont_videoIDTitle">
                        <div class="inner_marketTitleLanguage_cont">
                            <h2 class="marketTitle" data-videoid="<?php echo $videoIDToSearch?>"><?php echo $videoIDToSearch?></h2>
                        </div>
                        <!-- <span class="videosNum"></span> -->
                    </div>
                    <div class="genBlockVideo videosBtns" style="float: right">
                        <i class="blockVideo fas fa-ban"></i>
                    </div>
                    <!-- <i class="fas fa-edit genCommentBtn videosBtns"></i> -->
                </div>
                <div class="genAlert_cont videoRows_notes">
                    <p id="videoSearched_genAlertPar" class="inner_videoRows_generalNotes" data-videocommgen_id="<?php echo $comment_general_ID?>"><?php echo $comment_general?></p>
                </div>
            <!-- <hr> -->
            <div class="marketlistCont videosResults_listCont">
                <ul class="market_videosID_list">
                    <?php
                    // print all markets and determine which one is related to video
                    while ($row = mysqli_fetch_assoc($markets_result)) {
                        $marketName = $row['name'];
                        if (is_bool(stripos($marketName, 'Curioctopus'))) {
                            $marketColor = "gcvTitle";
                        } else {
                            $marketColor = "curioTitle";
                        }
                        // determine whether market has searched video edited
                        $className    = "nonValidMarketRow";
                        $comment      = "";
                        $inserted     = "";
                        $row_pointerEvents = "none";
                        $market_id    = "";
                        $id_video     = "";
                        $user_name    = "";
                        $user_color   = "";
                        $checked      = "";
                        $videoBlocked = "";
                        foreach ($videoMarkets_rowArr as $data) {
                            $validMarketName = $data['name'];
                            if ($validMarketName == $marketName) {
                                // get here row related data
                                $className = "highlightRow";
                                if ($data['loaded'] == 1) {
                                    $className .= " checked";
                                }
                                $row_pointerEvents = "all";
                                $market_id    = $data['market_id'];
                                $id_video     = $data['id_video'];
                                $comment      = $data['comment'];
                                $user_name    = $data['userName'];
                                $user_color   = $data['color'];
                                $inserted     = setDateFormat($data['inserted']);
                                $checked      = $data['loaded'] ? "checked" : "";
                                $videoBlocked = $data['blocked'];
                            }
                        }
                        
                        ?>
                        <li
                            class="videoMarketRow uncolored checkable <?php echo $className?> <?php echo $marketColor?> <?php if ($videoBlocked === '1' ) echo "blockedVid" ?>"
                            style="pointer-events: <?php echo $row_pointerEvents?>"
                            data-id_market =<?php echo $market_id?>
                            data-id_video  ="<?php echo $id_video?>"
                        >
                            <input type="checkbox" <?php echo $checked?>>
                            <?php
                            // for long market names put a <br> tag after the second letter
                            if(strlen($marketName) >=18 ) {
                                $marketName_explode = explode(" ", $marketName);
                                $marketName = "";
                                foreach ($marketName_explode as $i => $word) {
                                    $marketName .= " " . $word;
                                    if ($i == 1) {
                                        $marketName .= "<br>";
                                    }
                                }
                            }
                            ?>
                            <span id="videoID" data-marketid=<?php echo $market_id?>><?php echo $marketName?></span>
                            <?php
                            if (!empty($inserted)) {
                                ?>
                                <div class="marketRowOptionsCont" style="float: right">
                                    <div class="toggleBlockVideo videosBtns"><i class="fas fa-ban"></i></div>
                                    <div class="editComment_btn videosBtns"><i class="fas fa-edit"></i></div>
                                </div>
                                <?php   
                            }
                            /* 
                            if ($className != "nonValidMarketRow") {
                                ?>
                                <div class="videoRows_btnsCont">
                                    <div class="noteAlert_BtnCont videosBtns" data-videocommgen_id="<?php echo $id_video?>"><i class="fas fa-exclamation-triangle"></i></div>
                                    <!-- <div class="clipBrdBtnCont videosBtns"><i class="fas fa-copy"></i></div> -->
                                </div>
                                <?php
                            }
                             */
                            ?>
                            <div class="videoRows_notes">
                                <div class="comment_editCont">
                                    <div class="specEditCont_inner">
                                        <p class="inner_videoRows_specificNotes" data-marketcomm_id="<?php echo $market_id ?>" data-videocomm_id="<?php echo $id_video ?>"><?php echo $comment ?></p>
                                        <i style="display: <?php echo empty($comment) ? "none" : "block"?>" class="fas fa-check specResolve resolveComment_btn videosBtns"></i>
                                    </div>
                                </div>
                            </div>
                            <?php
                            if ($inserted) {
                                ?>
                                <p class="inner_videoRows_insertedVideo"><?php echo $inserted ?> <span class="userColorDot" style="background-color: #<?php echo $user_color?>"></span><?php echo $user_name?></p>
                                <?php
                            }
                            ?>
                        </li>
                    <?php
                    }
                    ?>
                </ul>
            </div>
            <?php
        } else {
            ?>
            <div class="infoCont">
                <h2>Not found :(</h2>
            </div>
            <?php
        }
    }
}
?>