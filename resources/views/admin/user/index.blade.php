@extends('admin.layouts.master')
@section('title')
    用户列表
@endSection

@section('css')
    <link rel="stylesheet" href="{{asset('public/static/AdminLTE')}}/bower_components/datatables.net-bs/css/dataTables.bootstrap.min.css">
    <link rel="stylesheet" href="{{asset('public/static/AdminLTE')}}/dist/css/skins/_all-skins.min.css">
@endSection

@section('content-wrapper')
    <!-- Content Wrapper. Contains page content -->
    <div class="content-wrapper">
        <!-- Content Header (Page header) -->
        <section class="content-header">
            <h1>
                后台管理
                <small>用户管理</small>
            </h1>
            <ol class="breadcrumb">
                <li><a href="#"><i class="glyphicon glyphicon-home"></i> {{auth()->user()->name}}</a></li>
                <li><a href="#">Tables</a></li>
                <li class="active">用户列表</li>
            </ol>
        </section>

        <!-- Main content -->
        <section class="content">
            <div class="row">
                <div class="col-xs-12">
                    <div class="box">
                        <div class="box-header">
                            <h3 class="box-title">用户列表</h3>
                        </div>
                        <!-- /.box-header -->
                        <div class="box-body">
                            <table id="example1" class="table table-bordered table-striped">
                                <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>用户名</th>
                                    <th>邮箱</th>
                                    <th>修改时间</th>
                                    <th>操作</th>
                                </tr>
                                </thead>
                                <tbody>

                                @foreach($users as $user)
                                <tr>
                                    <td>{{$user->id}}</td>
                                    <td>{{$user->name}}</td>
                                    <td>{{$user->email}}</td>
                                    <td>{{$user->updated_at}}</td>
                                    <td>操作</td>
                                </tr>
                                @endforeach
                                </tbody>
           
                            </table>
                        </div>
                        <!-- /.box-body -->
                    </div>
                    <!-- /.box -->
                </div>
                <!-- /.col -->
            </div>
            <!-- /.row -->
        </section>
        <!-- /.content -->
    </div>
    <!-- /.content-wrapper -->
@endsection
@section('script')
    <!-- DataTables -->
    <script src="{{asset('public/static/AdminLTE')}}/bower_components/datatables.net/js/jquery.dataTables.min.js"></script>
    <script src="{{asset('public/static/AdminLTE')}}/bower_components/datatables.net-bs/js/dataTables.bootstrap.min.js"></script>
    <!-- SlimScroll -->
    <script src="{{asset('public/static/AdminLTE')}}/bower_components/jquery-slimscroll/jquery.slimscroll.min.js"></script>
    <!-- FastClick -->
    <script src="{{asset('public/static/AdminLTE')}}/bower_components/fastclick/lib/fastclick.js"></script>
@endsection
@section('scriptCode')
    <script>
        $(function () {
            $('#example1').DataTable()
            $('#example2').DataTable({
                'paging'      : true,
                'lengthChange': true,
                'searching'   : true,
                'ordering'    : true,
                'info'        : true,
                'autoWidth'   : false
            })
        })
    </script>
@endsection

