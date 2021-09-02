<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="utf-8">
    <meta http-equiv="Content-Script-Type" content="text/javascript" />
	<meta http-equiv="Content-Style-Type" content="text/css" />
    <meta name="description" content="">
    <meta name="viewport" content="width=device-width,initial-scale=1.0,minimum-scale=1.0" />

    <link rel="icon" href="{{ asset('assets/image/favicon.ico') }}" />
    <link rel="apple-touch-icon" sizes="180x180" href="{{ asset('assets/image/apple-touch-icon.png') }}" />

    <link rel="stylesheet" href="{{asset('video-assets/css/app.css')}}">
    <style>
        .swal2-container {
            z-index: 2147483699 !important;
        }
    </style>
</head>
<body>
    <div id="app" class="{{g_isMobile()?'ol__sp':'ol__pc'}}">
        @if(!g_isMobile())
            <header class="ol__header">
                <div class="ol__logo"><img src="{{asset('video-assets/images/ap_logo_cl.png')}}" /></div>
                <div class="ol__actions">
                    <div class="ol__group">
                        <a class="ol__btn ol__btn-action"><span class="ol__btn-mic" id="btn_mic"></span></a>
                        <a class="ol__btn ol__btn-action"><span class="ol__btn-camera" id="btn_camera"></span></a>
                        <a class="ol__btn ol__btn-action"><span class="ol__btn-translate" id="btn_translate"> </span></a>
                        <a class="ol__btn ol__btn-action"><span class="ol__btn-speech" id="btn_speech"></span></a>
                        <a class="ol__btn ol__btn-action"><span class="ol__btn-chat" id="btn_chat"></span></a>
                        @if ($hostType == HOST_TYPE_TEACHER)
                        <a class="ol__btn ol__btn-action"><span class="ol__btn-screen" id="btn_screen"></span></a>
                        <!-- <a class="ol__btn ol__btn-action"><span class="ol__btn-camera" id="btn_record"></span></a> -->
                        @endif
                    </div>
                    <a class="ol__btn ol__btn-action"><span class="ol__btn-shutdown active" id="btn_shutdown"></span></a>

                    <div class="ol__avatar-title">
                        @if ($hostType == HOST_TYPE_TEACHER)
                        そろばん講師
                        @else
                            @if ($studentType == STUDENT_TYPE_SUBSCRIBE)
                            会員
                            @elseif ($studentType == STUDENT_TYPE_TOUR)
                            見学
                            @elseif ($studentType == STUDENT_TYPE_DEMO)
                            体験
                            @endif
                        @endif
                        :&nbsp;{{$selfName}}
                    </div>
                    <div class="ol__avatar">
                        <img class="ol__avatar-size-49" src="{{asset('video-assets/images/avatar-default.png')}}" />
                    </div> 
                </div>
            </header>
        @endif

        @if(g_isMobile())
            @yield('content_sp')
        @else
            @yield('content_pc')
        @endif

        <footer class="ol__footer">
            @if(g_isMobile())
                <div class="ol__profile">
                    <div class="ol__avatar">
                        <img class="ol__avatar-size-49" src="{{asset('video-assets/images/avatar-default.png')}}" />
                    </div>
                    <div class="ol__avatar-title">
                        アバカス スタジオ<br>
                        @if ($hostType == HOST_TYPE_TEACHER)
                        講師
                        @else
                            @if ($studentType == STUDENT_TYPE_SUBSCRIBE)
                            会員
                            @elseif ($studentType == STUDENT_TYPE_TOUR)
                            見学
                            @elseif ($studentType == STUDENT_TYPE_DEMO)
                            体験
                            @endif
                        @endif
                        :&nbsp;{{$selfName}}
                    </div>
                </div>
                <div class="ol__actions">
                    <div class="ol__group">
                        <a class="ol__btn ol__btn-action">
                            <span class="ol__btn-mic" id="btn_mic"></span><br>
                            <span class="ol__btn-title">マイク</span>
                        </a>
                        <a class="ol__btn ol__btn-action">
                            <span class="ol__btn-camera" id="btn_camera"></span><br>
                            <span class="ol__btn-title">ビデオ</span>
                        </a>
                        <a class="ol__btn ol__btn-action d-none">
                            <span class="ol__btn-translate active" id="btn_translate"></span><br>
                            <span class="ol__btn-title"></span>
                        </a>
                        <a class="ol__btn ol__btn-action d-none">
                            <span class="ol__btn-speech active" id="btn_speech"></span><br>
                            <span class="ol__btn-title"></span>
                        </a>
                        <a class="ol__btn ol__btn-action">
                            <span class="ol__btn-chat" id="btn_chat"></span><br>
                            <span class="ol__btn-title">チャット</span>
                        </a>
                        <a class="ol__btn ol__btn-action">
                            <span class="ol__btn-screen" id="btn_screen"></span><br>
                            <span class="ol__btn-title">共有画面</span>
                        </a>
                        <a class="ol__btn ol__btn-action">
                            <span class="ol__btn-shutdown active" id="btn_shutdown"></span><br>
                            <span class="ol__btn-title">終了</span>
                        </a>
                    </div>
                </div>
            @endif
        </footer>
    </div>
    
</body>
<script>
    window.__SKYWAY_KEY__ = "{{config('app.skyway_app_key')}}";
</script>
<script src="{{asset('video-assets/js/app.js')}}"></script>
<script src="{{asset('video-assets/js/media.js')}}"></script>
<script src="{{asset('video-assets/libs/sora/sora.js')}}"></script>

<script>
    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': '{{ csrf_token() }}'
        }
    });
</script>

@yield('page_js')

</html>