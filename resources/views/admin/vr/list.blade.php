<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>vr-详情页</title>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link rel="stylesheet" href="{{asset('public/static/hotsport')}}/css/p.min.css">

    <style>
        .layui-laydate .laydate-btns-confirm-disabled > .laydate-btns-confirm {
            color: lightgray;
            cursor: not-allowed;
        }

        .layui-laydate .laydate-btns-confirm-disabled > .laydate-btns-confirm:hover {
            color: lightgray;
        }
    </style>

</head>
<body>
{{--<div class="header"></div>--}}
<div class="container">
    <div class="vr-list-container mt-15">
        <div class="select-box">
            <div class="simplenss-box">
                <div class="flex-list">
                    <div class="flex-left item-center">城市</div>
                    <div class="separator item-center">:</div>
                    <div class="flex-right display_flex">
                        <div class="my-select my-select1" @click-list="addLabel">
                            <div class="my-select-btn"><span class="btn-text">全部</span><i
                                        class="iconfont iconUtubiao-13"></i></div>
                            <ul class="my-select-list">
                                <li class="on">全部</li>
                              @foreach($cityName as $cityVal)
                                <li>{{$cityVal}}</li>
                              @endforeach
                            </ul>
                        </div>
                    </div>
                </div>
                <div class="flex-list">
                    <div class="flex-left item-center">功能状态</div>
                    <div class="separator item-center">:</div>
                    <div class="flex-right display_flex">
                        <div class="my-select my-select1">
                            <div class="my-select-btn"><span class="btn-text">全部</span><i
                                        class="iconfont iconUtubiao-13"></i></div>
                            <ul class="my-select-list">
                                <li class="on">全部</li>
                                <li>已上线</li>
                                <li>未上线</li>
                            </ul>
                        </div>
                    </div>
                </div>
                <div class="flex-list">
                    <div class="flex-left item-center">关键字</div>
                    <div class="separator item-center">:</div>
                    <div class="flex-right">
                        <div class="my-input"><input type="text" class="inputs" placeholder="楼盘名称/房源名称/房源ID/创建人"></div>
                    </div>
                </div>
                <div class="flex-list">
                    <div class="flex-left item-center">创建时间</div>
                    <div class="separator item-center">:</div>
                    <div class="flex-right display_flex">
                        <div class="my-timer"><input type="text" id="createtTime" class="my-select-btn"
                                                     placeholder="开始时间-结束时间"><i class="iconfont iconUtubiao-3"></i>
                        </div>
                    </div>
                </div>
                <div class="flex-list">
                    <div class="my-btn my-btn-green btn-30">查询</div>
                </div>
            </div>
        </div>
        <div class="list-box selects-table-marign">
            <div class="my-table my-table-10 table-big">

                @if($count > 0 )
                    <div class="ele-scroll table-header">
                        <div class="list-top">
                            <span class="selected-text">筛选查询</span>
                            <span class="search-text">共查询到<span class="text">{{$count}}</span>条客源数据</span>
                        </div>
                        <div class="table-item table-title">
                            <div class="table-text table-text1">
                                <div>序号</div>
                            </div>
                            <div class="table-text table-text2">
                                <div>模型ID</div>
                            </div>

                            <div class="table-text table-text2">
                                <div>VR房勘检查状态</div>
                            </div>

                            <div class="table-text table-text3">
                                <div>楼盘名称</div>
                            </div>
                            <div class="table-text table-text4">
                                <div>房源名称</div>
                            </div>
                            <div class="table-text table-text5">
                                <div>房源ID</div>
                            </div>
                            <div class="table-text table-text6">
                                <div>所属部门</div>
                            </div>
                            <div class="table-text table-text7">
                                <div>发布人</div>
                            </div>
                            <div class="table-text table-text8">
                                <div>上传时间</div>
                            </div>
                            <div class="table-text table-text9">
                                <div>功能状态</div>
                            </div>
                            <div class="table-text table-text10">
                                <div>操作</div>
                            </div>
                        </div>
                    </div>
                    @foreach($panos as $pano)
                        <div class="table-item index-{{$pano->pano_id}}">
                            <div class="table-text table-text1">
                                <div>{{$pano->id}}</div>
                            </div>
                            <div class="table-text table-text2">
                                <div>VR{{$pano->pano_id}}</div>
                            </div>

                            <div class="table-text table-text2">
                                <div>
                                    @if($pano->check_at == "1" )
                                        未检查
                                     @elseif($pano->check_at == "2")
                                        合规
                                     @endif
                                </div>
                            </div>

                            <div class="table-text table-text3">
                                <div>{{$pano->house_name}}</div>
                            </div>
                            <div class="table-text table-text4">
                                <div>{{$pano->title}}</div>
                            </div>
                            <div class="table-text table-text5">
                                <div>{{$pano->pano_id}}</div>
                            </div>
                            <div class="table-text table-text6">
                                <div>百瑞景一店</div>
                            </div>
                            <div class="table-text table-text7">
                                <div>刘德马</div>
                            </div>
                            <div class="table-text table-text8">
                                <div>{{$pano->updated_at}}</div>
                            </div>
                            <div class="table-text table-text9">
                                <div class="">{{$pano->status == "1" ? "已上线" : "未上线"}}</div>
                            </div>
                            <div class="table-text table-text10">

                                @if($count > 0)
                                <a class="text-btn" href="{{url('vr/edit/'.$pano->pano_id)}}"
                                   target="_blank">编辑模型</a>
                                @else
                                <a class="text-btn" target="_blank">编辑模型</a>
                                @endif

                                <span class="vertical-line">|</span>
                                <div class="text-btn" onclick="listPreview({{$pano->pano_id}})">预览</div>
                                <span class="vertical-line">|</span>
                                <div class="text-btn  error-btn"
                                     onclick="turnup({{$pano->pano_id}})">{{$pano->status == "2" ? "上线" : "下线"}}</div>
                            </div>
                        </div>
                    @endforeach
                @else
                    <div class="container-single">
                        <div class="one1">
                            <img src="{{asset('public/static/hotsport')}}/css/img/empty_bj.png" alt="" class="img-404">
                            <p class="text caption1">很抱歉，暂时没有数据</p>
                        </div>
                    </div>
                @endif


            </div>
            {{ $panos->links('vendor.pagination.vr') }}
        </div>
    </div>
