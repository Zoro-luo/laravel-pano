@extends('layouts.master')

@section('script')
    <script src="{{asset('public/static/AdminLTE')}}/bower_components/jquery/dist/jquery.min.js"></script>
@endsection

@section('content')
    <div class="container">
        <div class="row">
            <div class="col-md-8 col-md-offset-2">
                <div class="panel panel-default">
                    <div class="panel-heading">前台会员登录</div>
                    <div class="panel-body">
                        <form class="form-horizontal" role="form" method="POST" action="{{ url('member/login') }}">
                            {{ csrf_field() }}

                            <div class="form-group{{ $errors->has('email') ? ' has-error' : '' }}">
                                <label for="email" class="col-md-4 control-label">E-Mail Address</label>

                                <div class="col-md-6">
                                    <input id="email" type="email" class="form-control" name="email"
                                           value="{{ old('email') }}" required autofocus>

                                    @if ($errors->has('email'))
                                        <span class="help-block">
                                        <strong class="email-str">{{ $errors->first('email') }}</strong>
                                    </span>
                                    @endif
                                </div>
                            </div>

                            <div class="form-group{{ $errors->has('password') ? ' has-error' : '' }}">
                                <label for="password" class="col-md-4 control-label">Password</label>

                                <div class="col-md-6">
                                    <input id="password" type="password" class="form-control" name="password" required>

                                    @if ($errors->has('password'))
                                        <span class="help-block">
                                        <strong>{{ $errors->first('password') }}</strong>
                                    </span>
                                    @endif
                                </div>
                            </div>

                            {{--<div class="form-group">
                                <div class="col-md-6 col-md-offset-4">
                                    <div class="checkbox">
                                        <label>
                                            <input type="checkbox" name="remember" {{ old('remember') ? 'checked' : ''}}> Remember Me
                                        </label>
                                    </div>
                                </div>
                            </div>--}}

                            <div class="form-group">
                                <div class="col-md-8 col-md-offset-4">
                                    <button type="submit" class="btn btn-primary">
                                        Login
                                    </button>

                                    {{--<a class="btn btn-link" href="{{ url('/password/reset') }}">
                                        Forgot Your Password?
                                    </a>--}}
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('scriptCode')
    <script>
        $(document).ready(function () {
            $("#email").blur(function () {
                var test = $(this).val();
                $.ajax({
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    url: "checkEmail",
                    type: "post",
                    data: "email=" + test,
                    success: function (res) {
                        if (res == 'No') {
                            $("input[name='email']").after("<strong class='email-str'>邮箱错误!</strong>");
                            $("button[type='submit']").attr('disabled', 'disabled');
                        }else{
                            $('.email-str').empty();
                            $("button[type='submit']").removeAttr('disabled');
                        }
                    }
                })
            })
        })
    </script>
@endsection

