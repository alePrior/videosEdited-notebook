var windowWidthThresh_midNarrow = 1150; 
var windowWidthThresh_narrow = 755; 
var sortSearch = false;
const user = {
    id   : $('#userNameLabel').attr('data-userid'),
    name : $('#userNameLabel').text()
}

function showUpperFixedNotif (message = 'Error occurred :(') {
    $('#userFixedNotif').addClass('showUpperNotif')
    $('#userFixedNotif p').text(message)
}
// close upper notif on 'X' click
$('#closeUpperNotif').click(function () {
    if ($('#userFixedNotif').hasClass('showUpperNotif')) {
        $('#userFixedNotif').removeClass('showUpperNotif')
        $('#userFixedNotif p').text('')
    }
})

function hideEmptyMarketBoxes () {
    // hide market boxes with all hidden video rows
    let $marketVidsCont = $('.marketVidsCont')
    // $marketVidsCont.css('display', 'block')

    // loop market boxes
    for(let i = 0; i < $marketVidsCont.length; i++) {
        let $currmarketVidsCont = $marketVidsCont.eq(i)
        let allVideoRows = $currmarketVidsCont.find('.videoRow')
        // find rows with display: none 
        let hiddenVideoRows = $currmarketVidsCont.find('.videoRow[style="display: none;"]')
        if (hiddenVideoRows.length === allVideoRows.length) $currmarketVidsCont.css('display', 'none')
        // determine whether market box is empty
        if (allVideoRows.length <= 0) $currmarketVidsCont.remove()
    }
}
/**
 * Bring back to view all market boxes with video rows
 */
function resetView () {
    // 'no results' box remove
    $('#noFilterResultsMsgBox').remove()
    // show only current user videos
    $('.videoRow').css('display', 'none')
    $('.videoRow[data-userid=' + user.id + ']').css('display', 'block')
    // select current user in users list
    $('#dropDownMenu ul > li').removeClass('userSelected')
    $('#dropDownMenu ul > li[data-userid=' + user.id + ']').addClass('userSelected')
    // change selector label
    $('#sortUsersView_input span').text('users: 1')
    // show here all market boxes, then hide those with no video rows
    $('.marketVidsCont').css('display', 'block')
    hideEmptyMarketBoxes ()
}

/**
 * Detect checked input checboxes and determine videos ID list elements classes
 * according to database 'loaded' attr
 */
function detectLoadedVids () {
    var videoRows = $('.videoRow checkable')
    for (var i = 0; i < videoRows.length; i++) {
        var listEl = $(videoRows[i])
        var $parent_infoCont = videoRows.eq(i).parents('.infoCont')
        viewVideosNumber($parent_infoCont)
        var checkBox = listEl.find('input')[0]

        if ($(checkBox).attr('checked')) {
            checkBox.checked = true;
            listEl.addClass('checked')
        } else {
            checkBox.checked = false;
            listEl.removeClass('checked')
        }
    }
}
/**
 * Detect comments/notes on videos and color video alert icon depending from them
 */
function detectComments() {
    var videoRows = $('.videoRow')
    var video_note, general_note, alertBtn

    for (var i = 0; i < videoRows.length; i++) {
        video_note = videoRows.eq(i).find('.inner_videoRows_specificNotes').text()
        general_note = videoRows.eq(i).find('.inner_videoRows_generalNotes').text()
        alertBtn = videoRows.eq(i).find('.noteAlert_BtnCont')

        if (general_note) {
            alertBtn.addClass('generalNoteDet')
        } else if (video_note) {
            alertBtn.addClass('specificNoteDet')
        }
    }
}

/**
 * Update side stats box about loaded videos
 */
function updateLoadedVideos_compltdMarks () {
    var completeMarkets = $('.completedMarketVids')
    var checkedVideos = $('.checked')
    // determine completed markets 
    $('#stats_market span#loaded').text(completeMarkets.length)
    // determine loaded videos
    $('#stats_videos span#loaded').text(checkedVideos.length)
}
/**
 * Update stats box about current markets/videos
 */
function updateSideStats(setMarketValue="", setVideosValue="") {
    var marketsConts = $('#dataCont .marketVidsCont')
    var videosRows = $('#dataCont .videoRow')

    // determine loaded videos and completed markets
    updateLoadedVideos_compltdMarks ()

    function determinePlural ($element, string) {
        if ($element != 1) {
            string += "s"
        }
        return string
    }
    // determine markets label
    var marketLabel = determinePlural (marketsConts.length, "market")
    $('#stats_market span#allMarks').text(marketsConts.length)
    $('#stats_market label').text(marketLabel)
    
    // determine videos label
    var videosLabel = determinePlural (videosRows.length, "video")
    $('#stats_videos span#allVids').text(videosRows.length)
    $('#stats_videos label').text(videosLabel)

    // ERRORS COUNT -----------------------------------------------------------
    function findErrors (commentTypeClass, messageContainer, singleLableType) {
        // countErrors
        var errorsNum = "";
        var errors = 0;
        var pointerEvents;
        
        for (var i = 0; i < commentTypeClass.length; i++) {
            if (commentTypeClass.eq(i).text()) {
                errors++
            }
        }
        
        var noErrorsMessage = determinePlural (errors, singleLableType)
        messageContainer.removeClass('stats_noVideoErrors')

        if (errors <= 0) {
            noErrorsMessage = "No " + singleLableType + "s here.."
            messageContainer.addClass('stats_noVideoErrors')
            pointerEvents = "none"
        } else {
            errorsNum = errors
            messageClass = ''
            pointerEvents = "all"
        }
        
        messageContainer.css('pointer-events', pointerEvents)
        messageContainer.find('label').text(noErrorsMessage)
        messageContainer.find('span').text(errorsNum)
    }
    // general alerts
    var generalNoteMsgs = $('.inner_videoRows_generalNotes')
    var genMsgContainer = $('#stats_videosErrors')
    
    findErrors (generalNoteMsgs, genMsgContainer, "alert")
    
    // specific warnings
    var specNoteMsgs = $('.inner_videoRows_specificNotes')
    var specMsgContainer = $('#stats_specificVideosErrors')

    findErrors (specNoteMsgs, specMsgContainer, "warning")
}

function updateNotifHeaderButtons () {
    var $warningsBtn_gen = $('#warningsBtn_gen')
    var $alertsBtn_gen = $('#alertsBtn_gen')
    var warning_class = "warnings_activeNot"
    var alert_class = "alerts_activeNot"
    var warning_text, alert_text

    function req_updateUI (postParObj, $el, text, elClass) {
        $.post('php/post_requests.php', postParObj, function (data) {
            if (data > 0) {
                text = data
                $el.addClass(elClass)
            } else {
                text = ""
                $el.removeClass(elClass)
            }
            $el.find('.btns_notify').text(text)
        })
    }

    // update warnings
    req_updateUI ({warningsReq: true}, $warningsBtn_gen, warning_text, warning_class)
    // update alerts
    req_updateUI ({alertsReq: true}, $alertsBtn_gen, alert_text, alert_class)
}

function viewErrorsWarnings ($errorType) {
    var $marketlistCont = $('.marketlistCont')
    
    $marketlistCont.addClass('hide_marketlistCont')
    $('.videoRows_notes').removeClass('viewVideoNote')
    $('.videoRow').removeClass('videoRow_notesHiglight')
    
    for (var i = 0; i < $errorType.length; i++) {
        var $infoCont = $errorType.eq(i).parents('.infoCont')
        var $videoRow = $errorType.eq(i).parents('.videoRow')
        var $videoRows_notes = $errorType.eq(i).parents('.videoRows_notes')

        if ($errorType.eq(i).text()) {
            $infoCont.find('.marketlistCont').removeClass('hide_marketlistCont')
            $videoRows_notes.addClass('viewVideoNote')
            $videoRow.addClass('videoRow_notesHiglight')
        }
    }
}

/* -------------------------------------------------------------------------------------------------------- */
/**
 * This function will display the form for inserting comments to videos
 * newForm: boolean to determine whether the passed elements are already present in db or not.
 *  - 'true': both textareas will show,
 *  - 'false': only the textarea with the corresponding passed data will show
 */
