@if ($paginator->hasPages())
    <div class="turn-pageing">
        <div class="turn-page-content">
            <div class="page">
                {{-- Previous Page Link --}}
                @if ($paginator->onFirstPage())
                    {{--<li class="disabled"><span>&laquo;</span></li>--}}
                    <a href="javascript:;"  class="prev disabled">上一页</a>
                @else
                    {{--<li><a href="{{ $paginator->previousPageUrl() }}" rel="prev">&laquo;</a></li>--}}
                    <a href="{{ $paginator->previousPageUrl() }}" class="prev">上一页</a>
                @endif


                {{-- Pagination Elements --}}
                @foreach ($elements as $element)
                    {{-- "Three Dots" Separator --}}
                    @if (is_string($element))
                        {{--<li class="disabled"><span>{{ $element }}</span></li>--}}
                        <a href="javascript:;" class="disabled">{{ $element }}</a>
                    @endif

                    {{-- Array Of Links --}}
                    @if (is_array($element))
                        @foreach ($element as $page => $url)
                            @if ($page == $paginator->currentPage())
                                {{--<li class="active"><span>{{ $page }}</span></li>--}}
                                <a href="javascript:;" class="active on">{{ $page }}</a>
                            @else
                                {{--<li><a href="{{ $url }}">{{ $page }}</a></li>--}}
                                <a href="{{ $url }}" >{{ $page }}</a>
                            @endif
                        @endforeach
                    @endif
                @endforeach


                {{--<a href="javascript:;">1</a>
                <a href="javascript:;">2</a>
                <a href="javascript:;" class="on">3</a>
                <a href="javascript:;">...</a>
                <a href="javascript:;">100</a>--}}

                {{-- Next Page Link --}}
                @if ($paginator->hasMorePages())
                    {{--<li><a href="{{ $paginator->nextPageUrl() }}" rel="next">&raquo;</a></li>--}}
                    <a href="{{ $paginator->nextPageUrl() }}" class="next">下一页</a>
                @else
                   {{-- <li class="disabled"><span>&raquo;</span></li>--}}
                    <a href="javascript:;" class="next disabled">下一页</a>
                @endif


            </div>
            <div class="caption1 ">到第</div><div class="my-input page-num"><input type="text" class="inputs"></div><div class="caption1 ">页</div>
            <div class="my-btn">确定</div>
            <div class="text">共有{{ $paginator->total() }}条</div>
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
@endif
