@extends('layouts.app')
<input type='hidden' id="txt_speech" />

@section('content_pc')
<main class="ol__main">
    <div class="ol__side">
        <video id="js-host-stream"  class="ol__host-video"></video>
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
                <div class="ol__btn ol__btn-action">
                    <span class="ol__btn-send active js-btn-send-message"></span>
                </div>
            </div>
        </div>
    </div>
    <div class="ol__body">
        <div class="ol__board">
            <div class="ol__host-screen ">
                <video id="js-host-screen" ></video>
                <div class="ol_host-screen-text"><p id="js-host-screen-text"></p></div>
            </div>
        </div>
        <div class="ol__student-list">
            <div id="js-student-streams" class="owl-carousel owl-theme">
                <!--
                <div class="ol__student-item">
                    <video class="ol__student-video"></video>
                    <div class="ol__student-title subscribe">abcd</div>
                </div>
                <div class="ol__student-item">
                    <video class="ol__student-video"></video>
                    <div class="ol__student-title tour">abcd</div>
                </div>
                <div class="ol__student-item">
                    <video class="ol__student-video"></video>
                    <div class="ol__student-title demo">abcd</div>
                </div>
                -->
            </div>
        </div>
    </div>
</main>
@endsection

@section('content_sp')
<style>
    body {
        background: #EBF4F7 0% 0% no-repeat padding-box;
        opacity: 1;
        font: "Noto Sans JP";
    }

    .ol__sp {
        padding: 0px;
    }

    .ol__sp .ol__main {
        overflow-y: auto;
        height: calc(100% - 155px); 
        height: -webkit-calc(100% - 155px);
    }

    #js-host-stream, #js-host-screen {
        object-fit: cover;
    }

    .ol__sp .ol__main .ol__host-video video {
        width: 100%;
        height: 100%;
        border-radius: 0px;
    }

    .ol__student-list:before {
        content: "生徒リスト";
        position:absolute;
        font-size:40px;
        font-weight:bold;
        opacity: 0.5;
        left:calc(50% - 90px);
        top: 30%;
    } 
    .ol__sp .ol__student-list {
        height: 186px;
        padding: 5px;
        background-color: #EBF4F7;
        border-radius: 0px;
        position: absolute;
        bottom: 155px;
        width: 100vw;
        width: -webkit-calc(100vw);
        display:none;
    }
    .ol__sp .ol__student-list .ol__student-item .ol__student-video {
        width: calc((100vw - 20px) / 2);
        border-radius: 10px;
    }
    .ol__chat {
        background: #EDF0F5 0% 0% no-repeat padding-box !important;
        opacity: 1;
    }
</style>
<main class="ol__main">
    <img src="{{asset('video-assets/images/ap_logo_cl.png')}}" style="position:fixed;top:15px;left: calc(50vw - 72px);" />
    <div class="ol__actions" style="position:absolute;right:15px; top:0px;z-index:100;">
        <div class="ol__group">
            <a class="ol__btn ol__btn-action mb-2 mt-3"><span class="ol__btn-translate" id="btn_translate"> </span></a>
            <a class="ol__btn ol__btn-action mb-2"><span class="ol__btn-speech" id="btn_speech"></span></a>
            <a class="ol__btn ol__btn-action"><span class="ol__btn-participant" id="btn_participant"></span></a>
        </div>
    </div>
    <div class="ol__host-video">
        <video id="js-host-stream" class="active" ></video>
        <video id="js-host-screen" ></video>
        <div class="ol_host-screen-text">
            <p id="js-host-screen-text">
                <b>授業タイトル:</b> {{$lessonTitle}}
            </p>
        </div> 
    </div>

    <div class="ol__chat">
        <div class="ol__chat-close" id="js-chat-close">
            <i class="fas fa-times"></i>
        </div>
        <div class="ol__chart-text-panel">
        </div>
        <div class="ol__chat-control-panel">
            <div class="ol__chat-text">
                <i class="far fa-smile"></i>
                <input type="text" id="txt_send_message" placeholder="メッセージを入力..." />
            </div>
            <div class="ol__btn ol__btn-action">
                <span class="ol__btn-send active js-btn-send-message"></span>
            </div>
        </div>
    </div>
