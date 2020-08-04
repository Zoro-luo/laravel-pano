<!DOCTYPE html>
<html>
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
                        <div class="my-select my-select1" @click-list="getCity">
                            <div class="my-select-btn"><span class="btn-text cityName">
                                    @if($panos->cityName == "-1" ) 全部
                                    @elseif($panos->cityName == "1") 武汉
                                    @elseif($panos->cityName == "2") 汉川
                                    @elseif($panos->cityName == "3") 测试城市
                                    @elseif($panos->cityName == "4")武穴
                                    @elseif($panos->cityName == "5")苏州
                                    @elseif($panos->cityName == "6")钟祥
                                    @elseif($panos->cityName == "7")罗田
                                    @elseif($panos->cityName == "8")宜昌
                                    @elseif($panos->cityName == "9")大冶
                                    @elseif($panos->cityName == "10")宜昌2.0升级
                                    @elseif($panos->cityName == "11")郑州
                                    @elseif($panos->cityName == "12")长沙
                                    @elseif($panos->cityName == "13")仙桃
                                    @elseif($panos->cityName == "14")麻城
                                    @elseif($panos->cityName == "15")咸宁
                                    @endif
                                </span><i class="iconfont iconUtubiao-13"></i></div>
                            <ul class="my-select-list">
                                <li class="on" name="cityName" item="-1">全部</li>
                                @foreach($cityName as $k=>$v)
                                    <li name="cityName" item="{{$k}}">{{$v}}</li>
                                @endforeach
                            </ul>
                        </div>
                    </div>
                </div>

                <div class="flex-list">
                    <div class="flex-left item-center">功能状态</div>
                    <div class="separator item-center">:</div>
                    <div class="flex-right display_flex">
                        <div class="my-select my-select1" @click-list="getStatus">
                            <div class="my-select-btn"><span class="btn-text status">
                                    @if($panos->status == "-1" ) 全部
                                    @elseif($panos->status == "1" ) 已上线
                                    @elseif($panos->status == "2" ) 未上线
                                    @endif
                                </span><i class="iconfont iconUtubiao-13"></i></div>
                            <ul class="my-select-list">
                                <li class="on" item="-1">全部</li>
                                <li item="1">已上线</li>
                                <li item="2">未上线</li>
                            </ul>
                        </div>
                    </div>
                </div>
                <div class="flex-list">
                    <div class="flex-left item-center">关键字</div>
                    <div class="separator item-center">:</div>
                    <div class="flex-right">
{{--                         @if($keywords)--}}
{{--                            <div class="my-input"><input type="text" class="inputs keywords" placeholder="{{$keywords}}"></div>--}}
{{--                         @else--}}
{{--                            <div class="my-input"><input type="text" class="inputs keywords" placeholder="楼盘名称/房源名称/房源编号/发布人"></div>--}}
{{--                         @endif--}}

                             <div class="my-input"><input type="text" class="inputs keywords" placeholder="楼盘名称/房源名称/房源编号/发布人"></div>
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
                                <div>房源编号</div>
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
                                <div>VR{{$pano->gid}}</div>
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
                                <div>{{$pano->houseNum}}</div>
                            </div>
                            <div class="table-text table-text6">
                                <div>{{$pano->storeName}}</div>
                            </div>
                            <div class="table-text table-text7">
                                <div>{{$pano->agentName}}</div>
                            </div>
                            <div class="table-text table-text8">
                                <div>{{$pano->updated_at}}</div>
                            </div>
                            <div class="table-text table-text9">
                                <div class="">{{$pano->status == "1" ? "已上线" : "未上线"}}</div>
                            </div>
                            <div class="table-text table-text10">

                                @if($count > 0)
                                    <a class="text-btn" href="{{url('vr/edit/'.$pano->gid)}}"
                                       target="_blank">编辑模型</a>
                                @else
                                    <a class="text-btn" target="_blank">编辑模型</a>
                                @endif

                                <span class="vertical-line">|</span>
                                <div class="text-btn" onclick="listPreview({{$pano->gid}})">预览</div>
                                <span class="vertical-line">|</span>
                                <div class="text-btn  error-btn"
                                     onclick="turnup({{$pano->gid}})">{{$pano->status == "2" ? "上线" : "下线"}}</div>
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
            {!! $panos->appends(['cityName' => $panos->cityName,'status'=>$panos->status])->render('vendor.pagination.vr') !!}

        </div>
    </div>
</div>
</body>
<script src="{{asset('public/static/hotsport')}}/js/jquery-1.8.3.min.js"></script>
<!-- <script src="js/layer/layer.js"></script> -->
<script src="{{asset('public/static/hotsport')}}/js/laydate/laydate.js"></script>
<script src="{{asset('public/static/hotsport')}}/js/common.js"></script>

