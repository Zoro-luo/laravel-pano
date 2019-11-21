var zFun = {
    componentFun: {
        // 文本域字数限制
        limit:function(target) {
            $(target).find("[data-type=wordLimit]")
                .on("input", ".textarea", function(event) {
                var _event = event || window.event;
                _event.stopPropagation();
                var $_this = $(this);
                $_this .parent().find(".num").text($_this .val().length);
                if($(this).hasClass("empty")) return $(this).val($(this).val().replace(/\s/g,''));
            })
        },
        // 图片上传
        uploadFile:function(target) {
            /* 删除弹窗模板 */
            var templateStr = function() { /*
                <template id="delPicture">
                    <div class="del-picture">
                        <p class="tc text">删除后不可恢复，确定删除吗？</p>
                        <div class="btn-group">
                            <div class="my-btn my-btn-green my-btn-confirm">确认</div>
                            <div class="my-btn my-btn-cancel ml-20">取消</div>
                        </div>
                    </div>
                </template>
            */}
            // 本地上传&手机上传
            $(target).find(".upload-box").on("click", ".upload-normaltext", function(e) {
                var event = e || window.event
                , target = event.target || event.srcElement
                , hanlder = $(this).attr("click-upload");
                hanlder && eval(hanlder).call(this, event, target);
            });
            // 设置封面
            $(target).on("click", ".set-cover", function(e) {
                var event = e || window.event
                , $module = $(this).parent()  // 点击设置封面的父元素
                , $oldModule = ''             // 原封面删除掉的父元素
                , hanlder = $module.attr("click-hanlder");
                $oldModule = $module.parents(".section-img").length > 0 ?
                                $module.parents(".section-img").find(".cover").parent(".upload-module")
                                : $module.parents(".upload-box").find(".cover").parent(".upload-module");
                // 当前元素删除设为封面并添加封面
                $(this).remove(".set-cover");
                $module.append("<div class='cover'>封面</div>");
                // 删除旧封面并添加设为封面
                $oldModule.find(".cover").remove();
                $oldModule.append("<div class='set-cover'>设为封面</div>")
                if(hanlder != undefined) eval(hanlder).call(this,event, $module, $oldModule);
            })
            // 进度条
            zFun.componentFun.progressBar(".upload-box>.upload-loading", ".bar-top", "data-progressBar");
            // 上传按钮事件
            $(target).find(".upload-box").find(".upload-nomarlfile").click(function (e) {
                var event = e || window.event
                , hanlder = $(this).attr("click-upload");
                if(hanlder != undefined) eval(hanlder).call(this,event);
            });
            // 删除按钮事件
            $(target).find(".upload-box").on("click", ".upload-module", function(e) {
                var _self = this,
                _event = e || window.event;
                if(_event.target.className.indexOf("close") == -1) return;
                var hanlder = $(this).attr("click-hanlder");
                if($(this).hasClass("upload-file")) {
                    if($("#delPicture").length == 0) {
                        $("body").append(templateStr.toString()
                                 .replace("function() { /*", "")
                                 .replace("*/}", ""));
                    }
                    myFun.layer.opens("#delPicture", "删除图片", "small", function(layero,index) {
                        var _this = this;
                        $(".layer-window").on("click", ".my-btn-confirm", function() {
                            if(hanlder != undefined) eval(hanlder).call(_self,_event,_this,index);
                        });
                    });
                } else {
                    $(this).remove();
                    if(hanlder != undefined) eval(hanlder).call(this,_event);
                }
            });
            // 附件上传图片查看
            $(".upload-box").off("click",".img-file").on("click", ".img-file", function(e) {
                if(myFun != undefined) myFun.layer.photoImg(e.target, ".upload-box", ".img-file", $(this).attr("data-type"));
            });
        },
        // 附件上传
        uploadAccessory:function(target) {
            $(target).on("click", ".accessory-btn",function(e) {
                // console.log(e);
                var hanlder = $(this).attr("@click-upload");
                if(hanlder != undefined) eval(hanlder).call(this, e);
            });
            this.progressBar(".upload-accessory-box .progress-bar", ".bar-top", "data-progressBar");
            $(target).find(".upload-accessory-box")
            .on("click", ".upload-module", function(e) {
                var e = e || window.event;
                if(e.target.className.indexOf("close") == -1) return;
                var hanlder = $(this).attr("@click-handler");
                if(hanlder != undefined) eval(hanlder).call(this,e);
            });
        },
        /*
          discription 进度条
          @parameter parent(父标签)
          @parameter progressElement(进度条)
          @parameter progressName(进度值名称)
        */
        progressBar:function(parent, progressElement, progressName, success) {
            if(!(parent && progressElement && progressName)) return;
            $(parent).each(function(index, element) {
                $(this).find(progressElement).width($(this).attr(progressName)+"%");
                success && success.call(this, progressElement, progressName);
            });
        }
    },
    utils: {
        // 输入禁止输入空格
        disabledSpaceKey:function(parent, target) {
            try {
                $(parent).on("blur" ,target ,function() {
                    return $(this).val($.trim($(this).val()));
                })
                // $(parent).on("", target ,function() {
                //     return $(this).val($(this).val().replace(/\s/g,''));
                // })
            } catch (error) {
                console.log(error);
            }
        },
        // 表单验证
        validationAll:function(arrays) {
            try {
                var flag = true;
                arrays = arrays ||
                [[".identityCard","IsIdentityCard"],[".telphone","IsTelCode"]
                ,[".email","IsEmail"], [".social","IsSocial"]
                ,[".back-card","IsBackCard"]];
                if($(".monitor").length != 0)  { // 判断是否非空判断
                    $(".monitor:visible").each(function(index, elem) {
                        if($(elem).hasClass("my-select")) {
                            if(!$(elem).find(".my-select-list>li").hasClass("on")) {
                                flag = false;
                                $(elem).blur();
                                return flag;
                            }else {
                                flag = true;
                                $(elem).removeClass("error");
                            }
                        }
                        if($(elem).hasClass("inputs")) {
                            if(elem.value && myFun.utils.IsNotEmpty(elem.value)) {
                                flag = true;
                            } else {
                                flag = false;
                                $(elem).hasClass("error") ? "" : $(elem).blur();
                                return flag;
                            }
                        }
                        if($(elem).hasClass("textarea")) {
                            if(elem.value && myFun.utils.IsNotEmpty(elem.value)) {
                                flag = true;
                            } else {
                                flag = false;
                                $(elem).hasClass("error") ? "" : $(elem).blur();
                                return flag;
                            }
                        }
                        if($(elem).hasClass("my-select-btn")) {
                            if(elem.value && myFun.utils.IsNotEmpty(elem.value)) {
                                flag = true;
                            } else {
                                flag = false;
                                $(elem).hasClass("error") ? "" : myFun.utils.dateValida(elem);
                                return flag;
                            }
                        }
                    })
                }
                if(flag && arrays.length != 0) {  // 验证格式的判断
                    $.each(arrays,function(index, args) {
                        if($(args[0]).length <= 0) return; // 没有找到验证的标签 return
                        $(args[0]).each(function(index, ele){
                            if(!flag) return; // 验证错误的格式 return
                            if(ele.classList.contains("monitor")
                                || myFun.utils.IsNotEmpty(ele.value)) {
                                if(myFun.utils[args[1]](ele.value)) {
                                    flag = true;
                                } else {
                                    flag = false;
                                    $(ele).blur();
                                    return flag;
                                }
                            }
                        });
                    })
                }
                // if(!flag) layer.close(loadingIndex);
                return flag;
            } catch(error) {
                console.log(error);
            }
        }
    },
    init() {
        for(var key in this.componentFun){
            if(this.componentFun.hasOwnProperty(key)){
        　　　　this.componentFun[key]('body');
        　　}
        }
    }
}

