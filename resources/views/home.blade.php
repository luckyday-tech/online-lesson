@extends('layouts.app')

<?php

use App\Models\VideoChatManager;
?>
@section('content')
<main class="ol__main">
    <div class="ol__side">
        <video id="js-host-stream" controls class="ol__host_video"></video>
        <div class="ol__chat">
            <div class="ol__chart-text-panel">
                <!--
                <div class="ol__chat-partner">
                    <div class="ol__avatar ">
                        <img class="ol__avatar-size-40" src="../images/avatar-teacher.png" />
                    </div>
                    <div class="ol__chat-content">
                        <div class="ol__chat-time">山下, 12:00 PM</div>
                        <div class="ol__chat-text">こんにちは。現役メイクアップアーティストの化粧美咲と申します！よろしくお願いします！</div>
                    </div>
                </div>
                <div class="ol__chat-self">
                    <div class="ol__chat-content">
                        <div class="ol__chat-time">12:00 PM</div>
                        <div class="ol__chat-text">こんにちは。現役メイクアップアーティストの化粧美咲と申します！よろしくお願いします！</div>
                    </div>
                </div>
                -->
            </div>
            <div class="ol__chat-control-panel">
                <div class="ol__chat-text">
                    <i class="far fa-smile"></i>
                    <input type="text" id="txt_send_message" placeholder="メッセージを入力..." />
                </div>
                <div class="ol__btn ol__btn-action ol__btn-blue js-btn-send-message"><i class="fas fa-paper-plane"></i></div>
            </div>
        </div>
    </div>
    <div class="ol__body">
        <div class="ol__board">
            <video id="js-host-screen" controls class="ol__host_screen"></video>

        </div>
        <div class="ol__student-list">
            <div id="js-student-streams" class="owl-carousel owl-theme">
                <!--<div class="ol__student-video"></div>-->
            </div>
        </div>
    </div>
</main>
@endsection

