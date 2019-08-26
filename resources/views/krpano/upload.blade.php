@extends('layouts.master')

@section('content')
    <div class="container">
        <div class="row">
            <div class="col-md-8 col-md-offset-2">
                <div class="panel panel-default">
                    <div class="panel-heading">上传全景图</div>
                    <div class="panel-body">
                        {{--<form action="{{ url("krpano/upload") }}" method="post" enctype="multipart/form-data">--}}
                        <form class="form-horizontal" action="{{ url("krpano/pano") }}" method="post"
                              enctype="multipart/form-data">
                            {{--<input type="hidden" name="_token" value="{{ csrf_token() }}" />--}}
                            {!! csrf_field() !!}
                            <div class="form-group">
                                <input type="text" class="" style="display: inline" value="" name="mb_name[]">
                                <input type="file" style="display: inline" name="filename[]" id="file1"/>
                            </div>

                            <div class="form-group">
                                <input type="text" value="" style="display: inline" name="mb_name[]">
                                <input type="file" style="display: inline" name="filename[]" id="file2"/>
                            </div>

                            <div class="form-group">
                                <input type="text" value=""style="display: inline"  name="mb_name[]">
                                <input type="file" style="display: inline" name="filename[]" id="file3"/>
                            </div>

                            <div class="form-group">
                                <input type="text" value="" style="display: inline" name="mb_name[]">
                                <input type="file" style="display: inline" name="filename[]" id="file4"/>
                            </div>

                            <div class="form-group">
                                <input type="text" value="" style="display: inline" name="mb_name[]">
                                <input type="file" style="display: inline" name="filename[]" id="file5"/>
                            </div>

                            <div class="form-group">
                                <input type="text" value="" style="display: inline" name="mb_name[]">
                                <input type="file" style="display: inline" name="filename[]" id="file6"/>
                            </div>

                            <input type="hidden" name="pano_id" value="7" />
                            <input type="hidden" name="user_id" value="2" />
                            <input type="hidden" name="house_name" value="国际百纳" />
                            <input type="hidden" name="house_used" value="住宅" />
                            <input type="hidden" name="house_type" value="3室2厅" />
                            <input type="hidden" name="house_area" value="123平米" />
                            <input type="hidden" name="remark" value="默认的备注" />

                            <div class="form-group">
                                <input type="submit" class="btn btn-primary" name="submit" value="Submit"/>
                            </div>
                        </form>

                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
