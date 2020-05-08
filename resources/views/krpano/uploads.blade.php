@extends('layouts.app')

@section('script')
    <script src="{{asset('public/static/AdminLTE')}}/bower_components/jquery/dist/jquery.min.js"></script>
@endsection

@section('content')
    <div class="container">
        <div class="row">
            <div class="col-md-8 col-md-offset-2">
                <div class="panel panel-default">
                    <div class="panel-heading">多图片上传全景图</div>
                    <div class="panel-body">
                        <form class="form-horizontal" action="{{ url("krpano/panos") }}" method="post"
                              enctype="multipart/form-data">
                            {!! csrf_field() !!}
                            <div class="form-group">
                                <label for="parlour" class="parlour">客厅</label>
                                <input type="file"  name="filename['parlour'][]"/>
                                <input type="file"  name="filename['parlour'][]"/>
                            </div>
                            <div class="form-group">
                                <label for="bedroom" class="bedroom">卧室</label>
                                <input type="file"  name="filename['bedroom'][]"/>
                                <input type="file"  name="filename['bedroom'][]"/>
                                <input type="file"  name="filename['bedroom'][]"/>
                            </div>
                            <div class="form-group">
                                <label for="toilet" class="toilet">卫生间</label>
                                <input type="file" id="toilet1" name="filename['toilet'][]"/>
                            </div>
                            <div class="form-group">
                                <label for="cookroom" class="cookroom">厨房</label>
                                <input type="file"   name="filename['cookroom'][]"/>
                            </div>
                            <div class="form-group">
                                <label for="exterior" class="exterior">

                                    <input type="text" name="custom" placeholder="自定义" width="30">

                                </label>
                                <input type="file"   name="filename['custom'][]"/>
                                <input type="file"   name="filename['custom'][]"/>
                                <input type="file"   name="filename['custom'][]"/>
                            </div>

                            <input type="hidden" name="pano_id" value="22" />
                            <input type="hidden" name="user_id" value="33" />
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


@section('scriptCode')
    <script>
        $(document).ready(function () {
           /* $(".btn-primary").click(function () {
                //客厅
                 var parlour1 = $("input[name='parlour[]']").get(0).files[0];
                if (parlour1 || parlour2 || parlour3 || bedroom1 || bedroom2 || bedroom3 || toilet1 || cookroom1 || exterior1 || exterior2 || exterior3) {
                    $(".form-horizontal").submit();
                }
            });*/

        });
    </script>
@endsection
