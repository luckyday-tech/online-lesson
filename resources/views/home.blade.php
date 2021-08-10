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
            <div class="ol__host-screen">
                <video id="js-host-screen" autoplay="" playsinline="" controls=""></video>
                <div class="ol_host-screen-text">
                    <p id="js-host-screen-text"></p>
                </div>
            </div>
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
    $(document).ready(function() {
        var stream_list = [];
        var is_allow_recog = 0;
        var is_allow_trans = 0;

        const HOST_TYPE = "{{$user['type']}}";
        const V_CLIENT_ID = "v_user_{{$user['id']}}";
        const V_HOST_ID = "v_user_{{$room['host_id']}}";
        const V_ROOM_ID = "v_room_{{$room['id']}}";

        const S_CLIENT_ID = "s_user_{{$user['id']}}";
        const S_HOST_ID = "s_user_{{$room['host_id']}}";
        const S_ROOM_ID = "s_room_{{$room['id']}}";

        const sora_v = Sora.connection('wss://abacus-platform.com:8043/signaling', false);
        const options_v = {
            multistream: true,
            clientId: V_CLIENT_ID,
            video: {
                "codec_type": "VP9"
            }
        }

        const sora_s = Sora.connection('wss://abacus-platform.com:8043/signaling', false);
        const options_s = {
            multistream: true,
            clientId: S_CLIENT_ID,
            video: {
                "codec_type": "VP9"
            }
        }

        var sora_sendrecv_v = sora_v.sendrecv(V_ROOM_ID, null, options_v);
        var sora_sendrecv_s = null;
        if (HOST_TYPE == "{{HOST_TYPE_TEACHER}}") {
            sora_sendrecv_s = sora_s.sendonly(S_ROOM_ID, null, options_s);
        } else {
            sora_sendrecv_s = sora_s.recvonly(S_ROOM_ID, null, options_s);
        }

        $(".js-btn-videomeeting").on("click", function(e) {
            if (!$(".js-btn-videomeeting").hasClass('ol__btn-pink')) {
                startVideoMeetingSora();
                $(".js-btn-videomeeting").toggleClass('ol__btn-pink');
            }
        });

        $(".js-btn-screenshare").on("click", function(e) {
            if (!$(".js-btn-screenshare").hasClass('ol__btn-pink')) {
                startScreenSharingSora();
                $(".js-btn-screenshare").toggleClass('ol__btn-pink');
            }
        });

        $(".js-btn-recognize").on("click", function(e) {
            if (HOST_TYPE == "{{HOST_TYPE_TEACHER}}") {
                $(this).toggleClass('ol__btn-pink');
                if ($(this).hasClass('ol__btn-pink')) {
                    is_allow_recog = 1;
                    startListening();
                } else {
                    is_allow_recog = 0;
                    stopListening();
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


        async function startVideoMeetingSora() {
            const host_video = document.getElementById('js-host-stream');
            const student_videos = document.getElementById('js-student-streams');

            const constraints = {
                audio: true,
                video: {
                    width: 640, height:480
                }
            };

            const local_stream = await navigator.mediaDevices
            .getUserMedia({
                audio: true,
                video: true,
            })
            .catch((error)=>{
                basicAlert('カメラに接続できません。カメラ接続確認後ページをリフレッシュしてください。');
                check_local_camera = false;
            });

            const localStream = await navigator.mediaDevices
                .getUserMedia(constraints)
                .then(mediaStream => {
                    sora_sendrecv_v.connect(mediaStream)
                        .then(stream => {
                            if (HOST_TYPE == "{{HOST_TYPE_TEACHER}}") {
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

            sora_sendrecv_v.on('track', function(event) {
                const stream = event.streams[0];
                if (!stream) return;
                if (event.track.kind != "video") return;

                stream_list.push(stream);
            });

            sora_sendrecv_v.on('notify', function(event) {
                if (event.event_type == "connection.created") {
                    if (event.client_id == V_CLIENT_ID) {
                        if (event.metadata_list != undefined) {
                            for (index = 0; index < event.metadata_list.length; index++) {
                                set_peer_id(event.metadata_list[index].connection_id, event.metadata_list[index].client_id);
                            }
                        }
                    } else {
                        set_peer_id(event.connection_id, event.client_id);
                    }
                }
            });

            sora_sendrecv_v.on('removetrack', function(event) {
                if (event.track.kind == "video") {
                    remove_stream(event.currentTarget.id);
                }
            });

            function set_peer_id(connection_id, peer_id) {
                for (index = 0; index < stream_list.length; index++) {
                    if (stream_list[index].peerId == peer_id) {
                        stream_list.splice(index, 1);
                    }
                }
                for (index = 0; index < stream_list.length; index++) {
                    if (stream_list[index].id == connection_id) {
                        stream_list[index].peerId = peer_id;
                        if (peer_id == V_HOST_ID) {
                            var host_video = document.getElementById('js-host-stream');
                            host_video.srcObject = stream_list[index];
                            host_video.playsInline = true;
                            host_video.setAttribute('data-peer-id', peer_id);
                            host_video.play().catch(console.error);
                        } else {
                            $('#js-student-streams').trigger('add.owl.carousel', ['<video id="' + peer_id + '"></video>']).trigger('refresh.owl.carousel');
                        }
                        return true;
                    }
                }
                return false;
            }

            function remove_stream(connection_id) {
                for (index = 0; index < stream_list.length; index++) {
                    if (stream_list[index].id == connection_id) {
                        remove_student(stream_list[index].peerId);
                        stream_list.splice(index, 1);
                        return true;
                    }
                }

                return false;
            }

            function remove_student(peer_id) {
                const student_video = student_videos.querySelector(
                    `[data-peer-id="${peer_id}"]`
                );

                if (student_video == null)
                    return;

                if (student_video.srcObject == null)
                    return;

                student_video.srcObject.getTracks().forEach(track => track.stop());
                student_video.srcObject = null;

                var removed_index = -1;
                for (var index = 0; index < stream_list.length; index++) {
                    if (stream_list[index].peerId == peer_id) {
                        removed_index = index;
                        break;
                    }
                }

                if (removed_index == -1)
                    return;

                $('#js-student-streams').trigger('remove.owl.carousel', removed_index).trigger('refresh.owl.carousel');
            }
        }

        async function startScreenSharingSora() {
            const host_video = document.getElementById('js-host-screen');

            if (HOST_TYPE == "{{HOST_TYPE_TEACHER}}") {
                const localStream = await navigator.mediaDevices
                    .getDisplayMedia({
                        audio: false,
                        video: true,
                    })
                    .then(mediaStream => {
                        sora_sendrecv_s.connect(mediaStream)
                            .then(stream => {
                                host_video.muted = true;
                                host_video.srcObject = stream;
                                host_video.playsInline = true;
                                host_video.play().catch(console.error);

                                $(".ol__host-screen").addClass('active');
                            });
                    })
                    .catch(console.error);
            } else {
                sora_sendrecv_s.connect()
                    .catch(e => {
                        console.error(e);
                    });
                sora_sendrecv_s.on('track', function(event) {
                    //console.log(event);
                    const stream = event.streams[0];
                    if (!stream) return;
                    if (event.track.kind != "video") return;

                    stream_list.push(stream);

                    var host_video = document.getElementById('js-host-screen');
                    host_video.srcObject = stream;
                    host_video.playsInline = true;
                    host_video.play().catch(console.error);
                    $(".ol__host-screen").addClass('active');
                });

                sora_sendrecv_s.on('notify', function(event) {
                    //console.log(event);
                });
            }
        }

        var flag_speech = 0;

        function startListening() {
            if (is_allow_recog == 0)
                return;

            window.SpeechRecognition = window.SpeechRecognition || webkitSpeechRecognition;
            var recognition = new webkitSpeechRecognition();
            recognition.lang = 'ja-JP';
            recognition.interimResults = true;
            recognition.continuous = true;

            recognition.onsoundstart = function() {
                //console.log("認識中");
            };
            recognition.onnomatch = function() {
                //console.log("もう一度試してください");
            };
            recognition.onerror = function() {
                //console.log("エラー");
                if (flag_speech == 0)
                    startListening();
            };
            recognition.onsoundend = function() {
                //console.log("停止中");
                startListening();
            };

            recognition.onresult = function(event) {
                if (is_allow_recog == 0)
                    return;

                var results = event.results;
                for (var i = event.resultIndex; i < results.length; i++) {
                    if (results[i].isFinal) {
                        if (is_allow_trans == 1) {
                            translate(results[i][0].transcript)
                        } else {
                            $("#js-host-screen-text").html(results[i][0].transcript);
                        }
                        startListening();
                    } else {
                        if (is_allow_trans == 1) {
                            $("#js-host-screen-text").html(results[i][0].transcript + "<br>【訳文】 ...");
                        } else {
                            $("#js-host-screen-text").html(results[i][0].transcript);
                        }
                        flag_speech = 1;
                    }
                }
            }
            flag_speech = 0;
            //console.log("start");
            recognition.start();
        }

        function stopListening() {
            window.SpeechRecognition = window.SpeechRecognition || webkitSpeechRecognition;
            var recognition = new webkitSpeechRecognition();
            recognition.stop();
            //console.log("----------------");
        }

        function translate(message) {
            $.ajax({
                url: "{{route('ajax.translate')}}",
                type: 'post',
                dataType: 'json',
                data: {
                    text: message
                },
                success: function(ret) {
                    $("#js-host-screen-text").html(message + "<br>【訳文】" + ret.result);
                },
                fail: function(err) {},
                error: function(err) {}
            });
        }

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
    })
</script>
@endsection