@extends('admin.layouts.master')
    @section('title')
        全景后台
    @endSection

@section('content-wrapper')
    <!-- Content Wrapper. Contains page content -->
    <div class="content-wrapper">
        <!-- Content Header (Page header) -->
        <section class="content-header">
            <h1>
                仪表盘
                <small>控制面板</small>
            </h1>                                           
            <ol class="breadcrumb">
                <li><a href="{{url('admin')}}"><i class="fa fa-dashboard"></i> Level</a></li>
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
@endsection


