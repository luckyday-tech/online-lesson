<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="utf-8">
    <meta http-equiv="Content-Script-Type" content="text/javascript" />
	<meta http-equiv="Content-Style-Type" content="text/css" />
    <meta name="description" content="">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <link rel="stylesheet" href="{{asset('css/app.css')}}">
    <title>{{ config('app.name', 'Online Lesson') }}</title>

</head>
<body>
    <div id="app" class="ol__">
        <header class="ol__header">
            <div class="ol__logo"><img src="{{asset('images/logo.svg')}}" /></div>
            <div class="ol__avatar">
                <img class="ol__avatar-size-49" src="{{asset('images/avatar-teacher.png')}}" />
            </div> 
            <div class="ol__avatar-title">化粧 美咲<br>リモートメイク術</div>
            <div class="ol__actions">
                <a class="ol__btn ol__btn-action ol__btn-black ol__btn-disabled"><i class="fas fa-microphone-slash"></i></a>
                <a class="ol__btn ol__btn-action ol__btn-black ol__btn-disabled"><i class="fas fa-video-slash"></i></a>
                <a class="ol__btn ol__btn-action ol__btn-black ol__btn-disabled"><i class="fas fa-phone"></i></a>
                <a class="ol__btn ol__btn-action ol__btn-black ol__btn-disabled"><i class="fas fa-comment"></i></a>
                <a class="ol__btn ol__btn-action ol__btn-black js-btn-trans"><i class="fas fa-language"></i></a>
                <a class="ol__btn ol__btn-action ol__btn-black js-btn-recognize"><i class="fas fa-random"></i></a>
                <a class="ol__btn ol__btn-action ol__btn-black ol__btn-disabled js-btn-screenshare"><i class="fas fa-chalkboard"></i></a>
                <a class="ol__btn ol__btn-action ol__btn-blue" href="{{route('home', ['room_id'=> $room_id,'host_id'=> $host_id,'is_host'=> 0,])}}" target="_blank"><i class="fas fa-plus"></i></a>
            </div>
        </header>
    
        @yield('content')
    
        <footer>
        </footer>
    </div>
    
</body>
<script>
    window.__SKYWAY_KEY__ = "{{config('app.skyway_app_key')}}";
</script>
<script src="{{asset('js/app.js')}}"></script>
<script>
    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': '{{ csrf_token() }}'
        }
    });
</script>

@yield('page_js')

</html>