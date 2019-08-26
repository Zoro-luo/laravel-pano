@extends('admin.layouts.master')
@section('title')
    全景列表
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
                <small>全景管理</small>
            </h1>
            <ol class="breadcrumb">
                <li><a href="#"><i class="glyphicon glyphicon-home"></i> {{auth()->user()->name}}</a></li>
                <li><a href="#">Tables</a></li>
                <li class="active">全景列表</li>
            </ol>
        </section>

        <!-- Main content -->
        <section class="content">
            <div class="row">
                <div class="col-xs-12">
                    <div class="box">
                        <div class="box-header">
                            <h3 class="box-title">全景列表</h3>
                        </div>
                        <!-- /.box-header -->
                        <div class="box-body">
                            <table id="example1" class="table table-bordered table-striped">
                                <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>用户ID</th>
                                   {{-- <th>全景ID</th>--}}
                                    <th>房堪名</th>
                                    <th>面积</th>
                                    <th>全景URL</th>
                                    <th>生成时间</th>
                                    <th>热点操作</th>
                                </tr>
                                </thead>
                                <tbody>

                                @foreach($panos as $pano)
                                    <tr>
                                        <td>{{$pano->id}}</td>
                                        <td>{{$pano->user_id}}</td>
                                       {{-- <td>{{$pano->pano_id}}</td>--}}
                                        <td>{{$pano->house_name.'_'.$pano->house_type.'_'.$pano->house_used }}</td>
                                        <td>{{$pano->house_area }}</td>
                                        <td><a href="{{$pano->panoUrl}}" target="_blank">{{$pano->panoUrl}}</a></td>
                                        <td>{{ $pano->updated_at }}</td>
                                        <td>
                                            {{--<a href="{{route('indexspot',array('pano_id'=>$pano->pano_id ) ) }}" target="_blank"><span>热点编辑</span></a>--}}
                                            <a href="{{url('admin/indexspot/'.$pano->pano_id)}}" target="_blank"><span>热点编辑</span></a>

                                        </td>
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

