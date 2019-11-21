<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>vr-详情页</title>
    <link rel="stylesheet" href="{{asset('public/static/hotsport')}}/css/p.min.css">
</head>
<body>
<div class="header"></div>
<div class="container">
    <div class="vr-list-container mt-15">
        <div class="select-box">
            <div class="simplenss-box">
                <div class="flex-list">
                    <div class="flex-left item-center">城市</div>
                    <div class="separator item-center">:</div>
                    <div class="flex-right display_flex">
                        <div class="my-select my-select1" @click-list="addLabel">
                            <div class="my-select-btn"><span class="btn-text">全部</span><i class="iconfont iconUtubiao-13"></i></div>
                            <ul class="my-select-list">
                                <li class="on">全部</li>
                                <li>武汉</li>
                                <li>合肥</li>
                            </ul>
                        </div>
                    </div>
                </div>
                <div class="flex-list">
                    <div class="flex-left item-center">功能状态</div>
                    <div class="separator item-center">:</div>
                    <div class="flex-right display_flex">
                        <div class="my-select my-select1">
                            <div class="my-select-btn"><span class="btn-text">全部</span><i class="iconfont iconUtubiao-13"></i></div>
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
                        <div class="my-timer"><input type="text" id="createtTime" class="my-select-btn" placeholder="开始时间-结束时间"><i class="iconfont iconUtubiao-3"></i></div>
                    </div>
                </div>
                <div class="flex-list">
                    <div class="my-btn my-btn-green btn-30">查询</div>
                </div>
            </div>
        </div>
        <div class="list-box selects-table-marign">
            <div class="my-table my-table-10 table-big">
                <div class="ele-scroll table-header">
                    <div class="list-top">
                        <span class="selected-text">筛选查询</span>
                        <span class="search-text">共查询到<span class="text">63</span>条客源数据</span>
                    </div>
                    <div class="table-item table-title">
                        <div class="table-text table-text1"><div>序号</div></div>
                        <div class="table-text table-text2"><div>模型ID</div></div>
                        <div class="table-text table-text3"><div>楼盘名称</div></div>
                        <div class="table-text table-text4"><div>房源名称</div></div>
                        <div class="table-text table-text5"><div>房源ID</div></div>
                        <div class="table-text table-text6"><div>业务部门</div></div>
                        <div class="table-text table-text7"><div>上传人</div></div>
                        <div class="table-text table-text8"><div>上传时间</div></div>
                        <div class="table-text table-text9"><div>功能状态</div></div>
                        <div class="table-text table-text10"><div>操作</div></div>
                    </div>
                </div>

                @foreach($panos as $pano)
                <div class="table-item">
                    <div class="table-text table-text1"><div>{{$pano->id}}</div></div>
                    <div class="table-text table-text2"><div>VR{{$pano->pano_id}}</div></div>
                    <div class="table-text table-text3"><div>{{$pano->house_name}}</div></div>
                    <div class="table-text table-text4"><div>{{$pano->house_name}}-{{$pano->house_type}} 随时看房</div></div>
                    <div class="table-text table-text5"><div>{{$pano->pano_id}}</div></div>
                    <div class="table-text table-text6"><div>百瑞景一店</div></div>
                    <div class="table-text table-text7"><div>刘德马</div></div>
                    <div class="table-text table-text8"><div>{{$pano->updated_at}}</div></div>
                    <div class="table-text table-text9"><div class="">已上线</div></div>
                    <div class="table-text table-text10">
                        <a class="text-btn" href="{{url('vr/edit/'.$pano->pano_id)}}" target="_blank">编辑模型</a><span class="vertical-line">|</span>
                        <div class="text-btn">预览</div><span class="vertical-line">|</span>
                        <div class="text-btn error-btn">下线</div>
                    </div>
                </div>
                @endforeach

            </div>
            <div class="turn-pageing">
                <div class="turn-page-content">
                    <div class="page">
                        <a href="javascript:;" class="prev">上一页</a>
                        <a href="javascript:;">1</a>
                        <a href="javascript:;">2</a>
                        <a href="javascript:;" class="on">3</a>
                        <a href="javascript:;">...</a>
                        <a href="javascript:;">100</a>
                        <a href="javascript:;" class="next">下一页</a>
                    </div>
                    <div class="caption1 ">到第</div><div class="my-input page-num"><input type="text" class="inputs"></div><div class="caption1 ">页</div>
                    <div class="my-btn">确定</div>
                    <div class="text">共有100条</div>
                    <div class="my-select my-select1 btn-30 bottom">
                        <div class="my-select-btn"><span class="btn-text">20条</span><i class="iconfont iconUtubiao-13"></i></div>
                        <ul class="my-select-list">
                            <li>5条</li>
                            <li>10条</li>
                            <li>20条</li>
                            <li>30条</li>
                            <li>50条</li>
                        </ul>
                    </div>
                </div>
            </div>
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
    $(function(){
        myFun.layer.layerDoubleDateTime("#createtTime", {max: +new Date()});
    });
    function addLabel(event, parent_li, parent_child) {
        var target = event.target,
            value = target.textContent || target.innerText;
        parent_li = parent_li || "";
        parent_child = parent_child || "";
        console.log(this, value, parent_li, parent_child);
    }
</script>
</html>
