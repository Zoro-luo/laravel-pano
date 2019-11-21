<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <title>@yield('title','Pano')</title>
    <!-- Tell the browser to be responsive to screen width -->
    <meta content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no" name="viewport">
    <link rel="stylesheet"
          href="{{asset('public/static/AdminLTE')}}/bower_components/bootstrap/dist/css/bootstrap.min.css">
    <!-- Font Awesome -->
    <link rel="stylesheet"
          href="{{asset('public/static/AdminLTE')}}/bower_components/font-awesome/css/font-awesome.min.css">
    <!-- Ionicons -->
    <link rel="stylesheet" href="{{asset('public/static/AdminLTE')}}/bower_components/Ionicons/css/ionicons.min.css">
    <!-- Theme style -->
    <link rel="stylesheet" href="{{asset('public/static/AdminLTE')}}/dist/css/AdminLTE.min.css">
    <!-- AdminLTE Skins. We have chosen the skin-blue for this starter
          page. However, you can choose any other skin. Make sure you
          apply the skin class to the body tag so the changes take effect. -->
    <link rel="stylesheet" href="{{asset('public/static/AdminLTE')}}/dist/css/skins/skin-blue.min.css">

    @section('css')
    @show

    <!-- HTML5 Shim and Respond.js IE8 support of HTML5 elements and media queries -->
    <!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
    <!--[if lt IE 9]>
    <script src="{{asset('public/static/AdminLTE')}}/oss/html5shiv.min.js"></script>
    <script src="{{asset('public/static/AdminLTE')}}/oss/respond.min.js"></script>
    <![endif]-->

</head>
<!--
BODY TAG OPTIONS:
=================
Apply one or more of the following classes to get the
desired effect
|---------------------------------------------------------|
| SKINS         | skin-blue                               |
|               | skin-black                              |
|               | skin-purple                             |
|               | skin-yellow                             |
|               | skin-red                                |
|               | skin-green                              |
|---------------------------------------------------------|
|LAYOUT OPTIONS | fixed                                   |
|               | layout-boxed                            |
|               | layout-top-nav                          |
|               | sidebar-collapse                        |
|               | sidebar-mini                            |
|---------------------------------------------------------|
-->
<body class="hold-transition skin-blue sidebar-mini">
<div class="wrapper">

@section('main-header')
    <!-- Main Header -->
        <header class="main-header">

            <!-- Logo -->
            <a href="{{url('admin')}}" class="logo">
                <!-- mini logo for sidebar mini 50x50 pixels -->
                <span class="logo-mini"><b>轻镜</b></span>
                <!-- logo for regular state and mobile devices -->
                <span class="logo-lg"><b>全景</b>漫游</span>
            </a>

            <!-- Header Navbar -->
            <nav class="navbar navbar-static-top" role="navigation">
                <!-- Sidebar toggle button-->
                <a href="#" class="sidebar-toggle" data-toggle="push-menu" role="button">
                    <span class="sr-only">Toggle navigation</span>
                </a>
                <!-- Navbar Right Menu -->
                <div class="navbar-custom-menu">
                    <ul class="nav navbar-nav">

                        <li class="dropdown user user-menu">
                            <!-- Menu Toggle Button -->
                            <a href="#" class="dropdown-toggle" data-toggle="dropdown">
                                <!-- The user image in the navbar-->
                                <img src="{{asset('public/static/AdminLTE')}}/dist/img/user2-160x160.jpg"
                                     class="user-image" alt="User Image">
                                <!-- hidden-xs hides the username on small devices so only the image appears. -->
                                <span class="hidden-xs">{{ Auth::user()->name }}</span>
                            </a>
                            <ul class="dropdown-menu">
                                <!-- The user image in the menu -->
                                <li class="user-header">
                                    <img src="{{asset('public/static/AdminLTE')}}/dist/img/user2-160x160.jpg"
                                         class="img-circle" alt="User Image">

                                    <p>
                                        {{ auth()->user()->name }}
                                        <small>Member since {{ isset(Auth::user()->created_at) ? Auth::user()->created_at->format('Y-m-d H:i') : Auth::user()->updated_at->format('Y-m-d H：i') }} </small>
                                    </p>
                                </li>

                                <!-- Menu Footer-->
                                <li class="user-footer">
                                    <div class="pull-left">
                                        <a href="#" class="btn btn-default btn-flat">Profile</a>
                                    </div>
                                    <div class="pull-right">
                                        <a class="btn btn-default btn-flat" href="{{ url('/logout') }}"
                                           onclick="event.preventDefault();
                                                     document.getElementById('admin-logout-form').submit();">
                                            Logout
                                        </a>
                                        <form id="admin-logout-form" action="{{ url('/logout') }}" method="POST" style="display: none;">
                                            {{ csrf_field() }}
                                        </form>
                                    </div>
                                </li>
                            </ul>
                        </li>
                        <!-- Control Sidebar Toggle Button -->
                        <li>
                            <a href="#" data-toggle="control-sidebar"><i class="fa fa-gears"></i></a>
                        </li>
                    </ul>
                </div>
            </nav>
        </header>
@show

