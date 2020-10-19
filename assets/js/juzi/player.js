(function (global__, $) {
    global__.mp3Player = function (opt) {
        var domStr = opt.dom || 'player_id';
        var playUrl = opt.playUrl || '';
        var playerDom;
        var resetProgress;//是否正在重定位播放进度
        if (typeof domStr == 'string') {
            playerDom = $('#' + domStr);
        } else {
            playerDom = domStr;
        }
        var auPlayer,
            progressArea = playerDom.find('.progress_area'),
            playingBar = playerDom.find('.playing_bar'),
            timeBar = playerDom.find('.time_bar'),
            hoverTime = playerDom.find('.hover_time'),
            hoverBg = playerDom.find('.hover_bg'),
            playPauseButton = playerDom.find(".play_pause_button"),
            currentTme = timeBar.find('.current_time'),
            seekT, seekLoc, playingBarPos, cM, ctMinutes,
            ctSeconds, curSeconds,
            durSeconds, playProgress, nTime = 0, buffInterval = null, tFlag = false
        ;

        function showHover(event) {
            playingBarPos = progressArea.offset();
            seekT = event.clientX - playingBarPos.left;
            seekLoc = auPlayer.duration * (seekT / progressArea.outerWidth());
            hoverBg.width(seekT);
            cM = seekLoc / 60;
            ctMinutes = Math.floor(cM);
            ctSeconds = Math.floor(seekLoc - ctMinutes * 60);
            if (ctSeconds < 0) return;
            if (ctSeconds < 0) return;
            if (isNaN(ctSeconds)) {
                hoverTime.text('--');
            } else {
                hoverTime.text(formatSeconds(seekLoc));
            }
            hoverTime.css({'left': seekT, 'margin-left': '-21px'}).fadeIn(0);

        }

        function hideHover() {
            hoverBg.width(0);
            hoverTime.text('0').css({'left': '0px', 'margin-left': '0px'}).fadeOut(0);
        }
        //播放器api对象
        var thisObj = {
            domObj: playerDom,
            isplay: false,
            setUrl: function (url) {
                auPlayer.src = url;
                return this;
            },
            isPlaying: function () {
                return this.isplay;
            },
            cmdPlay: function () {
                //控制一次只能播放一个语音 获取上次播放的缓存对象
                if (global__.mp3Player.playObj && global__.mp3Player.playObj.isPlaying()) {
                    global__.mp3Player.playObj.cmdPause();
                }
                global__.mp3Player.playObj = this;
                checkBuffering();
                playPauseButton.addClass('playing');
                if (auPlayer.play) {
                    this.isplay = true;
                    try {
                        auPlayer.play();
                    } catch (e) {
                        console.log(e);
                    }
                }
                return this;
            },
            cmdPause: function () {
                global__.mp3Player.playObj = null;
                clearInterval(buffInterval);
                playPauseButton.removeClass('playing');
                auPlayer.pause();
                this.isplay = false;
                return this;
            },
            hide: function () {
                playerDom.hide();
                return this;
            },
            show: function () {
                playerDom.show();
                return this;
            },
        };
        function updateCurrTime() {
            if (resetProgress) {
                // console.log('resetProgress!');
                return;
            }
            nTime = new Date();
            nTime = nTime.getTime();
            if (!tFlag) tFlag = true;

            // curSeconds = Math.floor(audio.currentTime - curMinutes * 60); //不支持超过1分钟的音频
            curSeconds = Math.floor(auPlayer.currentTime);

            // durSeconds = Math.floor(audio.duration - durMinutes * 60);  //不支持超过1分钟的音频
            durSeconds = Math.floor(auPlayer.duration);
            playProgress = (auPlayer.currentTime / auPlayer.duration) * 100;

            if (isNaN(curSeconds))
                currentTme.text('');
            else
                currentTme.text(formatSeconds(curSeconds));
            if (isNaN(curSeconds) || isNaN(durSeconds))
                timeBar.removeClass('active');
            else
                timeBar.addClass('active');
            playingBar.width(playProgress + '%');
            if (playProgress == 100) {
                playingBar.width(0);
                currentTme.text('');
            }
        }
        function checkBuffering() {
            clearInterval(buffInterval);
        }
        function initStyle() {
            playingBar.width(0);
            currentTme.text('');
            nTime = 0;
        }
        function initPlayer(url) {
            auPlayer = new Audio();
            auPlayer.loop = true;
            auPlayer.autoplay = true;
            initStyle();
            playPauseButton.on('click', function () {
                if (auPlayer.paused) {
                    thisObj.cmdPlay();
                }
                else {
                    thisObj.cmdPause();
                }
            });
            progressArea.mousemove(function (event) {
                showHover(event);
            });
            progressArea.mouseout(hideHover);
            progressArea.on('click', function () {
                //如果语音正在播放 要暂停
                if (thisObj.isplay) auPlayer.pause();
                if (auPlayer.currentTime) auPlayer.currentTime = seekLoc;
                playingBar.width(seekT);
                resetProgress = true;
                setTimeout(function () {
                    resetProgress = false;
                }, 100);
                if (thisObj.isplay) {
                    setTimeout(function () {
                        auPlayer.play();
                    }, 100);
                }

            });
            $(auPlayer).on('timeupdate', updateCurrTime);
            thisObj.setUrl(url);
        }
        initPlayer(playUrl);
        return thisObj;
    };
    var playingPlayer = null;
    //语音播放对象
    global__.showVoicePlayer = function (appendObj, voiceUrl, songTime) {
        songTime = formatSeconds(songTime);
        var playerDom = appendObj.find('.juziMusicPlayer');
        if (playerDom.length == 0) {
            playerDom = $('<div class="juziMusicPlayer">\n' +
                '    <div class="player_left">\n' +
                '        <div class="play_pause_button playing">\n' +
                '        </div>\n' +
                '    </div>\n' +
                '    <div class="player_right">\n' +
                '        <div class="time_bar">\n' +
                '            <div class="current_time"></div>\n' +
                '            <div class="all_length">' + songTime + '</div>\n' +
                '        </div>\n' +
                '        <div class="progress_area">\n' +
                '            <div class="hover_time"></div>\n' +
                '            <div class="hover_bg"></div>\n' +
                '            <div class="playing_bar"></div>\n' +
                '        </div>\n' +
                '    </div>\n' +
                '</div>');
            playingPlayer = new mp3Player({
                dom: playerDom,
                playUrl: voiceUrl,
            });
            appendObj.append(playerDom);
        } else {
            playingPlayer.setUrl(voiceUrl).cmdPlay();
        }
    };
    // showVoicePlayer(appendVoiceWhere, url, len);


    /**
     * 格式化秒
     * @param int  value 总秒数
     * @return string result 格式化后的字符串
     */
    function formatSeconds(value) {
        var theTime = parseInt(value);// 需要转换的时间秒
        var theTime1 = 0;// 分
        var theTime2 = 0;// 小时
        var theTime3 = 0;// 天
        if(theTime > 60) {
            theTime1 = parseInt(theTime/60);
            theTime = parseInt(theTime%60);
            if(theTime1 > 60) {
                theTime2 = parseInt(theTime1/60);
                theTime1 = parseInt(theTime1%60);
                if(theTime2 > 24){
                    //大于24小时
                    theTime3 = parseInt(theTime2/24);
                    theTime2 = parseInt(theTime2%24);
                }
            }
        }
        var result = '';
        if(theTime > 0){ //second
            result =  (theTime.toString().length==1 ? '0'+ theTime : parseInt(theTime));
        }
        if(theTime1 > 0) {//min
            result = ""+ (theTime1.toString().length==1 ? '0'+ theTime1 : parseInt(theTime1)) + ":"+ result;
        }
        if(theTime2 > 0) {//hour
            result = ""+parseInt(theTime2)+":"+result;
        }
        if(theTime3 > 0) {//days
            result = ""+parseInt(theTime3)+":"+result;
        }
        return result;
    }
})(this, jQuery);