function displayVideoCommentsForm (id_marketCli, id_videoCli, newForm = true, currEl="") {
    $.post('php/post_requests.php', {
        id_market : id_marketCli,
        id_video  : id_videoCli,
        reqCommentsData : true
    }, function (res) {
        // response code here
        let data_res = JSON.parse(res)
        let data = data_res.reqRow // array
        let doms = data_res.vidDoms // array
        let msg  = data_res.msg
        let bool = data_res.bool
        if (bool === false) {
            showUpperFixedNotif(msg)
        } else {
            // scroll to top window
            window.scrollTo(0, 0)
            // display form
            $('#dark_overlay').css('display', 'block')
            $('#videoCommentsForm').css('display', 'block')
            $('body').css('overflow-y', 'hidden');

            // append text
            var $videoCommentsForm_videoIDTitle = $('#videoCommentsForm_videoIDTitle')
            $videoCommentsForm_videoIDTitle.text(data['videoID'])
            $videoCommentsForm_videoIDTitle.attr('data-videoid', data['video_id'])
            // determine market title
            var $videoCommentsForm_marketIDTitle = $('#videoCommentsForm_marketIDTitle')
            if (data['marketName'] != "") {
                $videoCommentsForm_marketIDTitle.css('display', 'block')
                $videoCommentsForm_marketIDTitle.text(data['marketName'])
                var marketClass = '';
                if (data['marketName'] == 'Curioctopus') {
                    marketClass = "curioTitle"
                } else {
                    marketClass = "gcvTitle"
                }
                $videoCommentsForm_marketIDTitle.removeClass("curioTitle")
                $videoCommentsForm_marketIDTitle.removeClass("gcvTitle")
                $videoCommentsForm_marketIDTitle.addClass(marketClass)
                $videoCommentsForm_marketIDTitle.attr('data-marketid', data['market_id'])
            } else {
                $videoCommentsForm_marketIDTitle.css('display', 'none')
            }

            // Textareas -------
            var $specComment_textarea = $('#specComment_textarea')
            var $genComment_textarea = $('#genComment_textarea')
            var $specComment_textarea_parent = $specComment_textarea.parent()
            var $genComment_textarea_parent = $genComment_textarea.parent()

            // disable view of both textareas so that depending on comment value the corresponding textarea will be shown
            $specComment_textarea_parent.css('display', 'none')
            $genComment_textarea_parent.css('display', 'none')
            // empty both textareas values
            $specComment_textarea.prop('value', '')
            $genComment_textarea.prop('value', '')
            $specComment_textarea.attr('data-marketcomm_id', "")
            $specComment_textarea.attr('data-videocomm_id', "")
            $genComment_textarea.attr('data-videocommgen_id', "")

            // ..then fill textareas
            // warning
            if (data['warning'] || newForm) {
                $specComment_textarea.prop('value', data['warning']) 
                $specComment_textarea.attr('data-marketcomm_id', data['market_id'])
                $specComment_textarea.attr('data-videocomm_id', data['video_id'])
                $specComment_textarea_parent.css('display', 'flex')
            }
            // alert
            if (data['alert'] || newForm) {
                $genComment_textarea.prop('value', data['alert']) 
                $genComment_textarea.attr('data-videocommgen_id', data['video_id'])
                $genComment_textarea_parent.css('display', 'flex')
            }

            // prepare available domains list (market title drop-down menu)
            let mrkNms_listEls = $('#marketSelectMenu > ul li')
            mrkNms_listEls.removeClass('nonValidMarketRow')
            for (let i = 0; i < mrkNms_listEls.length; i++) {
                let listEl = mrkNms_listEls.eq(i);
                if(!doms.includes(listEl.text())) {
                    listEl.addClass('nonValidMarketRow')
                }
            }

            // focus on first textarea
            $('.videoCommentsForm_textarea').eq(0).focus()
        }
    })
    .fail(function() {
        showUpperFixedNotif()
    })
}
// toggle dropdown menu
$('#videoCommentsForm_marketIDTitle').click(function () {
    /**
     * !! MUST FILL MENU WITH THE -ALREADY DOWNLOADED VIDEOS- MARKETS
     * SO PASS THE SERVER THE VIDEO ID AND RETRIEVE A LIST OF THE "WORKED" MARKETS NAMES.
     */
    $('#marketSelectMenu').toggleClass('showMenu')
})
// market name list elements select
$('#marketSelectMenu li').click(function () {
    if (!$(this).hasClass('nonValidMarketRow')) {
        let marketIDTitle = $('#videoCommentsForm_marketIDTitle')
        // get data
        let market_id = $(this).attr('data-market_id')
        let domain_id = $(this).attr('data-domain_id')
        let market_name = $(this).text()
        
        // set title color
        marketIDTitle.removeClass('gcvTitle')
        marketIDTitle.removeClass('curioTitle')
        domain_id == 1 ? marketIDTitle.addClass('gcvTitle') : marketIDTitle.addClass('curioTitle')
        
        // append new data
        $('[data-marketcomm_id]').attr('data-marketcomm_id', market_id)
        marketIDTitle.attr('data-marketid', market_id)
        marketIDTitle.text(market_name)
        
        // update the warning comment
        let videoID  = $('#videoCommentsForm_videoIDTitle').attr('data-videoid')
        // update comments form according to selected data
        displayVideoCommentsForm(market_id, videoID)
    
        $('#marketSelectMenu').toggleClass('showMenu')
    }
})
/* -------------------------------------------------------------------------------------------------------- */

/** 
 * copied to clipboard message
*/
function copyToClipboardMsg (eventPar, msg="copied to clipboard") {
    var $clipBrd_logDiv = $('#clipBrd_logDiv')
    var scrolled = $(window).scrollTop();

    let clientRect = event.target.getBoundingClientRect()

    // let y_axis = eventPar.originalEvent.y
    // let x_axis = eventPar.originalEvent.x
    let y_axis = clientRect.y
    let x_axis = clientRect.x

    $clipBrd_logDiv.text(msg)
    $clipBrd_logDiv.delay(650).fadeOut(400)
    $clipBrd_logDiv.css({
        'display': 'block',
        'top' : y_axis + scrolled,
        'left': x_axis - 150
    })
}
/** 
 * Copy to clipboard passed string
 */
function copyToClipboard (str, event) {
    const el = document.createElement('textarea');  // Create a <textarea> element
    el.value = str;                                 // Set its value to the string that you want copied
    el.setAttribute('readonly', '');                // Make it readonly to be tamper-proof
    el.style.position = 'absolute';                 
    el.style.left = '-9999px';                      // Move outside the screen to make it invisible
    document.body.appendChild(el);                  // Append the <textarea> element to the HTML document
    const selected =            
        document.getSelection().rangeCount > 0      // Check if there is any content selected previously
        ? document.getSelection().getRangeAt(0)     // Store selection if found
        : false;                                    // Mark as false to know no selection existed before
    el.select();                                    // Select the <textarea> content
    document.execCommand('copy');                   // Copy - only works as a result of a user action (e.g. click events)
    document.body.removeChild(el);                  // Remove the <textarea> element
    if (selected) {                                 // If a selection existed before copying
        document.getSelection().removeAllRanges();  // Unselect everything on the HTML document
        document.getSelection().addRange(selected); // Restore the original selection
    }
    copyToClipboardMsg (event)
}

/**
 * Copy to clipboard event handler and message animation
 */
