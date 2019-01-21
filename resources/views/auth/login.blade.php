<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1">
    <link rel="stylesheet" href="{{asset('css/app.css')}}">
    <title>{{ config('app.name', 'Edmark Backend') }}</title>
    <link rel="icon" href="{{asset('img/favicon.png')}}">
</head>
<body class="cm-login">

<div class="text-center" style="padding:20px 0 30px 0;background:#fff;border-bottom:1px solid #ddd">
    <img src="{{ asset('images/lg_edmark.png') }}" width="300">
</div>
<div class="col-sm-6 col-md-4 col-lg-3" style="margin:40px auto; float:none;">
    <form method="POST" action="{{ route('login') }}">
        <input type="hidden" name="_token" value="{{ csrf_token() }}">
        <div class="col-xs-12">
            @foreach($errors as $error)
                <div class="alert alert-danger text-danger">
                    <strong>{{$error}}</strong>
                </div>
            @endforeach
            <div class="form-group">
                <div class="input-group">
                    <div class="input-group-addon"><i class="fa fa-fw fa-user"></i></div>
                    <input id="email" type="text" class="form-control" name="email" placeholder="Email" value="{{ Cookie::get('email')}}" autofocus="">
                </div>
            </div>
            <div class="form-group">
                <div class="input-group">
                    <div class="input-group-addon"><i class="fa fa-fw fa-lock"></i></div>
                    <input id="password" type="password" class="form-control" placeholder="Password" name="password">

                    <div class="input-group-addon">
                        <a class="show-password" href="#">
                            <i class="fa fa-fw fa-eye-slash"></i>
                        </a>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xs-6">
            <div class="checkbox">
                <label>
                    <input type="checkbox" name="remember" {{ Cookie::get('remember') ? 'checked' : '' }}> Remember Me
                </label>
            </div>
        </div>
        <div class="col-xs-6">
            <button type="submit" class="btn btn-block btn-primary">Sign in</button>
        </div>
    </form>
</div>
<script src="{{ asset('js/app.js') }}"></script>
<script>
    $(function () {
        $('.show-password').on('click', function () {
            if ($('.show-password i.fa-eye-slash').length) {
                $('.show-password i').attr('class', 'fa fa-fw fa-eye');
                $('#password').attr('type', 'text');
            } else {
                $('.show-password i').attr('class', 'fa fa-fw fa-eye-slash');
                $('#password').attr('type', 'password');
            }

            return false;
        });
    });
</script>
</body>
</html>