</main>

<div class="ol__student-list">
    <div id="js-student-streams" class="owl-carousel owl-theme">
    </div>
</div>
@endsection

@section('page_js')
<script>
    var stream_list = [];
    var is_allow_recog = 0;
    var is_allow_trans = 0;
    var mediaObj = new MediaControl();
    var videoInputDeviceList = [];
    var audioInputDeviceList = [];
    var localStream = null;
    var sora_sendrecv_v = null;
    var sora_sendrecv_s = null;
    var selectedVideoConstraint = null;
    var selectedScreenConstraint = null;
    var selectedDeviceForScreen = null;
    var mediaRecorder = null;
    var blobsRecorded = [];

    const PERSON_LIST = <?php echo json_encode($personList) ?>;
    const HOST_TYPE_STUDENT = "{{HOST_TYPE_STUDENT}}";
    const HOST_TYPE_TEACHER = "{{HOST_TYPE_TEACHER}}";
    const DEFAULT_VIDEO_RATE_STUDENT = eval("{{DEFAULT_VIDEO_RATE_STUDENT}}");
    const DEFAULT_VIDEO_RATE_TEACHER = eval("{{DEFAULT_VIDEO_RATE_TEACHER}}");
    const STUDENT_TYPE_SUBSCRIBE = "{{STUDENT_TYPE_SUBSCRIBE}}";
    const STUDENT_TYPE_TOUR = "{{STUDENT_TYPE_TOUR}}";
    const STUDENT_TYPE_DEMO = "{{STUDENT_TYPE_DEMO}}";
    const HOST_TYPE = "{{$hostType}}";
    const STUDENT_TYPE = "{{$studentType}}";
    const V_CLIENT_ID = "v_user_{{$studentId}}";
    const V_HOST_ID = "v_user_{{$teacherId}}";
    const V_ROOM_ID = "v_room_{{$roomId}}";
    

    const S_CLIENT_ID = "s_user_{{$studentId}}";
    const S_HOST_ID = "s_user_{{$teacherId}}";
    const S_ROOM_ID = "s_room_{{$roomId}}";

    function connectSoraForVideoMeeting() {
        var sora_v = Sora.connection('wss://abacus-platform.com:8043/signaling', false);
        const options_v = {
            multistream: true,
            clientId: HOST_TYPE==HOST_TYPE_TEACHER?V_HOST_ID:V_CLIENT_ID,
            videoCodecType: "VP8",
            videoBitRate: HOST_TYPE==HOST_TYPE_TEACHER?DEFAULT_VIDEO_RATE_TEACHER:DEFAULT_VIDEO_RATE_STUDENT,
        }
        sora_sendrecv_v = sora_v.sendrecv(V_ROOM_ID, null, options_v);

        sora_sendrecv_v.on("disconnect", (event) => {
            if (event.type = "abend") {
                if ($('.ol__btn-camera').hasClass('active')) {
                    setTimeout(startVideoMeetingSora, 2000);
                }
            }
        });
    }

    function connectSoraForScreenSharing() {
        var sora_s = Sora.connection('wss://abacus-platform.com:8043/signaling', false);
        const options_s = {
        multistream: true,
        clientId: HOST_TYPE==HOST_TYPE_TEACHER?V_HOST_ID:V_CLIENT_ID,
        videoCodecType: "VP8",
        }

        sora_sendrecv_s = null;
        if (HOST_TYPE == HOST_TYPE_TEACHER) {
            sora_sendrecv_s = sora_s.sendonly(S_ROOM_ID, null, options_s);
        } else {
            sora_sendrecv_s = sora_s.recvonly(S_ROOM_ID, null, options_s);
        }

        sora_sendrecv_s.on("disconnect", (event) => {
            if (event.type = "abend") {
                if ($('.ol__btn-screen').hasClass('active') && HOST_TYPE == HOST_TYPE_TEACHER || HOST_TYPE == HOST_TYPE_STUDENT) {
                    setTimeout(startScreenSharingSora, 2000);
                }
            }
        });
    }
     
    async function startVideoMeetingSora() {
        const host_video = document.getElementById('js-host-stream');
        const student_videos = document.getElementById('js-student-streams');
        
        remove_all_stream();

        var constraints = {
            audio: true,
            video: true,
        };

        if (selectedVideoConstraint == null) {
            if (videoInputDeviceList.length >= 2) {
                var options = {};
                videoInputDeviceList.forEach(function(device){
                    options[device.deviceId] = device.label;
                });

                const {value: selectedDevice} = await Swal.fire({
                    title: 'カメラを選択してください。',
                    input: 'select',
                    inputOptions: options,
                });

                constraints.video = {deviceId:{exact: selectedDevice}};
            }// else {
                //console.log(videoInputDeviceList);
                //constraints.video = {deviceId:{exact: videoInputDeviceList[0].deviceId}};
            //}
            selectedVideoConstraint = constraints;
        } else {
            constraints = selectedVideoConstraint;
        }
        
        $('#js-host-stream').LoadingOverlay('show');

        await navigator.mediaDevices
        .getUserMedia(constraints)
        .then(mediaStream => {
            window.localStream = mediaStream;
            sora_sendrecv_v.connect(mediaStream)
            .then(stream => {
                //console.log(stream);
                if (HOST_TYPE == HOST_TYPE_TEACHER) {
                    host_video.muted = true;
                    host_video.srcObject = stream;
                    host_video.playsInline = true;
                    host_video.play().catch(console.error);
                } else {
                    stream.peerId = 'self';
                    stream_list.push(stream);
                    $('#js-student-streams').trigger('add.owl.carousel', ['<div class="ol__student-item"><video id="self"></video><div class="ol__student-title subscribe">{{$selfName}}</div></div>', 0]).trigger('refresh.owl.carousel');
                }
                $('#btn_mic').addClass('active');
                $('#btn_camera').addClass('active');
                $('#js-host-stream').LoadingOverlay('hide');
            });
        })
        .catch(console.error);

        sora_sendrecv_v.on('track', function (event) {
            const stream = event.streams[0];
            if (!stream) return;
            if (event.track.kind != "video") return;

            stream_list.push(stream);
        });

        sora_sendrecv_v.on('notify', function (event) {
            if (event.event_type == "connection.created") {
                if ((event.client_id == (HOST_TYPE==HOST_TYPE_TEACHER?V_HOST_ID:V_CLIENT_ID))) {
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

        sora_sendrecv_v.on('removetrack', function (event) {
            if (event.track.kind == "video") {
                remove_stream(event.currentTarget.id);
            }
        });

        function set_peer_id(connection_id, peer_id){
            for (index = 0; index < stream_list.length; index ++){
                if (stream_list[index].peerId == peer_id) {
                    stream_list.splice(index, 1);
                }
            }
            for (index = 0; index < stream_list.length; index ++){
                if(stream_list[index].id == connection_id) {
                    stream_list[index].peerId = peer_id;
                    if (peer_id == V_HOST_ID) {
                        var host_video = document.getElementById('js-host-stream');
                        host_video.srcObject = stream_list[index];
                        host_video.playsInline = true;
                        host_video.setAttribute('data-peer-id', peer_id);
                        host_video.play().catch(console.error);
                    } else {
                        
                        $('#js-student-streams').trigger('add.owl.carousel', ['<div class="ol__student-item"><video id="' + peer_id + '"></video><div class="ol__student-title '+getStudentType(peer_id, PERSON_LIST)+'">' + getName(peer_id, PERSON_LIST) + '</div></div>']).trigger('refresh.owl.carousel');
                    }
                    return true;
                }
            }
            return false;
        }

        function remove_stream(connection_id) {
            for (index = 0; index < stream_list.length; index ++){
                if (stream_list[index].id  == connection_id) {
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

        function remove_all_stream(){
            for (index = 0; index < stream_list.length; index ++){
                console.log(stream_list[index]);
                remove_student(stream_list[index].peerId);
            }
            stream_list = [];
        }
    }

    async function startScreenSharingSora() {
        const host_video = document.getElementById('js-host-screen');

        if (HOST_TYPE == HOST_TYPE_TEACHER) {
            var isCamera;
            var isScreen;
            if (selectedDeviceForScreen == null) {
                const {isConfirmed:cameraSel,isDenied:screenSel} = await Swal.fire({
                    title: 'ボードに表示されるデバイスを選択してください。',
                    showDenyButton: true,
                    showCancelButton: true,
                    confirmButtonText: 'カメラ',
                    denyButtonText: '画面共有',
                });
                isCamera = cameraSel;
                isScreen = screenSel;

                if (isCamera) {
                    selectedDeviceForScreen = "camera";
                } else if (isScreen) {
                    selectedDeviceForScreen = "screen";
                }
            } else {
                if (selectedDeviceForScreen == "camera"){
                    isCamera = true;
                } else {
                    isScreen = true;
                }
            } 


            if (isCamera) {
                var constraints = {
                    audio: false,
                    video: true,
                };
                if (selectedScreenConstraint == null) {
                    if (videoInputDeviceList.length >= 2) {
                        var options = {};
                        videoInputDeviceList.forEach(function(device){
                            options[device.deviceId] = device.label;
                        });

                        const {value: selectedDevice} = await Swal.fire({
                            title: 'カメラを選択してください。',
                            input: 'select',
                            inputOptions: options,
                        });
                        constraints.video = {deviceId:{exact: selectedDevice}};
                    }
                    selectedScreenConstraint = constraints;
                } else {
                    constraints = selectedScreenConstraint;
                }

                const localStream = await navigator.mediaDevices
                .getUserMedia(constraints)
                .then(mediaStream => {
                    sora_sendrecv_s.connect(mediaStream)
                    .then(stream => {
                        host_video.muted = true;
                        host_video.srcObject = stream;
                        host_video.playsInline = true;
                        host_video.play().catch(console.error);

                        $('#js-host-screen').LoadingOverlay('hide');
                        $('#btn_screen').addClass('active');
                    });
                })
                .catch(console.error);
            } else if(isScreen) {
                $('#js-host-screen').LoadingOverlay('show');
                const localStream = await navigator.mediaDevices
                .getDisplayMedia({
                    video: true,
                })
                .then(mediaStream => {
                    sora_sendrecv_s.connect(mediaStream)
                    .then(stream => {
                        host_video.muted = true;
                        host_video.srcObject = stream;
                        host_video.playsInline = true;
                        host_video.play().catch(console.error);

                        $('#js-host-screen').LoadingOverlay('hide');
                        $('#btn_screen').addClass('active');
                    });
                })
                .catch(console.error);
            }
        } else {
            $('#js-host-screen').LoadingOverlay('show');

            sora_sendrecv_s.connect()
            .catch(e => {
                console.error(e);
            });
            sora_sendrecv_s.on('track', function (event) {
                //console.log(event);
                const stream = event.streams[0];
                if (!stream) return;
                if (event.track.kind != "video") return;

                stream_list.push(stream);

                var host_video = document.getElementById('js-host-screen');
                host_video.srcObject = stream;
                host_video.playsInline = true;
                host_video.play().catch(console.error);

                $('#js-host-screen').LoadingOverlay('hide');
                $('#btn_screen').addClass('active');
            });

            sora_sendrecv_s.on('notify', function (event) {
                //console.log(event);
            });
        }
    }

    async function startMessagingSkyway() {
        $('.ol__chat').LoadingOverlay('show');

        var dataPeer = (window.peer = new Peer(HOST_TYPE==HOST_TYPE_TEACHER?V_HOST_ID:V_CLIENT_ID, {
            key: window.__SKYWAY_KEY__,
            debug: 0,
        }));
        dataPeer.on('open', id => {
            const room = dataPeer.joinRoom(V_ROOM_ID, {
                mode: 'mesh'
            });

            room.once('open', async () => {
                $('.ol__chat').LoadingOverlay('hide');
                $('#btn_chat').addClass('active');
            });

            room.on('peerJoin', peerId=>{
                //console.log('joined: peerId=' + peerId);
            })

            room.on('data', ({data, src}) => {
                if (isJsonString(data)) {
                    jsonData = JSON.parse(data);
                    if (jsonData.message != "" && jsonData.message != undefined){
                        if (jsonData.translate != "" && $('.ol__btn-translate').hasClass('active')) {
                            $("#js-host-screen-text").html(jsonData.message + "<br>" + jsonData.translate);
                        } else {
                            $("#js-host-screen-text").html(jsonData.message );
                        }
                    }
                } else {
                    var userName = getName(src, PERSON_LIST);
                    var nameSufix = "";

                    if (src == V_HOST_ID) {
                        nameSufix = " 先生";
                    } else {
                        nameSufix = "さん";
                    }

                    addChatByPartner(data, userName + nameSufix, "../video-assets/images/avatar-default.png");
                    @if(!g_isMobile())
                    ol_notify_from_partner(userName + nameSufix, data, "../video-assets/images/avatar-default.png");
                    @endif
                    $(".ol__chart-text-panel").scrollTop($(".ol__chart-text-panel").prop("scrollHeight"));
                }
            });

            room.once('close', () => {
                //console.log('room has been closed');
            });

            $(".js-btn-send-message").on("click", function() {
                if (!$(".ol__btn-chat").hasClass("active")) {
                    Swal.fire("チャットサーバーには接続できません。 しばらくしてからもう一度実行してください。");
                }

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

            $("#txt_speech").on("click", function(e) {
                room.send($(this).val());
            });
        });

        dataPeer.on('error', error => {
            if (error.type != "peer-unavailable") {
                $('.ol__chat').LoadingOverlay('hide');
                $('#btn_chat').removeClass('active');
                setTimeout(startMessagingSkyway, 5000);
            }
        });
    }

    var flag_speech = 0;
    function startListening () {
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
                startListening();
        };
        recognition.onsoundend = function() {
            console.log("停止中");
            startListening();
        };

        recognition.onresult = function(event) {
            if (is_allow_recog == 0)
                return;
                
            var results = event.results;
            for (var i = event.resultIndex; i < results.length; i++) {
                if (results[i].isFinal) {
                    if (is_allow_trans == 1) {
                        translate(results[i][0].transcript);
                    } else {
                        $("#js-host-screen-text").html(results[i][0].transcript);
                        sendSpeech(results[i][0].transcript);
                    }
                    startListening();
                } else {
                    if (is_allow_trans == 1) {
                        $("#js-host-screen-text").html(results[i][0].transcript + "<br>【訳文】 ...");
                    } else {
                        $("#js-host-screen-text").html(results[i][0].transcript + "【認識中】...");
                    }
                    flag_speech = 1;
                }
            }
        }
        flag_speech = 0;
        //console.log("start");
        recognition.start();
    }
    function stopListening () {
        window.SpeechRecognition = window.SpeechRecognition || webkitSpeechRecognition;
        var recognition = new webkitSpeechRecognition();
        recognition.stop();
        //console.log("----------------");
    }

    function translate(message){
        $.ajax({
            url: "{{route('ajax.translate')}}",
            type: 'post',
            dataType: 'json',
            data: {
                text: message
            },
            success: function (ret) {
                $("#js-host-screen-text").html(message + "<br>【訳文】" + ret.result);
                sendSpeech(message, ret.result);
            },
            fail: function (err) {
            },
            error: function (err) {
            }
        });
    }

    function getName(id, personList) {
        var personType = id.split('z')[0];
        var personId = '';
        var studentType = '';
        var isTeacher = false;

        if (personType.startsWith('v_user_t_')) {
            isTeacher = true;
            personId = personType.substring(9);
        } else if (personType.startsWith('v_user_s_' + STUDENT_TYPE_SUBSCRIBE)) {
            studentType = STUDENT_TYPE_SUBSCRIBE;
            personId = personType.substring(11);
        } else if (personType.startsWith('v_user_s_' + STUDENT_TYPE_TOUR)) {
            studentType = STUDENT_TYPE_TOUR;
            personId = personType.substring(11);
        } else if (personType.startsWith('v_user_s_' + STUDENT_TYPE_DEMO)) {
            studentType = STUDENT_TYPE_DEMO;
            personId = personType.substring(11);
        }   
        //console.log(id + "," + personId);

        var retName = id;
        if (isTeacher) {
            retName = personList['teacher'].name;
        } else {
            for(index = 0; index < personList['student'].length; index++) {
                if (personList['student'][index].id == eval(personId) && personList['student'][index].type == studentType){
                    retName = personList['student'][index].name;
                    break;
                }
            }
        }

        if (retName.length > 8) {
            console.log(retName.length);
            retName = retName.substring(0, 7) + "...";
        }
        return retName;
    }

    function getStudentType(id, personList) {
        var personType = id.split('z')[0];
        var personId = '';
        var studentType = '';

        if (personType.startsWith('v_user_t_')) {
            return '';
        } else if (personType.startsWith('v_user_s_' + STUDENT_TYPE_SUBSCRIBE)) {
            return 'subscribe';
        } else if (personType.startsWith('v_user_s_' + STUDENT_TYPE_TOUR)) {
            return 'tour';
        } else if (personType.startsWith('v_user_s_' + STUDENT_TYPE_DEMO)) {
            return 'demo';
        }
        
        return '';
    }

    function sendSpeech(message, translate=''){
        $('#txt_speech').val(JSON.stringify({
            message: message,
            translate: translate,
        }));

        $("#txt_speech").click();
    }

    function addChatByMe(message) {
        var html = "<div class='ol__chat-self'><div class='ol__chat-content'><div class='ol__chat-time'>" + getCurrentTime() + "</div><div class='ol__chat-text'>" + message + "</div></div></div>";
        $('.ol__chart-text-panel').append(html);
    }

    function addChatByPartner(message, name, avatar_url) {
        var html = "<div class='ol__chat-partner'><div class='ol__avatar'><img class='ol__avatar-size-40' src='" + avatar_url + "'></div><div class='ol__chat-content'><div class='ol__chat-time'>" + name + ", " + getCurrentTime() + "</div><div class='ol__chat-text'>" + message + "</div></div></div>";
        $('.ol__chart-text-panel').append(html);
    }

    $('.owl-carousel').owlCarousel({
        loop: false,
        margin: 11,
        responsive:{
            0:{
                items: 2,
            },
            768:{
                items: 4,
            }
        },
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

//---------------------------------------------------------------
    $('#btn_mic').on('click', function(e){
        $(this).toggleClass('active');
        if ($(this).hasClass('active')) {
            document.getElementById(HOST_TYPE == HOST_TYPE_TEACHER ? 'js-host-stream':'self').srcObject.getAudioTracks().forEach(t=> t.enabled = true);
        } else {
            document.getElementById(HOST_TYPE == HOST_TYPE_TEACHER ? 'js-host-stream':'self').srcObject.getAudioTracks().forEach(t=> t.enabled = false);
        }
    });

    $('#btn_camera').on('click', function(e){
        $(this).toggleClass('active');
        if ($(this).hasClass('active')) {
            document.getElementById(HOST_TYPE == HOST_TYPE_TEACHER ? 'js-host-stream':'self').srcObject.getVideoTracks().forEach(t=> t.enabled = true);
        } else {
            document.getElementById(HOST_TYPE == HOST_TYPE_TEACHER ? 'js-host-stream':'self').srcObject.getVideoTracks().forEach(t=> t.enabled = false);
        }
    });

    $('#btn_translate').on('click', function(e){
        $(this).toggleClass('active');
        is_allow_trans = $(this).hasClass('active');
    });

    $('#btn_speech').on('click', function(e){
        $(this).toggleClass('active');

        is_allow_recog = $(this).hasClass('active');

        if ($(this).hasClass('active')) {
            $('.ol_host-screen-text').addClass('active');
            if (HOST_TYPE == HOST_TYPE_TEACHER) {
                startListening();
            }
        } else {
            $('.ol_host-screen-text').removeClass('active');
            if (HOST_TYPE == HOST_TYPE_TEACHER) {
                stopListening();
            }
        }
    });

    $('#btn_chat').on('click', function(e){
        @if(g_isMobile()) 
            $('.ol__chat').addClass('active');
        @endif
    });

    $('#js-chat-close').on('click', function(e){
        $('.ol__chat').removeClass('active');
    });

    $('#btn_screen').on('click', function(e){
        @if(!g_isMobile()) {
            $(this).toggleClass('active');
            if ($(this).hasClass('active')) {
                //document.getElementById('js-host-screen').srcObject.getVideoTracks().forEach(t=> t.enabled = true);
                connectSoraForScreenSharing();
                startScreenSharingSora();
            } else {
                sora_sendrecv_s.disconnect();
                selectedScreenConstraint = null;
                selectedDeviceForScreen = null;
                //document.getElementById('js-host-screen').srcObject.getVideoTracks().forEach(t=> t.enabled = false);
            }
        }
        @else
            $(this).toggleClass('screen');
            if ($(this).hasClass('screen')) {
                $('#js-host-stream').removeClass('active');
                $('#js-host-screen').addClass('active');
            } else {
                $('#js-host-screen').removeClass('active');
                $('#js-host-stream').addClass('active');
            }
        @endif
    });

    $('#btn_shutdown').on('click', function(e){
        Swal.fire({
            title: '授業を終了しますか？',
            showCancelButton: true,
        }).then((result) => {
            if (result.isConfirmed) {
                window.history.go(-1);
            }
        });
    });

    $('#btn_record').on('click', function(e){
        $(this).toggleClass('active');
        if ($(this).hasClass('active')) {
            mediaRecorder = new MediaRecorder(window.localStream, { mimeType: 'video/webm' });
            mediaRecorder.addEventListener('dataavailable', function(e) {
                blobsRecorded.push(e.data);
            });

            mediaRecorder.addEventListener('stop', function() {
                let url = URL.createObjectURL(new Blob(blobsRecorded, { type: 'video/webm' }));
                blobsRecorded = [];
                window.downloadFile(url, "recorded_file.webm");
            });

            mediaRecorder.start(1);
        } else {
            if (mediaRecorder != null) {
                mediaRecorder.stop(); 
            }
        }
    });

    $('#btn_participant').on('click', function(e){
        $(this).toggleClass('active');

        let is_show_particpant = $(this).hasClass('active');
        manageVisibleParticipant();
    });

    (async () => {
        videoInputDeviceList = await mediaObj.getConnectedDevicesBy('videoinput');
        audioInputDeviceList = await mediaObj.getConnectedDevicesBy('audioinput');

        if (eval("{{g_isMobile()}}") == 1 && HOST_TYPE == HOST_TYPE_TEACHER){
            Swal.fire('PCでのみご利用いただけます。').then((result)=>{window.history.go(-1)});
            return;
        }

        if (videoInputDeviceList.length == 0) {
            Swal.fire('カメラを準備してから再度接続してください。').then((result)=>{window.history.go(-1)});
        } else if (audioInputDeviceList.length == 0) {
            Swal.fire('マイクを準備してから再度接続してください。').then((result)=>{window.history.go(-1)});
        } else {
            const{isConfirmed} = await Swal.fire({
                title: '授業を始めますか？',
                showCancelButton: true,
            });

            if (!isConfirmed) {
                window.history.go(-1);
                return;
            }
            await startMessagingSkyway();
            await startVideoMeetingSora();
            await startScreenSharingSora();
        }
    })();

    connectSoraForVideoMeeting();
    connectSoraForScreenSharing();

    function manageVisibleParticipant(flag) {
        //$(".ol__student-list").toggleClass('active');
        if ( $(".ol__student-list").is( ":hidden" ) ) {
            $(".ol__student-list").slideDown("slow" );
        } else {
            $(".ol__student-list").slideUp( "slow" );
        }
    }
</script>
@endsection