function rowsOptionsEvents () {
    // block video
    $('.toggleBlockVideo').click(function () {
        let targetRow = $(this).parents('li')
        let marketid = targetRow.attr('data-id_market')
        let idVideo  = targetRow.attr('data-id_video')

        $.post('php/post_requests.php', {
            market_id  : marketid,
            video_id   : idVideo,
            toggleBlockVideo : true
        }).done(function( data ) {
            data = JSON.parse(data)
            /**
             * MUST HANDLE BLOCKED VIDEOS VALUES FOR REAL TIME UI CHANGE
             */
            if (data.bool) {
                // if (!targetRow.hasClass('videoMarketRow')) {
                //     // in daily videos list page
                //     targetRow.remove()
                //     hideEmptyMarketBoxes()
                // } else {
                    // in video search results  
                    if (data.blocked) {
                        targetRow.addClass('blockedVid')
                    } else {
                        targetRow.removeClass('blockedVid')
                    }
                // }
            } else {
                showUpperFixedNotif('Error in request: block video: ' + idVideo + ' for market: ' + marketid + ': ' + data.msg)
            }
        })
    })
    
    // block video in all markets (in video search page)
    $('.genBlockVideo').click(function () {
        let $this = $(this);
        let marketVidsCont = $this.parents('.marketVidsCont')
        let videoID_val = marketVidsCont.find('h2[data-videoid]').attr('data-videoid')

        $.post('php/post_requests.php', {
            videoID   : videoID_val,
            genBlockVideo : true
        }).done(function( data ) {
            if (parseInt(data) === 1) {
                if (marketVidsCont.hasClass('genBlockedCont')) {
                    marketVidsCont.removeClass('genBlockedCont')
                } else {
                    // $this.remove() // remove button
                    marketVidsCont.addClass('genBlockedCont')
                }
                updateNotifHeaderButtons()
            } else if (data.startsWith('Error')) {
                showUpperFixedNotif(data != "" ? data : 'Error in request: general block of video: ' + videoID)
            }
        })
    })

    // unblock video (in video search page)
    $('.unblockVideo').click(function () {
        let $videoMarketRow = $(this).parents('.videoMarketRow')
        let marketid = $videoMarketRow.find('span[data-marketid]').attr('data-marketid')
        let idVideo = $(this).parents('.marketVidsCont').find('h2[data-videoid]').attr('data-videoid')
        
        $.post('php/post_requests.php', {
            market_id  : marketid,
            videoID   : idVideo,
            unblockVideo : true
        }).done(function( data ) {
            if (parseInt(data) === 1) {
                $videoMarketRow.removeClass('blockedVid')
                $videoMarketRow.find('.unblockVideo').remove()
                updateNotifHeaderButtons()
            } else {
                showUpperFixedNotif('Error in request: unblock video: ' + idVideo + ' for ' + market_id )
            }
        })
    })

    // edit note button (on markets results page)
    $('.editComment_btn').click(function () {
        var $target = $(this)
        var $videoRow = $target.parents('li')
        
        var id_market_vidRow = $videoRow.attr('data-id_market')
        var id_video_vidRow  = $videoRow.attr('data-id_video')

        displayVideoCommentsForm (id_market_vidRow, id_video_vidRow)
    })

    // copy to clipboard button
    $('.videoRows_btnsCont .clipBrdBtnCont').click(function (event) {
        var target = $(event.target)
        var videoRow = target.parents('.videoRow')
        var language = target.parents('.marketVidsCont').find('.languagePar').text()

        if (!videoRow.length) {
            videoRow = target.parents('.videoMarketRow')
        }
        // var videoID = videoRow.find('span')[0].textContent
        var videoID = videoRow.attr('data-videoid')

        // this could be an option in the user options menu
        if (language) {
            videoID += "_" + language
        }

        copyToClipboard(videoID, event)

        $('.clipBrdBtnCont').removeClass('clipBrdAnim')
        target.addClass('clipBrdAnim')
    })
}

/**
 * Toggle market videos number on '.infoCont's
 * 
 * @param {Jquery Object} $targetEl jQuery element '.infoCont' container
 */
function viewVideosNumber($parent_infoCont) {
    var $span_videosNum = $parent_infoCont.find('span.videosNum')
    var $marketlistCont = $parent_infoCont.find('.marketlistCont')
    var $videoRows_num = $parent_infoCont.find('.marketlistCont .market_videosID_list li.videoRow').length

    $span_videosNum.text("(" + $videoRows_num +")")

    if ($marketlistCont.hasClass('hide_marketlistCont')) {
        $span_videosNum.css('display', 'block')
    } else {
        $span_videosNum.css('display', 'none')
    }
}

/**
 * Event listener function for selecting videos IDs after AJAX request
 */