@section('page_js')
<script>
    var stream_list = [];
    var is_allow_recog = 0;
    var is_allow_trans = 0;

    const IS_HOST = "{{$is_host}}";
    const VIDEO_HOST_ID = "{{$host_id}}_v";
    const SCREEN_HOST_ID = "{{$host_id}}_s";

    const VIDEO_ROOM_ID = "{{$room_id}}_v";
    const SCREEN_ROOM_ID = "{{$room_id}}_s";

    const sora = Sora.connection('wss://abacus-platform.com/signaling', false);
    const options = {
      multistream: true,
    }

    if (IS_HOST == 1) {
        options['clientId'] = VIDEO_HOST_ID;
        options['connectionId'] = VIDEO_HOST_ID;
    } else {
        options['clientId'] = "{{VideoChatManager::generatePeerId()}}";
        options['connectionId'] = "{{VideoChatManager::generatePeerId()}}";
    }

    const sora_sendrecv = sora.sendrecv(VIDEO_ROOM_ID, null, options);

    $('.owl-carousel').owlCarousel({
        loop: false,
        margin: 11,
        items: 4,
        onRefreshed: onCarouselRefreshed,
    })

    function onCarouselRefreshed(event) {
        for (var index = 0; index < stream_list.length; index++) {
            var new_video = document.getElementById(stream_list[index].peerId);

            if (new_video == null)
                continue;

            if (new_video.classList.contains('ol__student-video'))
                continue;

            new_video.srcObject = stream_list[index];
            new_video.playsInline = true;
            new_video.setAttribute('data-peer-id', stream_list[index].peerId);
            new_video.setAttribute('class', 'ol__student-video');

            new_video.play().catch(console.error);
        }
    }

    function getCurrentTime() {
        var currentdate = new Date();

        var hour = '';
        var minute = '';

        if (currentdate.getHours() < 10) {
            hour = '0' + currentdate.getHours();
        } else {
            hour = currentdate.getHours();
        }

        if (currentdate.getMinutes() < 10) {
            minute = '0' + currentdate.getMinutes();
        } else {
            minute = currentdate.getMinutes();
        }

        return hour + ":" + minute;
    }

    function addChatByMe(message) {
        if (is_allow_trans == 0) {
            var html = "<div class='ol__chat-self'><div class='ol__chat-content'><div class='ol__chat-time'>" + getCurrentTime() + "</div><div class='ol__chat-text'>" + message + "</div></div></div>";
            $('.ol__chart-text-panel').append(html);
        } else {
            addChatByMeTrans(message);
        }
    }

    function addChatByPartner(message, name, avatar_url) {
        if (is_allow_trans == 0) {
            var html = "<div class='ol__chat-partner'><div class='ol__avatar'><img class='ol__avatar-size-40' src='" + avatar_url + "'></div><div class='ol__chat-content'><div class='ol__chat-time'>" + name + ", " + getCurrentTime() + "</div><div class='ol__chat-text'>" + message + "</div></div></div>";
            $('.ol__chart-text-panel').append(html);
        } else {
            addChatByPartnerTrans(message, name, avatar_url);
        }
    }
    
    function addChatByMeTrans(message){
        $.ajax({
            url: "{{route('ajax.translate')}}",
            type: 'post',
            dataType: 'json',
            data: {
                text: message
            },
            success: function (ret) {
                var html = "<div class='ol__chat-self'><div class='ol__chat-content'><div class='ol__chat-time'>" + getCurrentTime() + "</div><div class='ol__chat-text'>" + message +  "<br>【訳文】" + ret.result +"</div></div></div>";
                $('.ol__chart-text-panel').append(html);
            },
            fail: function (err) {
                
            },
            error: function (err) {
            }
        });
    }

    function addChatByPartnerTrans(message, name, avatar_url){
        $.ajax({
            url: "{{route('ajax.translate')}}",
            type: 'post',
            dataType: 'json',
            data: {
                text: message
            },
            success: function (ret) {
                var html = "<div class='ol__chat-partner'><div class='ol__avatar'><img class='ol__avatar-size-40' src='" + avatar_url + "'></div><div class='ol__chat-content'><div class='ol__chat-time'>" + name + ", " + getCurrentTime() + "</div><div class='ol__chat-text'>" + message + "<br>【訳文】" + ret.result +"</div></div></div>";
                $('.ol__chart-text-panel').append(html);
            },
            fail: function (err) {
                
            },
            error: function (err) {
            }
        });
    }

    function openFullscreen(elem) {
        if (elem.requestFullscreen) {
            elem.requestFullscreen();
        } else if (elem.mozRequestFullScreen) {
            elem.mozRequestFullScreen();
        } else if (elem.webkitRequestFullscreen) {
            elem.webkitRequestFullscreen();
        } else if (elem.msRequestFullscreen) {
            elem.msRequestFullscreen();
        }
    }

    $(".js-btn-recognize").on("click", function(e) {
        if (IS_HOST == 1) {
            $(this).toggleClass('ol__btn-pink');
            if ($(this).hasClass('ol__btn-pink')) {
                is_allow_recog = 1;
                vr_function_start();
            } else {
                is_allow_recog = 0;
                vr_function_stop();
            }
        }
    });

    $(".js-btn-trans").on("click", function(e) {
            $(this).toggleClass('ol__btn-pink');
            if ($(this).hasClass('ol__btn-pink')) {
                is_allow_trans = 1;
            } else {
                is_allow_trans = 0;
            }
    });

    $(".js-btn-screenshare").on("click", function(e) {
        $(".js-btn-screenshare").toggleClass('ol__btn-pink');
        if ($(".js-btn-screenshare").hasClass('ol__btn-pink')) {
            startScreenSharing();
        }
    });

    $(".js-btn-videomeeting").on("click", function(e) {
        if (!$(".js-btn-videomeeting").hasClass('ol__btn-pink')) {
            startVideoMeetingSora();
            $(".js-btn-videomeeting").toggleClass('ol__btn-pink');
        }
    });

    async function startVideoMeetingSora() {
        const host_video = document.getElementById('js-host-stream');
        const student_videos = document.getElementById('js-student-streams');

        const constraints = {
            audio: true,
            video: {
                width: 640, height: 480
            }
        };
        
        sora_sendrecv.on('track', function (event) {
            const stream = event.streams[0];
            if (!stream) return;
            console.log("-------------------------------");
            console.log(event);
            console.log(sora_sendrecv);
            console.log(stream);
            console.log("BBBBBBBBBBBBBBBBBBBBBBBBBB");
            const remoteVideoId = 'sendrecv1-remotevideo-' + stream.id;
            const remoteVideos = document.querySelector('#sendrecv1-remote-videos');
            if (!remoteVideos.querySelector('#' + remoteVideoId)) {
                const remoteVideo = document.createElement('video');
                remoteVideo.id = remoteVideoId;
                remoteVideo.style.border = '1px solid red';
                remoteVideo.autoplay = true;
                remoteVideo.playsinline = true;
                remoteVideo.controls = true;
                remoteVideo.width = '160';
                remoteVideo.height = '120';
                remoteVideo.srcObject = stream;
                remoteVideos.appendChild(remoteVideo);
            }
        });

        const localStream = await navigator.mediaDevices
        .getUserMedia(constraints)
        .then(mediaStream => {
            sora_sendrecv.connect(mediaStream)
            .then(stream => {
                console.log("-------------------------------");
                console.log(sora_sendrecv);
                console.log(stream);
                console.log("AAAAAAAAAAAAAAAAAAAAAAAAAAA");
                if (IS_HOST == 1) {
                    host_video.muted = true;
                    host_video.srcObject = stream;
                    host_video.playsInline = true;
                    host_video.play().catch(console.error);
                } else {
                    stream.peerId = 'self';
                    stream_list.push(stream);
                    $('#js-student-streams').trigger('add.owl.carousel', ['<video id="self"></video>']).trigger('refresh.owl.carousel');
                }
            });
        })
        .catch(console.error);


        /*sendrecv1.on('removetrack', function (event) {
            const remoteVideo = document.querySelector('#sendrecv1-remotevideo-' + event.target.id);
            if (remoteVideo) {
                document.querySelector('#sendrecv1-remote-videos').removeChild(remoteVideo);
            }
        });*/
    }

    async function startVideoMeetingSkyway() {

        const host_video = document.getElementById('js-host-stream');
        const student_videos = document.getElementById('js-student-streams');

        const getRoomModeByHash = () => ('mesh');

        /*window.addEventListener(
            'hashchange',
            () => (roomMode.textContent = getRoomModeByHash())
        );*/

        const localStream = await navigator.mediaDevices
            .getUserMedia({
                audio: true,
                video: true,
            })
            .catch(console.error);
        
        // Render local stream
        if (IS_HOST == 1) {
            host_video.muted = true;
            host_video.srcObject = localStream;
            host_video.playsInline = true;
            await host_video.play().catch(console.error);
        } else {
            localStream.peerId = 'self';
            stream_list.push(localStream);
            $('#js-student-streams').trigger('add.owl.carousel', ['<video id="self"></video>']).trigger('refresh.owl.carousel');
        }

        var peer;
        if (IS_HOST == 1) {
            peer = (window.peer = new Peer(VIDEO_HOST_ID, {
                key: window.__SKYWAY_KEY__,
                debug: 3,
            }));
        } else {
            peer = (window.peer = new Peer("{{VideoChatManager::generatePeerId()}}", {
                key: window.__SKYWAY_KEY__,
                debug: 3,
            }));
        }

        peer.on('open', id => {
            const room = peer.joinRoom(VIDEO_ROOM_ID, {
                mode: getRoomModeByHash(),
                stream: localStream,
                videoBandwidth: 50,
            });

            room.once('open', () => {
                ol_notify("接続に成功しました。");
            });

            room.on('peerJoin', peerId => {
                if (peerId == VIDEO_HOST_ID) {
                    ol_notify_from_partner(peerId + "さん", "接続しました。", "../images/avatar-teacher.png");
                } else {
                    ol_notify_from_partner(peerId + "さん", "接続しました。", "../images/avatar-teacher.png");
                }
            });

            room.on('stream', async stream => {
                stream_list.push(stream);
                if (IS_HOST == 1) {
                    $('#js-student-streams').trigger('add.owl.carousel', ['<video id="' + stream.peerId + '"></video>']).trigger('refresh.owl.carousel');
                } else {
                    if (stream.peerId == VIDEO_HOST_ID) {
                        var host_video = document.getElementById('js-host-stream');
                        host_video.srcObject = stream;
                        host_video.playsInline = true;
                        host_video.setAttribute('data-peer-id', stream.peerId);
                        host_video.play().catch(console.error);
                    } else {
                        $('#js-student-streams').trigger('add.owl.carousel', ['<video id="' + stream.peerId + '"></video>']).trigger('refresh.owl.carousel');
                    }
                }
            });

            room.on('peerLeave', peerId => {
                const student_video = student_videos.querySelector(
                    `[data-peer-id="${peerId}"]`
                );

                if (student_video == null)
                    return;

                if (student_video.srcObject == null)
                    return;

                student_video.srcObject.getTracks().forEach(track => track.stop());
                student_video.srcObject = null;

                var removed_index = -1;
                for (var index = 0; index < stream_list.length; index++) {
                    if (stream_list[index].peerId == peerId) {
                        removed_index = index;
                        break;
                    }
                }

                if (removed_index == -1)
                    return;

                $('#js-student-streams').trigger('remove.owl.carousel', removed_index).trigger('refresh.owl.carousel');
            });

            room.on('data', ({
                data,
                src
            }) => {
                if (src == VIDEO_HOST_ID) {
                    addChatByPartner(data, src, "../images/avatar-teacher.png");
                    ol_notify_from_partner(src + "さん", data, "../images/avatar-teacher.png");
                } else {
                    addChatByPartner(data, src, "../images/avatar-student.png");
                    ol_notify_from_partner(src + "さん", data, "../images/avatar-student.png");
                }
                $(".ol__chart-text-panel").scrollTop($(".ol__chart-text-panel").prop("scrollHeight"));

            });

            $(".js-btn-send-message").on("click", function() {
                room.send($("#txt_send_message").val());
                addChatByMe($("#txt_send_message").val());
                $("#txt_send_message").val("");
                $(".ol__chart-text-panel").scrollTop($(".ol__chart-text-panel").prop("scrollHeight"));
            });

            $("#txt_send_message").keypress(function(e) {
                if (e.keyCode == 13) {
                    $(".js-btn-send-message").click();
                }
            });

        });
        peer.on('error', error => {
            //if (error.type != "peer-unavailable") {
            //    console.log('error: type=' + error.type + ", message=" + error.message);
            //}
            ol_notify("通信にエラーが発生しました。", "danger");
        });
    }

    async function startScreenSharing() {
        const host_video = document.getElementById('js-host-screen');
        var localStream;

        const getRoomModeByHash = () => ('mesh');

        if (IS_HOST == 1) {
            localStream = await navigator.mediaDevices
                .getDisplayMedia({
                    video: true,
                })
                .catch(console.error);

            host_video.muted = true;
            host_video.srcObject = localStream;
            host_video.playsInline = true;
            await host_video.play().catch(console.error);
        }

        var peer;
        if (IS_HOST == 1) {
            peer = (window.peer = new Peer(SCREEN_HOST_ID, {
                key: window.__SKYWAY_KEY__,
                debug: 3,
            }));
        } else {
            peer = (window.peer = new Peer("{{VideoChatManager::generatePeerId()}}", {
                key: window.__SKYWAY_KEY__,
                debug: 3,
            }));
        }

        peer.on('open', id => {
            const room = peer.joinRoom(SCREEN_ROOM_ID, {
                mode: getRoomModeByHash(),
                stream: localStream,
            });

            room.once('open', () => {
                //ol_notify("接続に成功しました。");
            });

            room.on('peerJoin', peerId => {
                if (peerId == SCREEN_HOST_ID) {
                    //ol_notify_from_partner(peerId + "さん", "接続しました。", "../images/avatar-teacher.png");
                } else {
                    //ol_notify_from_partner(peerId + "さん", "接続しました。", "../images/avatar-teacher.png");
                }
            });

            room.on('stream', async stream => {
                if (IS_HOST != 1) {
                    if (stream.peerId == SCREEN_HOST_ID) {
                        var host_video = document.getElementById('js-host-screen');
                        host_video.srcObject = stream;
                        host_video.playsInline = true;
                        host_video.setAttribute('data-peer-id', stream.peerId);
                        host_video.play().catch(console.error);
                    }
                }
            });

            room.on('peerLeave', peerId => {
                if (peerId == SCREEN_HOST_ID) {
                    const host_video = document.getElementById('js-host-screen');
                    if (host_video == null)
                        return;

                    if (host_video.srcObject == null)
                        return;

                    host_video.srcObject.getTracks().forEach(track => track.stop());
                    host_video.srcObject = null;
                }
            });
        });
        peer.on('error', error => {
            //if (error.type != "peer-unavailable") {
            //    console.log('error: type=' + error.type + ", message=" + error.message);
            //}
            ol_notify("通信にエラーが発生しました。(ScreenShare)", "danger");
        });
    }

    var flag_speech = 0;
    function vr_function_start () {
        if (is_allow_recog == 0)
            return;

        window.SpeechRecognition = window.SpeechRecognition || webkitSpeechRecognition;
        var recognition = new webkitSpeechRecognition();
        recognition.lang = 'ja-JP';
        recognition.interimResults = true;
        recognition.continuous = true;

        recognition.onsoundstart = function() {
            console.log("認識中");
        };
        recognition.onnomatch = function() {
            console.log("もう一度試してください");
        };
        recognition.onerror = function() {
            console.log("エラー");
            if (flag_speech == 0)
                vr_function_start();
        };
        recognition.onsoundend = function() {
            console.log("停止中");
            vr_function_start();
        };

        recognition.onresult = function(event) {
            if (is_allow_recog == 0)
                return;
                
            var results = event.results;
            for (var i = event.resultIndex; i < results.length; i++) {
                if (results[i].isFinal) {
                    $("#txt_send_message").val(results[i][0].transcript);
                    $(".js-btn-send-message").click();
                    vr_function_start();
                } else {
                    $("#txt_send_message").val(results[i][0].transcript);
                    flag_speech = 1;
                }
            }
        }
        flag_speech = 0;
        console.log("start");
        recognition.start();
    }
    function vr_function_stop () {
        window.SpeechRecognition = window.SpeechRecognition || webkitSpeechRecognition;
        var recognition = new webkitSpeechRecognition();
        recognition.stop();
        console.log("----------------");
    }

    //startScreenSharing();
</script>
@endsection