/*将根元素字号大小设置为：屏宽与图宽的比；  
由于chrom对10px以下的字不再缩小，而且手机屏  
都比较小，所以作为默认字体大小又乘了100，这样  
计算其他元素大小时，量出图上大小再除以100就可以了*/

function defaultfont() {
  var ymW = window.innerWidth
  var sw = ymW > 750 ? 750 : ymW
  var pw = 750
  var f = (100 * sw) / pw
  document.getElementsByTagName('html')[0].style.fontSize = f + 'px'
}
defaultfont()
/*之所以要延时100ms再调用这个函数是因为  
如果不这样屏幕宽度加载会有误差*/
setTimeout(function() {
  defaultfont()
}, 100)
var w_height = window.innerWidth
window.onresize = function() {
  defaultfont()
}
// 加载底部网警信息的方法，不要删除；
/* $(function(){
    $("footer").load("footer.html",function(){
        var screenHeight = $("body").height();
        var headerHeight = $("header").height();
        var selectHeight = $(".fy-select").height();
        var fontSize = parseInt($("html").css("font-size"));
        var copyrightHeight = fontSize * 1.8;
        var minHeight = screenHeight - headerHeight - copyrightHeight - selectHeight;
        $("section.main").css("min-height",minHeight + "px");
    });
}); */

// 处理-webkit-overflow-scrolling: touch 触发偶尔卡住的问题
function iosTouchKa(selector){
    $(selector).scroll(function(){
        // debugger
        var fullHeight = this.scrollHeight;
        var screenHeight = $(this).innerHeight();
        var scrollTop = $(this).scrollTop();
        if(fullHeight == (screenHeight + scrollTop)){
            $(this).scrollTop(scrollTop - 1);
        }
        if(scrollTop == 0){
            $(this).scrollTop(scrollTop + 1);
        }
    });
}