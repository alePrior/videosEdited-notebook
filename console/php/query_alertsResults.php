<?php
include "../../php/includes/dbc.php";

if (isset ($_POST['alerts_req'])) {
    // set page title
    ?>
    <div class="infoCont">
        <h3 class="alertsTitle"><i class="fas fa-exclamation-triangle"></i> Alerts</h3>
    </div>
    <?php
    
    $alerts_sql =
    'SELECT *
    FROM multilanguage_videos.videos
    WHERE comment_general != ""
    AND videos.blocked_gen = 0
    ;';

    // store all "videos_edit" rows in array
    $alerts_result = mysqli_query($conn, $alerts_sql);
    $alertsCheck = mysqli_num_rows($alerts_result);

    if ($alertsCheck > 0) {
        while ($row = mysqli_fetch_assoc($alerts_result)) {
            ?>
            <div class="infoCont marketVidsCont">
                <div class="marketTitleOptions_cont">
                    <div class="marketTitleSpan_cont infoCont_videoIDTitle">
                        <div class="inner_marketTitleLanguage_cont">
                            <h2 class="marketTitle" data-videoId="<?php echo $row['id_video']?>"><?php echo $row['videoID']?></h2>
                        </div>
                    </div>
                    <div class="genBlockVideo videosBtns" style="float: right">
                        <i class="blockVideo fas fa-ban"></i>
                    </div>
                    <!-- <i class="fas fa-edit genCommentBtn videosBtns"></i> -->
                    <!-- <span class="videosNum"></span> -->
                </div>
                <div class="genAlert_cont videoRows_notes">
                    <p id="videoSearched_genAlertPar" class="inner_videoRows_generalNotes" data-videocommgen_id="<?php echo $row['id_video']?>"><?php echo $row['comment_general']?></p>
                    <div class="videoRows_noteBtns" style="display: flex; align-items: center">
                        <!-- <i id="editBtn_general" class="fas fa-edit onlyGenComm_editBtn editComment_btn videosBtns"></i> -->
                        <i class="fas fa-check genResolve resolveComment_btn videosBtns"></i>
                        <!-- <i id="editBtn_general" class="fas fa-pen-square editComment_btn videosBtns"></i> -->
                    </div>
                </div>
            </div>
            <?php
        }
    } else {
        ?>
        <div class="infoCont">
            <h2>Luckily no alerts here..</h2>
        </div>
        <?php
    }
}
?>