<!-- <script src="js/echarts.simple.min.js"></script> -->
<script>
    var search = {
        city: "",
        status: "",
    }

    function getCity(event) {
        //search.city = $(event.target).text();
        search.city = $(event.target).attr("item");
    }

    function getStatus(event) {
        //search.status = $(event.target).text();
        search.status = $(event.target).attr("item");
        //console.log( $(event.target).text() )
    }

    //获取url参数
    function getQueryVariable(variable) {
        var query = window.location.search.substring(1);
        var vars = query.split("&");
        for (var i = 0; i < vars.length; i++) {
            var pair = vars[i].split("=");
            if (pair[0] == variable) {
                return pair[1];
            }
        }
        return (false);
    }


    //编辑跳转页
    function listUpdate(panoId) {
        var panoId = paonId;
        $.ajax({
            headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
            url: "{{url('vr/edit')}}",
            type: "POST",
            data: {"panoId": paonId},
            success: function (e) {
                window.open("{{url('vr/online')}}" + "/" + panoId);
            }
        })
    }

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
                            <div class="table-text table-text2"><div>VR${e[0]['gid']}</div></div>
                            <div class="table-text table-text2"><div> ${e[0]['check_at'] == "1" ? "未检查" : "合规"} </div></div>
                            <div class="table-text table-text3"><div>${e[0]['house_name']}</div></div>
                            <div class="table-text table-text4"><div>${e[0]['title']}</div></div>
                            <div class="table-text table-text5"><div>${e[0]['houseNum']}</div></div>
                            <div class="table-text table-text6"><div>${e[0]['storeName']}</div></div>
                            <div class="table-text table-text7"><div>${e[0]['agentName']}</div></div>
                            <div class="table-text table-text8"><div>${e[0]['updated_at']}</div></div>
                            <div class="table-text table-text9"><div class="">${e[0]['status'] == "1" ? "已上线" : "未上线"}</div></div>
                            <div class="table-text table-text10">
                            <a class="text-btn" href="javascript:;" onclick="listUpdate('${e[0]['gid']}')" target="_blank">编辑模型</a>
                            <span class="vertical-line">|</span><div class="text-btn" onclick="listPreview('${e[0]['gid']}')" >预览</div>
                                <span class="vertical-line">|</span><div class="text-btn  error-btn" onclick="turnup('${e[0]['gid']}')" >${e[0]['status'] == "2" ? "上线" : "下线"}</div>
                            </div></div>`;

                $(".index-" + e[0]['pano_id'] + "").html("").replaceWith(panoStr);
            }
        })
    }

    $(function () {
        //搜索提交
        $(".my-btn").click(function () {
            var keywords = $(".keywords").val();
            var createtTime = $("#createtTime").val();
            createtTime = createtTime.replace(/\s*/g,"");       //去除字符串空白字符
            var cityName_url = getQueryVariable("cityName");
            var status_url = getQueryVariable("status");
            if (cityName_url == '') {
                cityName_url = search.city;
            } else {
                if ($(".cityName").hasClass("active")) {
                    cityName_url = search.city;
                } else {
                    cityName_url = getQueryVariable("cityName");
                }
            }

            if (status_url == '') {
                status_url = search.status;
            } else {
                if ($(".status").hasClass("active")) {
                    status_url = search.status;
                } else {
                    status_url = getQueryVariable("status");
                }
            }
            // if (keywords == ''){
            //     var keywords_placeholder = $(".keywords").attr("placeholder");
            //     if (keywords_placeholder == "楼盘名称/房源名称/房源编号/发布人"){
            //         keywords = '';
            //     }
            //     keywords = keywords_placeholder;
            // }
            $.ajax({
                headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
                url: "{{url('vr/list')}}",
                type: "GET",
                data: {
                    "cityName": cityName_url,
                    "status": status_url,
                    "keywords": keywords,
                    "createtTime": createtTime
                },
                success: function (e) {
                      window.location.href = "/pano/vr/list?cityName="+cityName_url+"&status="+status_url+"&keywords="+keywords+"&createtTime="+createtTime;
                }
            })
        });


        //  data: {"cityName": search.city,"status":status,"keywords":keywords,"createtTime":createtTime},
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

        if (page > Math.ceil(count / perPage)) {
            page = Math.ceil(count / perPage);
        }
        if (page <= 0) {
            page = 1;
        }
        window.location.href = "/pano/vr/list?page=" + page;
    })
</script>
</html>
