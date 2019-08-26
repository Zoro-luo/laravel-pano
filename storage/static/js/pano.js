//打开窗口页面
function openWebpage(url){
    var left = (screen.width - 400) / 2;
    var top = (screen.height - 400) / 2;
    var params = "menubar=no,toolbar=no,status=no,width=600,height=600,top=" + top + ",left=" + left;
    window.open(url,'sharer', params);
}

//发邮件
function sendEmail(email,info){
    window.location.href="mailto:"+email+"?subject="+info;
}

//拨号
function openPhone(num){
    window.location.open = "tel:"+num;
}


function add_text(){
    console.log("aaaa");
}

function add_ajax(hotName,h,v,sceneName)
{
    console.log(hotName);
    console.log(h);
    console.log(v);
    console.log(sceneName);
    /*$.ajax({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        },
        type:"POST",
        url:"{{url('admin/addspot')}}",
        data:{'hotName':hotName, 'h':h, 'v':v, 'sceneName':sceneName},
        success:function(e){
            console.log(e);
        }
    })*/
}
