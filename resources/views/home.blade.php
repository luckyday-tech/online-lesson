@extends('layouts.app')

@section('content')
<main class="ol__main">
    <div class="ol__side">
        <video id="js-host-stream" class="ol__host_video"></video>
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
            <img src="../images/board.png" />
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
    const IS_HOST = "{{$is_host}}";
    const HOST_ID = "{{$host_id}}";
    const ROOM_ID = "{{$room_id}}";

    $('.owl-carousel').owlCarousel({
        loop: false,
        margin: 11,
        items: 4,
        onRefreshed: onCarouselRefreshed,
    })

    function onCarouselRefreshed(event) {
        for (var index = 0; index < stream_list.length; index++){
            var new_video = document.getElementById(stream_list[index].peerId);

            if (new_video==null) 
                continue;

            if (new_video.classList.contains('ol__student-video') )
                continue;

            new_video.srcObject = stream_list[index];
            new_video.playsInline = true;
            new_video.setAttribute('data-peer-id', stream_list[index].peerId);
            new_video.setAttribute('class', 'ol__student-video');

            new_video.play().catch(console.error);
        }
    }

    function getCurrentTime(){
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

        var html = "<div class='ol__chat-self'><div class='ol__chat-content'><div class='ol__chat-time'>" + getCurrentTime() + "</div><div class='ol__chat-text'>" + message + "</div></div></div>";
        $('.ol__chart-text-panel').append(html);
    }

    function addChatByPartner(message, name, avatar_url){

        var html = "<div class='ol__chat-partner'><div class='ol__avatar'><img class='ol__avatar-size-40' src='"+avatar_url+"'></div><div class='ol__chat-content'><div class='ol__chat-time'>" + name + ", " + getCurrentTime() + "</div><div class='ol__chat-text'>" + message + "</div></div></div>";
        $('.ol__chart-text-panel').append(html);
    }

    (async function main() {

        const host_video = document.getElementById('js-host-stream');
        const student_videos = document.getElementById('js-student-streams');

        const getRoomModeByHash = () => ('mesh');

        window.addEventListener(
            'hashchange',
            () => (roomMode.textContent = getRoomModeByHash())
        );

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
            peer = (window.peer = new Peer(HOST_ID, {
                key: window.__SKYWAY_KEY__,
                debug: 3,
            }));
        } else {
            peer = (window.peer = new Peer({
                key: window.__SKYWAY_KEY__,
                debug: 3,
            }));
        }

        peer.on('open', id => {
            const room = peer.joinRoom(ROOM_ID, {
                mode: getRoomModeByHash(),
                stream: localStream,
                videoBandwidth: 50,
            });

            room.once('open', () => {
                console.log('=== You joined ===\n');
            });

            room.on('peerJoin', peerId => {
                console.log(`=== ${peerId} joined ===\n`);
            });

            room.on('stream', async stream => {
                stream_list.push(stream);
                if (IS_HOST == 1) {
                    $('#js-student-streams').trigger('add.owl.carousel', ['<video id="' + stream.peerId + '"></video>']).trigger('refresh.owl.carousel');
                } else {
                    if (stream.peerId == HOST_ID) {
                        var host_video = document.getElementById('js-host-stream');
                        host_video.srcObject = stream;
                        host_video.playsInline = true;
                        host_video.setAttribute('data-peer-id', stream.peerId);
                        host_video.play().catch(console.error);
                    }
                }
            });

            room.on('peerLeave', peerId => {
                const student_video = student_videos.querySelector(
                    `[data-peer-id="${peerId}"]`
                );
                student_video.srcObject.getTracks().forEach(track => track.stop());
                student_video.srcObject = null;

                var removed_index = -1;
                for (var index = 0; index < stream_list.length; index++){
                    if (stream_list[index].peerId == peerId) {
                        removed_index = index;
                        break;
                    }
                }

                if (removed_index == -1)
                    return;
                
                $('#js-student-streams').trigger('remove.owl.carousel', removed_index).trigger('refresh.owl.carousel');
            });

            room.on('data', ({ data, src }) => {
                if (src == HOST_ID) {
                    addChatByPartner(data, src, "../images/avatar-teacher.png");
                } else {
                    addChatByPartner(data, src, "../images/avatar-student.png");
                }
                $(".ol__chart-text-panel").scrollTop($(".ol__chart-text-panel").prop("scrollHeight"));

            });

            $(".js-btn-send-message").on("click", function(){
                room.send($("#txt_send_message").val());
                addChatByMe($("#txt_send_message").val());   
                $("#txt_send_message").val("");  
                $(".ol__chart-text-panel").scrollTop($(".ol__chart-text-panel").prop("scrollHeight"));
            });


            $("#txt_send_message").keypress(function(e) { 
                if (e.keyCode == 13){
                    $(".js-btn-send-message").click()
                }    
            });

        });
    })();


</script>
@endsection