</div>
</body>
<script src="{{asset('public/static/hotsport')}}/js/jquery-1.8.3.min.js"></script>
<!-- <script src="js/layer/layer.js"></script> -->
<script src="{{asset('public/static/hotsport')}}/js/common.js"></script>
<script src="{{asset('public/static/hotsport')}}/js/laydate/laydate.js"></script>
<!-- <script src="js/echarts.simple.min.js"></script> -->
<script>
    //点击预览另打开预览窗口页
    function listPreview(paonId) {
        var panoId = paonId;
        $.ajax({
            headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
            url: "{{url('vr/seer')}}",
            type: "POST",
            data: {"panoId": paonId},
            success: function (e) {
                if (e == "online") {
                    window.open("{{url('vr/online')}}" + "/" + panoId);
                } else if (e == "outline") {
                    window.open("{{url('vr/look')}}" + "/" + panoId);
                }
            }
        })
    }

    //上下线操作
    function turnup(paonId) {
        var panoId = paonId;
        $.ajax({
            headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
            url: "{{url('vr/turnup')}}",
            type: "POST",
            data: {"panoId": paonId},
            success: function (e) {
                var panoStr = `<div class="table-item index-${e[0]['pano_id']}">
                            <div class="table-text table-text1"><div>${e[0]['id']}</div></div>
                            <div class="table-text table-text2"><div>VR${e[0]['pano_id']}</div></div>
                            <div class="table-text table-text3"><div>${e[0]['house_name']}</div></div>
                            <div class="table-text table-text4"><div>${e[0]['house_name']}-${e[0]['house_type']}随时看房</div></div>
                            <div class="table-text table-text5"><div>${e[0]['pano_id']}</div></div>
                            <div class="table-text table-text6"><div>百瑞景一店</div></div>
                            <div class="table-text table-text7"><div>刘德马</div></div>
                            <div class="table-text table-text8"><div>${e[0]['updated_at']}</div></div>
                            <div class="table-text table-text9"><div class="">${e[0]['status'] == "1" ? "已上线" : "未上线"}</div></div>
                            <div class="table-text table-text10"><a class="text-btn" href="{{url('vr/edit/'.$pano->pano_id)}}" target="_blank">编辑模型</a>
                            <span class="vertical-line">|</span><div class="text-btn" onclick="listPreview('${e[0]['pano_id']}')" >预览</div>
                                <span class="vertical-line">|</span><div class="text-btn  error-btn" onclick="turnup('${e[0]['pano_id']}')" >${e[0]['status'] == "2" ? "上线" : "下线"}</div>
                            </div></div>`;

                $(".index-"+e[0]['pano_id']+"").html("").replaceWith(panoStr);
            }
        })
    }

    $(function () {
        /*myFun.layer.layerDoubleDateTime("#createtTime", {max: `new Date().getFullYear()-new Date().getMonth()-new Date().getDate() 23:59:59`}, function(value, date, endDate) {
            let selectedDate = new Date(endDate.year, endDate.month - 1, endDate.date, endDate.hours, endDate.minutes, endDate.seconds),
                now = new Date();
            console.log(selectedDate > now);
            if(selectedDate > now) {
                var _this = this;
                this.hint("不在有效日期或时间范围内!");
                $(".laydate-footer-btns .laydate-btns-confirm").css("pointer-events", "none");
                $(".laydate-footer-btns").addClass("laydate-btns-confirm-disabled");
                $(".laydate-btns-confirm-disabled").find(".laydate-btns-confirm").on("click", function(e) {
                    e.stopPropagation();
                    _this.hint("不在有效日期或时间范围内!");
                    return false;
                });
            } else {
                $(".laydate-footer-btns .laydate-btns-confirm").css("pointer-events", "auto");
                $(".laydate-footer-btns").removeClass("laydate-btns-confirm-disabled");
            }
        });*/

        myFun.layer.layerdata2("#createtTime", {max: +new Date()});
    });

    function addLabel(event, parent_li, parent_child) {
        var target = event.target,
            value = target.textContent || target.innerText;
        parent_li = parent_li || "";
        parent_child = parent_child || "";
        console.log(this, value, parent_li, parent_child);
    };

    $(".trip-page").click(function () {
        var page = $(".inputs-page").val();     //跳转到多少页
        var count = "{{$count}}";               //总条数
        var perPage = "{{$perPage}}";           //每页显示多少条

        if (page > Math.ceil(count/perPage)){
            page = Math.ceil(count/perPage);
        }
        if (page <= 0){
            page = 1;
        }
        window.location.href = "/pano/vr/list?page=" + page;
    })
</script>
</html>