@section('main-sidebar')
    <!-- Left side column. contains the logo and sidebar -->
        <aside class="main-sidebar">

            <!-- sidebar: style can be found in sidebar.less -->
            <section class="sidebar">

                <!-- Sidebar Menu -->
                <ul class="sidebar-menu" data-widget="tree">
                    <li class="header">主菜单</li>
                    <!-- Optionally, you can add icons to the links -->
                    <li ><a href="{{url('admin')}}"><i class="glyphicon glyphicon-paperclip"></i> <span>仪表盘</span></a></li>
                    <li ><a href="{{url('user/index')}}"><i class="glyphicon glyphicon-home"></i> <span>用户管理</span></a></li>
                    {{--<li><a href="#"><i class="glyphicon glyphicon-tags"></i> <span>分类管理</span></a></li>--}}
                    <li class="active"><a href="{{url('pano/index')}}"><i class="glyphicon glyphicon-film"></i> <span>全景管理</span></a></li>

                    <li class="active"><a href="{{url('vr/list')}}"><i class="glyphicon glyphicon-film"></i> <span>VR管理</span></a></li>
                    {{--<li class="treeview">
                        <a href="#"><i class="fa fa-link"></i> <span>Multilevel</span>
                            <span class="pull-right-container">
                <i class="fa fa-angle-left pull-right"></i>
              </span>
                        </a>
                        <ul class="treeview-menu">
                            <li><a href="#">Link in level 2</a></li>
                            <li><a href="#">Link in level 2</a></li>
                        </ul>
                    </li>--}}
                </ul>
                <!-- /.sidebar-menu -->
            </section>
            <!-- /.sidebar -->
        </aside>
@show

@section('content-wrapper')
    <!-- Content Wrapper. Contains page content -->
        <div class="content-wrapper">
            <!-- Content Header (Page header) -->
            <section class="content-header">
                <h1>
                    Page Header
                    <small>Optional description</small>
                </h1>
                <ol class="breadcrumb">
                    <li><a href="#"><i class="fa fa-dashboard"></i> Level</a></li>
                    <li class="active">Here</li>
                </ol>
            </section>

            <!-- Main content -->
            <section class="content container-fluid">

                <!--------------------------
                  | Your Page Content Here |
                  -------------------------->

            </section>
            <!-- /.content -->
        </div>
        <!-- /.content-wrapper -->
@show

@section('main-footer')
    <!-- Main Footer -->
        <footer class="main-footer">
            <!-- To the right -->
            <div class="pull-right hidden-xs">
                Anything you want
            </div>
            <!-- Default to the left -->
            <strong>Copyright &copy; 2016 <a href="#">Company</a>.</strong> All rights reserved.
        </footer>
@show

@section('control-sidebar')
    <!-- Control Sidebar -->
        <aside class="control-sidebar control-sidebar-dark">
            <!-- Create the tabs -->
            <ul class="nav nav-tabs nav-justified control-sidebar-tabs">
                <li class="active"><a href="#control-sidebar-home-tab" data-toggle="tab"><i class="fa fa-home"></i></a>
                </li>
                <li><a href="#control-sidebar-settings-tab" data-toggle="tab"><i class="fa fa-gears"></i></a></li>
            </ul>
            <!-- Tab panes -->
            <div class="tab-content">
                <!-- Home tab content -->
                <div class="tab-pane active" id="control-sidebar-home-tab">
                    <h3 class="control-sidebar-heading">Recent Activity</h3>
                    <ul class="control-sidebar-menu">
                        <li>
                            <a href="javascript:;">
                                <i class="menu-icon fa fa-birthday-cake bg-red"></i>

                                <div class="menu-info">
                                    <h4 class="control-sidebar-subheading">Langdon's Birthday</h4>

                                    <p>Will be 23 on April 24th</p>
                                </div>
                            </a>
                        </li>
                    </ul>
                    <!-- /.control-sidebar-menu -->

                    <h3 class="control-sidebar-heading">Tasks Progress</h3>
                    <ul class="control-sidebar-menu">
                        <li>
                            <a href="javascript:;">
                                <h4 class="control-sidebar-subheading">
                                    Custom Template Design
                                    <span class="pull-right-container">
                    <span class="label label-danger pull-right">70%</span>
                  </span>
                                </h4>

                                <div class="progress progress-xxs">
                                    <div class="progress-bar progress-bar-danger" style="width: 70%"></div>
                                </div>
                            </a>
                        </li>
                    </ul>
                    <!-- /.control-sidebar-menu -->

                </div>
                <!-- /.tab-pane -->
                <!-- Stats tab content -->
                <div class="tab-pane" id="control-sidebar-stats-tab">Stats Tab Content</div>
                <!-- /.tab-pane -->
                <!-- Settings tab content -->
                <div class="tab-pane" id="control-sidebar-settings-tab">
                    <form method="post">
                        <h3 class="control-sidebar-heading">General Settings</h3>

                        <div class="form-group">
                            <label class="control-sidebar-subheading">
                                Report panel usage
                                <input type="checkbox" class="pull-right" checked>
                            </label>

                            <p>
                                Some information about this general settings option
                            </p>
                        </div>
                        <!-- /.form-group -->
                    </form>
                </div>
                <!-- /.tab-pane -->
            </div>
        </aside>

        <!-- /.control-sidebar -->
        <!-- Add the sidebar's background. This div must be placed
        immediately after the control sidebar -->
        <div class="control-sidebar-bg"></div>
@show
</div>
<!-- ./wrapper -->

<!-- REQUIRED JS SCRIPTS -->

<!-- jQuery 3 -->
<script src="{{asset('public/static/AdminLTE')}}/bower_components/jquery/dist/jquery.min.js"></script>
<!-- Bootstrap 3.3.7 -->
<script src="{{asset('public/static/AdminLTE')}}/bower_components/bootstrap/dist/js/bootstrap.min.js"></script>
<!-- AdminLTE App -->
<script src="{{asset('public/static/AdminLTE')}}/dist/js/adminlte.min.js"></script>

@section('script')
@show

<!-- Optionally, you can add Slimscroll and FastClick plugins.
     Both of these plugins are recommended to enhance the
     user experience. -->

@section('scriptCode')
@show
</body>
</html>
