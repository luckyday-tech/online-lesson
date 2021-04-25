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
            <div class="ol__logo"><img src="../images/logo.svg" /></div>
            <div class="ol__avatar">
                <img class="ol__avatar-size-49" src="../images/avatar-teacher.png" />
            </div> 
            <div class="ol__avatar-title">化粧 美咲<br>リモートメイク術</div>
            <div class="ol__actions">
                <div class="ol__btn ol__btn-action ol__btn-black"><i class="fas fa-microphone-slash"></i></div>
                <div class="ol__btn ol__btn-action ol__btn-black"><i class="fas fa-video-slash"></i></div>
                <div class="ol__btn ol__btn-action ol__btn-pink"><i class="fas fa-phone"></i></div>
                <div class="ol__btn ol__btn-action ol__btn-black"><i class="fas fa-comment"></i></div>
                <div class="ol__btn ol__btn-action ol__btn-blue"><i class="fas fa-plus"></i></div>
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

@yield('page_js')

</html>