function postDataLoad_eventListeners() {
    /**
     * Filter market boxes and video rows according to filter controls
     */
    function filterDOM () {
        let marketVal = $('#sortMarketView_input').prop('value')
        marketVal = marketVal.toLowerCase().replace(/\s/g, '') // remove spaces
        let videoVal  = $('#sortVideoView_input').prop('value')
        $('#noFilterResultsMsgBox').remove() // remove eventual 'no results' box
        var format = /[ !@#$%^&*()_+\-=\[\]{};':"\\|,.<>\/?]/; //detect special charachters
        // set 'user selection' option label
        $('#sortUsersView_input span').text('users: ' + $('.userSelected:not(#allUserSelect)').length)
        // determine which loaded/unloaded buttons selected
        let loadUnloadBtn = $('.loadUnloadBtns.selectedBtn:not(#resetFilt)').attr('id')   
        
        // open market boxes
        $('.marketlistCont').removeClass('hide_marketlistCont')
        
        // filter markets -----------------------------------
        if (!marketVal) {
            $('.marketVidsCont').css('display', 'block')    
        } else if (!format.test(marketVal)) {
            $('.marketVidsCont').css('display', 'none')
            $('.marketVidsCont[data-marketname^=' + marketVal + ']').css('display', 'block')
        }
        
        // filter videos -----------------------------------
        // get selected users id
        let userIds = []
        $('#dropDownMenu ul > li.userSelected:not(#allUserSelect)').each( function () {
            userIds.push($(this).attr('data-userid'))
        })
        
        // filter for selected users
        $('.videoRow').css('display', 'none')
        userIds.forEach( id_user => {
            if (videoVal && !format.test(videoVal)) {
                // filter for searched videoID
                $('.videoRow[data-userid=' + id_user + '][data-videoid^=' + videoVal + ']').css('display', 'block')
            } else {
                $('.videoRow[data-userid=' + id_user + ']').css('display', 'block')
            }
            // determine loaded/unloaded videos
            if (loadUnloadBtn) {
                if (loadUnloadBtn.startsWith('loaded')) {
                    $('.videoRow:not(.checked)').css('display', 'none')
                } else if (loadUnloadBtn.startsWith('unloaded')) {
                    $('.videoRow.checked').css('display', 'none')
                }
            }
        })
        hideEmptyMarketBoxes()
        // show message on no results
        if($('#dataCont > .marketVidsCont[style="display: none;"]').length === $('#dataCont > .marketVidsCont').length) {
            $('#dataCont').append('<div id="noFilterResultsMsgBox" class="infoCont"><h2>No results</h2></div>')
        }
    }   

    /* ----------------------------------------------------------------------------------- */
    // Listeners ----------------------------------------------------------------------------
    /* ----------------------------------------------------------------------------------- */
    // market boxes filter controls arrow viewer button
    $('.filterCtrls_toggler').click(function () {
        $('#viewCtrls_cont').toggleClass('show_hideCtrls')
        $('#arrow_ctrlsViewer').toggleClass('toggledCtrls')
        $('#viewCtrls_label').toggleClass('show_hideLabel')
        $('#sortMarketView_input').focus()
        if (!$('#viewCtrls_cont').hasClass('show_hideCtrls')) {
            $('.loadUnloadBtns').removeClass('selectedBtn')
            $('#dropDownMenu').removeClass('showDropMenu')
            resetView ()
        }
    })

    // view sort input forms
    // users ------------------------------------
    $('#sortUsersView_input').click(function (event) {
        // show users list
        $('#dropDownMenu').toggleClass('showDropMenu')
    })
    // 'select all users' option
    $('#allUserSelect').click(function () {
        if ($('#dropDownMenu ul > li.userSelected').length === $('#dropDownMenu ul > li').length) {
            $('#dropDownMenu ul > li').removeClass('userSelected')
        } else {
            $('#dropDownMenu ul > li').addClass('userSelected')
        }
        // if non is selected, select current user
        // if ($('.userSelected').length <= 0) $('#dropDownMenu ul > li[data-userid=' + user.id + ']').addClass('userSelected')
        filterDOM ()
    })
    // user filter selection
    $('#dropDownMenu ul > li:not(:first-child)').click(function () {
        // style selected user row
        $(this).toggleClass('userSelected')
        if ($('#dropDownMenu ul > li.userSelected').length !== $('#dropDownMenu ul > li').length) {
            $('#allUserSelect').removeClass('userSelected')
        }
        // if non is selected, select current user
        // if ($('.userSelected').length <= 0) $('#dropDownMenu ul > li[data-userid=' + user.id + ']').addClass('userSelected')
        filterDOM ()
    })
    // market/video
    $('#sortMarketView_input, #sortVideoView_input').keyup(function () {
        filterDOM ()
    })
    // loaded/unloaded videos buttons
    // loaded/unloaded
    $('#loadedFiltBtn, #unloadedFiltBtn').click(function () {
        $(this).toggleClass('selectedBtn')
        if ($('.selectedBtn').length > 1) {
            $('.loadUnloadBtns').removeClass('selectedBtn')
            $(this).addClass('selectedBtn')
        }
        filterDOM()
    })
    // reset -----------------------------------------
    $('#resetFilt').click(function () {
        $('.loadUnloadBtns').removeClass('selectedBtn')
        $(this).addClass('selectedBtn')
        resetView ()
        // always remove user drop down list
        $('#dropDownMenu').removeClass('showDropMenu')
        // remove content in input forms
        $('#sortMarketView_input').prop('value', "")
        $('#sortVideoView_input').prop('value', "")
    })

    /* -------------------------------------------------------------- */

    // check video rows and instantly query to db
    $('.checkable').click(function (event) {
        var $target = $(event.target)
        if ( $target.hasClass('marketTitle_ctrls') || $target.hasClass('status_icons_cont') ) {
            $target = $target.parents('.checkable')
        }
        var checkBox = $target.find('input')
        var checked = checkBox.prop('checked')
        var checkedVal

        if ($target.hasClass('checkable')) {
            if (!checked) {
                checkedVal = true;
                $target.addClass('checked')
            } else {
                checkedVal = false;
                $target.removeClass('checked')
            }
            checkBox.prop('checked', checkedVal)
            
            videoID = $target.attr('data-id_video')
            marketID = $target.attr('data-id_market')
            
            $.post('php/post_requests.php', {
                market_id: marketID,
                id_video: videoID,
                loaded: checkedVal
            })
            if ($target.hasClass('videoRow')) {
                // determine if all videos are checked, if so close market box
                var $infoCont_parent = $target.parents('.infoCont')
                var videoRows = $infoCont_parent.find('.checkable')
                var checkedRows = $infoCont_parent.find('.checked')
                if (videoRows.length === checkedRows.length) {
                    $infoCont_parent.find('h2.marketTitle').addClass('completedMarketVids')
                    $infoCont_parent.find('.marketlistCont').addClass('hide_marketlistCont')
                    viewVideosNumber($infoCont_parent)
                } else {
                    $infoCont_parent.find('h2.marketTitle').removeClass('completedMarketVids')
                }
            }
        }
        updateLoadedVideos_compltdMarks ()
    }) 

    // market list container hides videos list on click
    $('.marketTitleSpan_cont:not(.infoCont_videoIDTitle)').click(function (event) {
        if (event.altKey) {
            // with alt click all market boxes are toggled
            $('h2#stats_market').trigger('click')    
        } else {
            var $target = $(event.target)
            var $infoCont = $target.parent()
            var $marketlistCont = $target.siblings('.marketlistCont')
            $marketlistCont.toggleClass('hide_marketlistCont')
            // display videos number
            viewVideosNumber($infoCont)
        }
    })

    $('.infoCont_videoIDTitle').click(function (event) {
        var videoID = $(this).find('.marketTitle').text()
        copyToClipboard(videoID, event)
    })

    // edit videos comments btn
    // (click is listened on all comments row section
    // but script will only work with the edit button click)
    $('.videoRows_notes').click(function (event) {
        var $target = $(event.target)
        
        // var $videoRow = $target.parents('.videoRow')
        var $videoRow = $target.parents('.videoRows_notes')
        var commentsFormbol
        $target.hasClass('onlyGenComm_editBtn') ? commentsFormbol = false : commentsFormbol = true
        
        // warning comment: get id of market and video
        var $specCommentEl = $videoRow.find('[data-marketcomm_id]')
        var specComment_marketId = $specCommentEl.attr('data-marketcomm_id')
        specComment_marketId = specComment_marketId ? specComment_marketId : ""
        var specComment_videoId = $specCommentEl.attr('data-videocomm_id')
        specComment_videoId = specComment_videoId ? specComment_videoId : ""
        // alert comment: get id of video
        var $genCommentEl = $videoRow.find('[data-videocommgen_id]')
        var genCommentEl_idVideo = $genCommentEl.attr('data-videocommgen_id')
        genCommentEl_idVideo = genCommentEl_idVideo ? genCommentEl_idVideo : ""

        if (!genCommentEl_idVideo) {
            genCommentEl_idVideo = specComment_videoId
        }

        // determine if edit button has been clicked
        if ($target.hasClass('editComment_btn')) {
            // request comments data and display the to comments form
            $.post('php/post_requests.php', {
                marketId       : specComment_marketId,
                videoId        : specComment_videoId,
                idVideo        : genCommentEl_idVideo,
                commentDataReq : true
            }, function (data) {
                if (data) {
                    var commentData = JSON.parse(data)
                    if (commentData['idMarket']) {
                        var market = {
                            id   : commentData['idMarket'],
                            name : commentData['marketName'],
                        }
                    } else {
                        market = "";
                    }
                    displayVideoCommentsForm(
                        market,
                        commentData['videoID'],
                        commentData['idVideo'],
                        commentData['specComment'],
                        commentData['genComment'],
                        commentsFormbol
                    )
                }
            })
        }
        // 'resolve warning comment' button
        else if ($target.hasClass('specResolve')) {
            $.post('php/post_requests.php', {
                marketId            : specComment_marketId,
                idVideo             : specComment_videoId,
                resolve_specComment : true
            }, function (data) {
                if (parseInt(data) === 1) { // on success
                    $('.inner_videoRows_specificNotes[data-marketcomm_id='+ specComment_marketId +'][data-videocomm_id='+ specComment_videoId +']').addClass('resolvedComments')
                    updateNotifHeaderButtons ()
                }
            })
        }
        // 'resolve alert comment' button
        else if ($target.hasClass('genResolve')) {
            $.post('php/post_requests.php', {
                idVideo            : genCommentEl_idVideo,
                resolve_genComment : true
            }, function (data) {
                if (parseInt(data) === 1) { // on success
                    $('.inner_videoRows_generalNotes[data-videocommgen_id='+ genCommentEl_idVideo +']').addClass('resolvedComments')
                    updateNotifHeaderButtons ()
                }
            })
        }
    })
}
/**
 * Functions for loaded data during
 * page refresh and data request
 * in one place
 */
function recallDataLoadedFunctions () {
    detectLoadedVids()
    detectComments()
    postDataLoad_eventListeners()
    closeUncloseMarketBoxes()
    updateSideStats()
    rowsOptionsEvents()
}

/**
 * Make AJAX request and load videos data list.
 * 
 * @param {string} dataHint String hint for php script to determine request
 */
function loadingGif () {
    var $dataCont = $('#dataCont')
    $dataCont.empty()
    setTimeout (function () {
        if ($('#dataCont .infoCont').length <= 0) {
            $dataCont.append("<img id='loadingGif' src='images/loading_blue_blackBknd.gif'>") // better to put in in HTML or CSS background
        }
    }, 225)
}
function requestData (dataHint, sortSearchPar = false) {
    var $dataCont = $('#dataCont')
    $dataCont.empty()
    // append loading gif if after interval data hasn't still been fetched
    loadingGif ()
    $dataCont.load('php/query_marketsResults.php', {
        searchHint: dataHint,
        sort: sortSearchPar
    }, function () {
        recallDataLoadedFunctions()
        updateDate(dataHint)
        hideEmptyMarketBoxes ()
        // resetViewUserVids ()
    })
}

/**
 * Returns a formatted date according to the passed date string:
 * Ex: '2019-11-24', returns: 'Sunday 24 November 2019'
 * 
 * @param {*} dateString date string
 */
function setDateFormat (dateString) {
    var weekDays = ['Sunday', 'Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday'];
    var months = ['January','February','March','April','May','June','July','August','September','October','November','December'];

    var date = new Date(dateString);

    var dateArr = {
        dayName :  weekDays[date.getDay()],
        dayNum  :  date.getDate(),
        month   :  months[date.getMonth()],
        year    :  date.getFullYear()
    }

    return dateArr
}

/**
 * Position calendar depending on window width
 */
function positionCalendar_dateCont_navBtns() {
    // containers
    var $inner_middleCont = $('#inner_middleCont')
    var $inner_rightCont = $('#inner_rightCont')
    // moving elements
    var $calendar = $('#calendarMainContainer')
    // var $dateContainer = $('#dateContainer')
    var $navBtnsCont = $('#navBtnsCont')
    // side stats
    var $sideStats = $('#sideStats_cont')
    
    if (window.innerWidth <= windowWidthThresh_narrow) {
        // mobile
        $inner_middleCont.prepend($navBtnsCont)
        $inner_middleCont.prepend($calendar)
        // $dateContainer.insertAfter($navBtnsCont)
        $calendar.css('display', 'none')
        $('#viewStats_onhover').append($sideStats)
    } else {
        // desktop
        $inner_rightCont.prepend($calendar)
        $inner_rightCont.prepend($navBtnsCont)
        $calendar.css('display', 'flex')
        $inner_rightCont.append($sideStats)
    }       
}

/**
 * Display viewing date into date box
 * 
 * @param {String} date String date. Format: yy-mm-dd, ex: 2019-11-26
 */
function updateDate(date) {
    return
    var dateText = setDateFormat(date);
    $('#dateContainer #inner_calendar_dateCont label').html(dateText.dayName + "<br>" + dateText.dayNum + " " + dateText.month + " " + dateText.year)
}

function closeFormOverlay () {
    $('#dark_overlay').css('display', 'none')
    $('#videoCommentsForm').css('display', 'none')
    $('body').css('overflow-y', 'auto');
}

function submitCommentsForm() {
    closeFormOverlay ()

    var videoCommentsCont    = $('#videoCommentsForm_innerCont_inputsCont')
    var videoID_specComm     = videoCommentsCont.find('[data-videocomm_id]').attr('data-videocomm_id')
    var videoID_genComm      = videoCommentsCont.find('[data-videocommgen_id]').attr('data-videocommgen_id')
    var marketID             = videoCommentsCont.find('[data-marketcomm_id]').attr('data-marketcomm_id')
    var specComment_textarea = videoCommentsCont.find('#specComment_textarea').prop('value')
    var genComment_textarea  = videoCommentsCont.find('#genComment_textarea').prop('value')

    if (!videoID_specComm) {
        specComment_textarea = "__NO_UPDATE_VALUE"
    }
    if (!videoID_genComm) {
        genComment_textarea = "__NO_UPDATE_VALUE"
    }
    
    var idVideo = videoID_specComm ? videoID_specComm : videoID_genComm
    
    // send data to server
    $.post('php/post_requests.php', {
        id_video: idVideo,
        market_id: marketID,
        specComment: specComment_textarea,
        genComment: genComment_textarea,
        submitComments: true
    }, function (data) {
        if (data.startsWith('1')) {
            // update warnings-alerts header number buttons values
            updateNotifHeaderButtons()

            if (specComment_textarea == "__NO_UPDATE_VALUE") {
                specComment_textarea = ""
            }
            if (genComment_textarea == "__NO_UPDATE_VALUE") {
                genComment_textarea = ""
            }
            if (!marketID) {
                marketID = 0  // will target no elements since no market is id = 0
            }

            // append comments ----------------------------------
            // in pages other than the daily videos list one:
            // display general alerts under videoID <h2> title
            $('[data-videocommgen_id=' + idVideo + ']').text(genComment_textarea)

            // warning target row
            let $specTargetLi = $('li[data-id_market=' + marketID + '][data-id_video=' + idVideo + ']')
            $specTargetLi.find('.inner_videoRows_specificNotes').text(specComment_textarea)
            $specTargetLi.find('.inner_videoRows_specificNotes').removeClass('resolvedComments')
            specComment_textarea ? $specTargetLi.addClass('highlightRow') : $specTargetLi.removeClass('highlightRow')
            // alert target row(s)
            let $genTargetLi = $('li[data-id_video=' + idVideo + ']')
            $genTargetLi.find('.inner_videoRows_generalNotes').text(genComment_textarea)
            $genTargetLi.find('.inner_videoRows_generalNotes').removeClass('resolvedComments')
            if (genComment_textarea) $genTargetLi.addClass('highlightRow')

            // view "resolve" button ----------------------------------
            // warnings
            let resolveBtn = $specTargetLi.find('.specEditCont_inner').find('svg')
            specComment_textarea ? resolveBtn.css('display', 'block') : resolveBtn.css('display', 'none')
            // alerts
            resolveBtn = $genTargetLi.find('.genEditCont_inner').find('svg')
            genComment_textarea ? resolveBtn.css('display', 'block') : resolveBtn.css('display', 'none')

            // for each genComment check if there are both comments, if true dislay <hr> ----------------------------------
            let $comment_editCont = $('.comment_editCont')
            for (let i = 0; i < $comment_editCont.length; i++) {
                let currComment_editCont = $comment_editCont.eq(i)
                let specNote = currComment_editCont.find('.inner_videoRows_specificNotes').text()
                let genNote = currComment_editCont.find('.inner_videoRows_generalNotes').text()

                // display <hr> with both comments nt empty
                specNote && genNote ? currComment_editCont.find('hr').css('display', 'block') : currComment_editCont.find('hr').css('display', 'none')
            }
            
            updateSideStats()
        }
    })
}
/**
 * Update the month container month title according to the passed date string
 * @param {*} dateString 
 */
function updateMonthTitle (dateString) {
    var $calendarMainContainer = $('#calendarMainContainer')
    $calendarMainContainer.attr('data-date_info', dateString)
    var nextMonthTitleArr = setDateFormat(dateString)
    $calendarMainContainer.find('#monthNameCont h4').text(nextMonthTitleArr["year"] + " " + nextMonthTitleArr["month"])
}

/**
 * Close market boxes when all videos are completed
 */
function closeUncloseMarketBoxes() {
    for (var i = 0; i < $('.market_videosID_list').length; i++) {
        var $market_videosID_list = $('.market_videosID_list').eq(i)
        var videoRows = $market_videosID_list.find('li.checkable')
        var checkedRows = $market_videosID_list.find('li.checked')
        
        if (videoRows.length == checkedRows.length) {
            // close market videos box
            $market_videosID_list.parents('.marketlistCont').addClass('hide_marketlistCont')
            // mark market title
            $infoCont_parent = $market_videosID_list.parents('.infoCont')
            $infoCont_parent.find('h2.marketTitle').addClass('completedMarketVids') // could place this into a function while passing it into the parent
            // view videos number
            viewVideosNumber ($infoCont_parent)
        }
    }
}

/**
* appear menu option for viewing notes - only on narrow width
*/
function sidenotesMenuOption () {
    if (window.innerWidth <= windowWidthThresh_midNarrow) {
        $('#dots_menu li[data-option="notes"]').css('display', 'block')
    } else {
        $('#dots_menu li[data-option="notes"]').css('display', 'none')
        // if notes are in the dataCont trigger the 'Today class', so you're not having two notes conts in the page
        if ($('#dataCont').children(':first-child').attr('id') ===  'inner_innerNoteContainer') {
            $('#inner_rigthNoteCont').empty()
            $('#inner_rigthNoteCont').append($('#dataCont').children())
            $('.todayBtn').trigger('click')
        }
    }
}

/* --------------------------------------------------------------------------------------------------------------------------
 _______  __   __  _______  __    _  _______    ___      ___   _______  _______  _______  __    _  _______  ______    _______
|       ||  | |  ||       ||  |  | ||       |  |   |    |   | |       ||       ||       ||  |  | ||       ||    _ |  |       |
|    ___||  |_|  ||    ___||   |_| ||_     _|  |   |    |   | |  _____||_     _||    ___||   |_| ||    ___||   | ||  |  _____|
|   |___ |       ||   |___ |       |  |   |    |   |    |   | | |_____   |   |  |   |___ |       ||   |___ |   |_||_ | |_____
|    ___||       ||    ___||  _    |  |   |    |   |___ |   | |_____  |  |   |  |    ___||  _    ||    ___||    __  ||_____  |
|   |___  |     | |   |___ | | |   |  |   |    |       ||   |  _____| |  |   |  |   |___ | | |   ||   |___ |   |  | | _____| |
|_______|  |___|  |_______||_|  |__|  |___|    |_______||___| |_______|  |___|  |_______||_|  |__||_______||___|  |_||_______|
 -------------------------------------------------------------------------------------------------------------------------- */

$(function () {
    recallDataLoadedFunctions()
    positionCalendar_dateCont_navBtns()
    updateNotifHeaderButtons()
    sidenotesMenuOption ()
    requestData ($('#calendarMainContainer').attr('data-date_info')) // load current day videos page

    $(window).resize(function() {
        positionCalendar_dateCont_navBtns()
        sidenotesMenuOption ()
    })
    // close different elements on outside click of those elements
    $(document).click(function (event) {
        // remove upper-right menu
        if ( $(event.target).attr('id') !== 'options_dots' ) {
            if ($('#dots_menu').hasClass('showMenu')) {
                $('#dots_menu').removeClass('showMenu')
            }
        }

        // video warning/alerts form: remove market name selector dropdown menu
        if ( $(event.target).attr('id') !== 'videoCommentsForm_marketIDTitle' ) {
            if ($('#marketSelectMenu').hasClass('showMenu')) {
                $('#marketSelectMenu').removeClass('showMenu')
            }
        }

        // remove users filter selectable dropdown menu in videos filter box 
        if ( $(event.target).attr('id') !== 'sortUsersView_input' && $(event.target).attr('id') !== 'dropDownMenu' && $(event.target).parents('#dropDownMenu').length == 0 ) {
            $('#dropDownMenu').removeClass('showDropMenu')
        }
        // remove calendar on mobile
        // remove calendar on mobile
        if ( window.innerWidth <= windowWidthThresh_narrow) {
            if ($(event.target).attr('id') !== 'calendar-selectBtn' && $(event.target).parents('#calendarMainContainer').length == 0) {
                $('#calendarMainContainer').css('display', 'none');
            }
        }
    })
    /*
    * nav buttons AJAX request
    */
    // clicking on one navBtn it highlights it and remove highlight on others
    $('.navBtns:not(#textArea_addBtn, .loadUnloadBtns, #sort_btn, .submitNoteBtn, #commentsVidSubmit)').click(function (event) {
        /*
        * maybe this could be placed inside listener function below ↓
        */
       $('.navBtns').removeClass('selectedBtn')
       $(event.target).addClass('selectedBtn')
       $('#dropDownMenu').removeClass('showDropMenu') // remove user selction filter options list 
    })
    // tomorrow/ yesterday buttons
    // $('.navBtns:not(#calendar-selectBtn, #textArea_addBtn, .notifBtns, #sort_btn, .loadUnloadBtns, #unloadedBtn_gen)').click(function (event) {
    $('.todayBtn').click(function (event) {
        var $target = $(event.target)
        var dataHint = $target.attr('data-hint')
        requestData (dataHint, sortSearch)

        // remove calendar clicking another button
        $('.dayNumsDivs').removeClass('selectedDate')
        $('.dayNumsDivs[data-qhint="' + dataHint + '"]').addClass('selectedDate')

        if ($target.hasClass('todayBtn')) {
            // update month title
            updateMonthTitle (dataHint)
            // move calendar to current date
            var $calendar_slider = $('#calendar_slider')
            var $monthSlides = $calendar_slider.find('.monthSlide')
            var $dayNumsDivs = $calendar_slider.find('.dayNumsDivs')

            $monthSlides.removeClass('currentSlide')
            $monthSlides.eq(0).addClass('currentSlide')
            $dayNumsDivs.removeClass('selectedDate')
            $calendar_slider.css({
                "transform": "translateX(0px)"
            }) 
        }
    })

    $('#sort_btn').click(function () {
        // get date from calendar (last selected date)
        var date_info = $('#calendarMainContainer').attr('data-date_info')
        sortSearch = !sortSearch
        $(this).toggleClass('sortBtn_selectedBtn')
        requestData (date_info, sortSearch)
    })

    /* ----------------------------------------------------------------------------- */
    /* SEARCH VIDEO INPUT ---------------------------------------------------------- */
    /* ----------------------------------------------------------------------------- */
    
    // search input form, search is done pressing 'enter'
    $('#searchVideo_input').keypress(function (event) {
        if (event.which == 13 || event.keyCode == 13) {
            var idToSearch = $(this).prop('value')
            idToSearch = idToSearch.trim()
            // request video/markets list
            $('.navBtns').removeClass('selectedBtn')
            if (idToSearch) {
                loadingGif ()
                $('#dataCont').load('php/query_videosResults.php', {
                    id_video: idToSearch
                }, function () {
                    updateSideStats()
                    rowsOptionsEvents()
                    postDataLoad_eventListeners()
                })
                $(this).prop('value', '')
            }
        }
    })

    // $('#searchVideo_input').focus(function () {
    //     console.log('changed')
    //     console.log($(this).prop('value'))
    // })
    // // on focus, right mouse click will paste from clipboard
    // $('#searchVideo_input').change(function () {
    //     console.log('changed')
    //     $(this).trigger('contextmenu')
    //     var videoID = $(this).prop('value')
    //     /*
    //     * If videoID is copied from market boxes then format will be: '1234_language'.
    //     * VideoID search accepts only numbers, so videoID will be extracted from above format.
    //     */
    //     var value_split = videoID.split('_')
    //     if (value_split.length > 1) {
    //         videoID = value_split[0]
    //     }
    //     $(this).prop('value', videoID)
    // } )
    // $('#searchVideo_input').contextmenu(function (event) {
    //     event.preventDefault()
    //     navigator.clipboard.readText().then( text => {
    //         // var videoID = text
    //         // /*
    //         // * If videoID is copied from market boxes then format will be: '1234_language'.
    //         // * VideoID search accepts only numbers, so videoID will be extracted from above format.
    //         // */
    //         // var text_split = text.split('_')
    //         // if (text_split.length > 1) {
    //         //     videoID = text_split[0]
    //         // }
    //         // $(this).prop('value', videoID)
    //         $(this).trigger('focus')
    //     });
    // })

    /* ----------------------------------------------------------------------------- */
    /* HEADER BUTTONS -------------------------------------------------------------- */
    /* ----------------------------------------------------------------------------- */
    // three dots header button: show/hide menu
    $('#options_dots').click(function () {
        $('#dots_menu').toggleClass('showMenu')
    })
    $('#dots_menu li').click(function () {
        let option = $(this).attr('data-option')
        $('.navBtns').removeClass('selectedBtn')
        switch (option) {
            case "notes":
                // get the notes container children and append it to the container in the center
                $('#dataCont').empty()
                $('#dataCont').append($('#inner_rigthNoteCont').children().clone(true))
            break;
            case "unloaded":
                loadingGif ()
                $('#dataCont').load('php/query_reqResults.php', {
                    unloaded_req: true
                }, function () {
                    updateSideStats()
                    postDataLoad_eventListeners()
                    rowsOptionsEvents ()
                })
            break;
            case "blocked":
                loadingGif ()
                $('#dataCont').load('php/query_reqResults.php', {
                    blocked_req: true
                }, function () {
                    updateSideStats()
                    postDataLoad_eventListeners()
                    rowsOptionsEvents ()
                })  
            break;
            case "logout":
                $.post('index.php', {
                    'logOut' : true
                })
                .done(function() {
                    window.open('../index.php', "_self")
                })
            break
        }
    })

    // warnings - yellow notif
    $('#warningsBtn_gen').click(function () {
        loadingGif ()
        $('#dataCont').load('php/query_reqResults.php', {
            warnings_req: true
        }, function () {
            updateSideStats()
            postDataLoad_eventListeners()
            rowsOptionsEvents ()
        })
    })

    // alerts - red notif
    $('#alertsBtn_gen').click(function () {
        loadingGif ()
        $('#dataCont').load('php/query_alertsResults.php', {
            alerts_req: true
        }, function () {
            updateSideStats()
            postDataLoad_eventListeners()
            rowsOptionsEvents ()
        })
    })

    // $('#logOutBtn').click(function () {
    //     console.group('logOut')
    //     $.post('index.php', {
    //     // $.post('php/query_reqResults.php', {
    //         'logOut' : true
    //     }, function (data) {
    //         console.log(data)
    //         console.groupEnd()
    //     })
    // })

    

    /* ----------------------------------------------------------------------------- */
    /* SIDE STATS ------------------------------------------------------------------ */
    /* ----------------------------------------------------------------------------- */

    // Markets title: toggles videos list hiding on all current markets
    $('h2#stats_market').click(function () {
        var $marketlistCont = $('.marketlistCont:not(.warnings_listCont, .videosResults_listCont)')
        var hidden = false;

        $('.videoRow').removeClass('videoRow_notesHiglight')
        $('.videoRows_notes').removeClass('viewVideoNote')
        
        if ($('.hide_marketlistCont').length == $marketlistCont.length) {
            hidden = true;
        }
        
        for (var i = 0; i < $marketlistCont.length; i++) {
            var $currMarketListCont_parent = $marketlistCont.eq(i).parent()
            if (hidden) {
                $marketlistCont.eq(i).removeClass('hide_marketlistCont')
            } else {
                $marketlistCont.eq(i).addClass('hide_marketlistCont')
            }
            viewVideosNumber($currMarketListCont_parent)
        }
    })

    // Video warnings (orange comments/errors)
    $('#stats_specificVideosErrors').click(function () {
        var $specWarnings =  $('.inner_videoRows_specificNotes')
        viewErrorsWarnings ($specWarnings)
    })
    // Video alerts (red comments/errors)
    $('#stats_videosErrors').click(function () {
        var $genWarnings =  $('.inner_videoRows_generalNotes')
        viewErrorsWarnings ($genWarnings)
    })

    /* ----------------------------------------------------------------------------- */
    /* VIDEO COMMENTS -------------------------------------------------------------- */
    /* ----------------------------------------------------------------------------- */

    // dark overlay: remove overlay and the videos comments form
    $('#dark_overlay').click(function () {
        $('#closeFormCont').trigger('click')
    })
    $('#closeFormCont').click(function () {
        closeFormOverlay ()
    })
    // send comments pressing enter
    $('.videoCommentsForm_textarea').keypress(function (event) {
        if (event.which == 13 || event.keyCode == 13) {
            $('#commentsVidSubmit').trigger('click')
        }
    })
    $('#commentsVidSubmit').click(function () {
        submitCommentsForm()
    })


    /* ----------------------------------------------------------------------------- */
    /* CALENDAR -------------------------------------------------------------------- */
    /* ----------------------------------------------------------------------------- */

    // calendar slider arrows/carousel handler
    $('#monthNameCont').click(function (event) {
        var $target_arrow = $(event.target)
        var $calendarMainContainer = $('#calendarMainContainer')
        var todayDate_string = $calendarMainContainer.attr('data-date_info')
        var $calendar_slider = $('#calendar_slider')
        var $monthSlide = $('.monthSlide')
        var $monthSlide_first = $monthSlide.eq($monthSlide.length - 1)
        var monthSlide_first_dims = $monthSlide_first[0].getBoundingClientRect()
        var currSlide_index = parseInt($('.currentSlide').attr('data-slideindex'))

        // year-month info to month days container
        // get previous month date info
        var dateSplit = todayDate_string.split('-')
        var dateSplit_year  = dateSplit[0]
        var dateSplit_month = dateSplit[1] - 1
        
        if ($target_arrow.hasClass('fa-caret-left')) {
            // calendar left <-
            var prevMonth_firstDay = new Date(dateSplit_year, dateSplit_month - 1, 1);
            var prevMonth_lastDay = new Date(dateSplit_year, dateSplit_month, 0);
            var prevMonth_firstDay_weekDay = prevMonth_firstDay.getDay()
            var prevMonth_lastDay_num = prevMonth_lastDay.getDate()
    
            // sundays will be numbered with 7 so week days will be numbered:
            // 1-7 / Monday-Sunday
            if (prevMonth_firstDay_weekDay == 0) {
                prevMonth_firstDay_weekDay = 7
            }
            // determine prev month date info
            // year
            var prevMonth_yearNum = dateSplit_year
            // month
            var prevMonth_monthNum = dateSplit_month
            if (dateSplit_month < 1) {
                prevMonth_monthNum = 12
                prevMonth_yearNum = dateSplit_year - 1
            }

            var monthCont_dateInfo = prevMonth_yearNum + "-" + prevMonth_monthNum
            // ..and year-month info to month days container
            updateMonthTitle (monthCont_dateInfo)
            /* 
            $calendarMainContainer.attr('data-date_info', monthCont_dateInfo)
            var prevMonthTitleArr = setDateFormat(monthCont_dateInfo)
            $calendarMainContainer.find('#monthNameCont h4').text(prevMonthTitleArr["year"] + " " + prevMonthTitleArr["month"])
            */
            if ($('.currentSlide').attr('data-slideindex') == $monthSlide.length) {
                // MUST FIRST CREATE PREVIOUS MONTH!
                // create new month and append it
                var $calendar_clone = $monthSlide_first.clone(true)
                var $dayNumsDivs = $calendar_clone.find('.dayNumsDivs')

                // remove 'nextDays' class and 'currentDay' id
                $dayNumsDivs.removeClass('nextDays')
                $dayNumsDivs.removeClass('emptyDays')
                $dayNumsDivs.removeClass('sundaysDays')
                $dayNumsDivs.removeClass('selectedDate')
                $dayNumsDivs.removeAttr('id')
                
                var d = 0
                var dayNumStr = 0;
                for (var i = 1; i <= $dayNumsDivs.length; i++) {
                    // determine days to display
                    var emptyDaysClass = ''
                    
                    if ((i >= prevMonth_firstDay_weekDay) && ((d + 1) <= prevMonth_lastDay_num)) {
                        dayNumStr = ++d;
                        var dataDateAttr = prevMonth_yearNum + "-" + prevMonth_monthNum  + "-" + dayNumStr
                    } else {
                        dayNumStr = ''
                        dataDateAttr = ''
                        emptyDaysClass = 'emptyDays'
                    }
                    // determine sundays
                    var sundayClass = ""
                    if (!(i % 7)) {
                        sundayClass = "sundaysDays"
                    }
                    // ..finally append all to day container
                    $dayNumsDivs.eq(i - 1).find('label').text(dayNumStr)
                    $dayNumsDivs.eq(i - 1).attr('data-qhint', dataDateAttr)
                    $dayNumsDivs.eq(i - 1).addClass(emptyDaysClass + " " + sundayClass)
                }
                
                // position slide to left side
                $calendar_clone.css('right', (monthSlide_first_dims.width * $monthSlide.length))
                // assign index
                var lastIndex = parseInt($calendar_clone.attr('data-slideindex'))
                $calendar_clone.attr('data-slideindex', lastIndex + 1)
                // finally append it
                $calendar_slider.append($calendar_clone)
            }
            
            // assign 'currentSlide' class
            $monthSlide.removeClass('currentSlide')
            $('.monthSlide[data-slideindex='+ (currSlide_index + 1) +']').addClass('currentSlide')
            
            // move slider to new month slide
            $calendar_slider.css({
                "transform": "translateX(" + monthSlide_first_dims.width * currSlide_index + "px)"
            }) 

        } else if ($target_arrow.hasClass('fa-caret-right')) {
            // calendar right ->
            // don't move the slider when arrived at current month
            if ($('.currentSlide').attr('data-slideindex') > 1) {
                $monthSlide.removeClass('currentSlide')
                $('.monthSlide[data-slideindex='+ (currSlide_index - 1) +']').addClass('currentSlide')
                // slide slider
                if ($monthSlide.length > 1) {
                    $calendar_slider.css({
                        "transform": "translateX(" + (monthSlide_first_dims.width * ((parseInt($('.currentSlide').attr('data-slideindex')))-1)) + "px)"
                    }) 
                }
                // year-month info to month days container
                var date_info =  $calendarMainContainer.attr('data-date_info')
                var dateSplit_year  = parseInt(dateSplit[0])
                var dateSplit_month = parseInt(dateSplit[1]) + 1
                if (dateSplit_month <= 0) {
                    dateSplit_year -= 1
                    dateSplit_month = 12
                }
                if (dateSplit_month > 12) {
                    dateSplit_year += 1
                    dateSplit_month = 1
                }
                var dateConcat = dateSplit_year + "-" + dateSplit_month
                updateMonthTitle (dateConcat)
                /* 
                $calendarMainContainer.attr('data-date_info', dateConcat)
                var nextMonthTitleArr = setDateFormat(dateConcat)
                $calendarMainContainer.find('#monthNameCont h4').text(nextMonthTitleArr["year"] + " " + nextMonthTitleArr["month"])
                */
            }
        }
    }) 

    // calendar date selector
    $('#calendar-selectBtn').click(function () {
        if (window.innerWidth <= windowWidthThresh_narrow) {
            var cssDisplay = "";
            var $calendarCont = $('#calendarMainContainer')
            var calendarStyleAttr = $calendarCont.attr('style')
            if (!calendarStyleAttr || calendarStyleAttr.includes('none')) {
                cssDisplay = 'flex';
            } else {
                cssDisplay = 'none';
            }
            $calendarCont.css('display', cssDisplay);
        }
    })

    // date selectors
    $('.monthSlide').click(function (event) {
        var $target = $(event.target);
        if ($target.hasClass('dayNumsDivs') && !$target.hasClass('emptyDays') ) {
            var dataHint = $target.attr("data-qHint");
        
            $('.dayNumsDivs').removeClass('selectedDate')
            $target.addClass('selectedDate')
            $('#calendarMainContainer').attr('data-date_info', dataHint)
            requestData (dataHint, sortSearch)
            
            if (window.innerWidth <= windowWidthThresh_narrow) {
                // mobile
                $('#calendarMainContainer').css('display', 'none');
            } else {
                // desktop
                $('.navBtns').removeClass('selectedBtn')
                $('.navBtns[data-hint="' + dataHint + '"]').addClass('selectedBtn')
            }
        }
    })
    
    /* ----------------------------------------------------------------------------- */
    /* SIDE NOTES ------------------------------------------------------------------ */
    /* ----------------------------------------------------------------------------- */

    // notes are copied for narrower window widths into the dataCont,
    // so when submitting a change copy back to original container the modified notes
    function updateRealSideNotes() {
        if (window.innerWidth <= windowWidthThresh_midNarrow) {
            $('#inner_rigthNoteCont').empty()
            $('#inner_rigthNoteCont').append($('#dataCont').children().clone(true))
        }
    }

    // add note button
    $('#textArea_addBtn').click(function (event) {
        let $parent = $(this).parent()
        var $noteMainCont = $parent.find('.noteMainCont')
        var $noteMainCont_last = $noteMainCont.eq($noteMainCont.length - 1)
        var $noteMainCont_clone = $noteMainCont_last.clone(true)

        // empty noteId attribute and <p> text
        $noteMainCont_clone.attr('data-noteid', '')
        $noteMainCont_clone.find('p.noteTextarea').text('')
        // give the empty <p> some css height:
        // beacuse textarea gets the <p> css height,
        // if text in <p> is empty then textarea won't be visible
        $noteMainCont_clone.find('p.noteTextarea').css('height', '70px')
        $noteMainCont_clone.find('.removeNoteBtn').css('display' ,'block')
        // append new note element
        $noteMainCont.eq($noteMainCont.length - 1).after($noteMainCont_clone)

        // click on <p> and textarea is focused and you are ready to write text
        $('.noteMainCont:last-child').find('.editNoteBtn').click()
    })

    // remove note btn
    $('.removeNoteBtn').click(function (event) {
        let $noteMainCont = $(this).parents('.noteMainCont')
        let noteID = $noteMainCont.attr('data-noteid')
        let $parent = $noteMainCont.parent()

        $.post('php/post_requests.php', {
            removeNote: true,
            id_Note : noteID
        }) 
        // display default note block if all were deleted
        if ($parent.find('.noteMainCont').length <= 1) {
            $noteMainCont.attr('data-noteid', '')
            $noteMainCont.find('.removeNoteBtn').css('display', 'none')
            $noteMainCont.find('p.noteTextarea').text('I am the first note, edit me or add more notes!')
            $noteMainCont.find('textarea.noteTextarea').prop('value', '')
        } else {
            $noteMainCont.remove()
        }
        updateRealSideNotes()
    })


    // able textarea on note <p> tags click
    $('.editNoteBtn').click(function (event) {
        var $parent = $(event.target).parents('.noteMainCont')
        var $pTag_noteTextarea = $parent.find('p.noteTextarea')
        var event_target_height = $pTag_noteTextarea[0].getBoundingClientRect().height
        var textarea_sibling = $pTag_noteTextarea.siblings('textarea.noteTextarea')
        var noteText = $pTag_noteTextarea[0].textContent
        var submitNoteBtn = $parent.find('.submitNoteBtn')
        var note_inserted = $parent.find('.note_inserted') // note inserted date element

        textarea_sibling.empty()
        textarea_sibling[0].value = noteText
        $pTag_noteTextarea.css('display', 'none')
        note_inserted.css('display', 'none')
        submitNoteBtn.css('display', 'block')
        textarea_sibling.css({
            display: 'block',
            height: event_target_height + 'px',
        })
        textarea_sibling.focus()
    })
    // in focus textarea enter will 'submit' textarea
    $('textarea.noteTextarea').focus(function () {
        let $noteMainCont = $(event.target).parents('.noteMainCont')
        // hide buttons options
        $noteMainCont.find('.noteBtns_cont').css('display', 'none')
        let submitBtn = $noteMainCont.find('.submitNoteBtn')

        $(this).keypress(function (event) {
            if (event.which == 13 || event.keyCode == 13) { // pressing Enter key
                submitBtn.trigger('click')
            }
        })
    })
    // display <p> tag on textarea blur
    // $('textarea.noteTextarea').blur(function (event) { // BLUR IS DONW TWICE !! (MAYBE SHOUDL CHANGE METHOD OF SUBMITTING
    $('.submitNoteBtn').click(function (event) {
        var $parent = $(event.target).parents('.noteMainCont')
        var $textarea_noteTextarea = $parent.find('textarea.noteTextarea')
        var noteText = $textarea_noteTextarea[0].value.trim()
        var pTag_sibling = $textarea_noteTextarea.siblings('p')
        var $noteMainCont = $textarea_noteTextarea.parents('.noteMainCont')
        var noteID = $noteMainCont.attr('data-noteid')
        var submitNoteBtn = $parent.find('.submitNoteBtn')

        $textarea_noteTextarea.css('display', 'none')
        pTag_sibling.text(noteText)
        pTag_sibling.css('display', 'block')

        // if note is left empty remove it
        if(!noteText) {
            var $noteMainCont_lastChild = $('.noteMainCont:last-child')
            if ($('.noteMainCont').length == 1) {
                var $noteMainCont_lastChild_clone = $noteMainCont_lastChild.clone(true)
                $noteMainCont_lastChild_clone.insertBefore($noteMainCont_lastChild)
                
                $noteMainCont_lastChild_clone.attr('data-noteid', '')
                $noteMainCont_lastChild_clone.find('p.noteTextarea').text('No notes, write here something!')
                // $noteMainCont_lastChild_clone.find('p.noteTextarea').css('height', '35px')
                $noteMainCont_lastChild_clone.find('p.noteTextarea').addClass('noNotesMsg')
            }
            // remove note triggering remove button
            $noteMainCont.find('.removeNoteBtn').trigger('click')
        } else {
            pTag_sibling.css('height', '')
            pTag_sibling.removeClass('noNotesMsg')
            if (noteID) {
                // update note
                $.post('php/post_requests.php', {
                    updateNote: noteText,
                    id_Note : noteID
                }) 
            } else {
                // insert note
                $.post('php/post_requests.php', {
                    insertNote: noteText,
                    user_id : user.id
                }, function (data) {
                    // last 'id_note' is returned
                    $noteMainCont.attr('data-noteid', data)
                }) 
            }
        }
        $parent.find('.noteBtns_cont').css('display', 'block')
        $parent.find('.note_inserted').css('display', 'block')
        submitNoteBtn.css('display', 'none')
        updateRealSideNotes()
    })
})
    