$(function() {
    zFun.init();
    function Dogz() {
    /**
     *
     * ━━━━━━神兽出没━━━━━━
     * 　　　┏┓　　　┏┓
     * 　　┏┛┻━━━┛┻┓
     * 　　┃　　　　　　　┃
     * 　　┃　　　━　　　┃
     * 　　┃　┳┛　┗┳　┃
     * 　　┃　　　　　　　┃
     * 　　┃　　　┻　　　┃
     * 　　┃　　　　　　　┃
     * 　　┗━┓　　　┏━┛Code is far away from bug with the animal protecting
     * 　　　　┃　　　┃    神兽保佑,代码无bug
     * 　　　　┃　　　┃
     * 　　　　┃　　　┗━━━┓
     * 　　　　┃　　　　　　　┣┓
     * 　　　　┃　　　　　　　┏┛
     * 　　　　┗┓┓┏━┳┓┏┛
     * 　　　　　┃┫┫　┃┫┫
     * 　　　　　┗┻┛　┗┻┛
     *
     * ━━━━━━感觉萌萌哒━━━━━━
     */
    }
    //console.log(Dogz.toString().replace("function Dogz() {","").replace("}", ""));
    // (_=>[..."`1234567890-=~~QWERTYUIOP[]\\~ASDFGHJKL;'~~ZXCVBNM,./~"].map(x=>(o+=`/${b='_'.repeat(w=x<y?2:' 667699'[x=["BS","TAB","CAPS","ENTER"][p++]||'SHIFT',p])}\\|`,m+=y+(x+'    ').slice(0,w)+y+y,n+=y+b+y+y,l+=' __'+b)[73]&&(k.push(l,m,n,o),l='',m=n=o=y),m=n=o=y='|',p=l=k=[])&&k.join``)();
});
