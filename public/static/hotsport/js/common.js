
var index = null//记录layer弹窗组件
,loadingIndex = null
,houseType_text = ['状态','公私','标签','带看次数','跟进时间','委托时间','来源','客源维护人','意向区域']//房子类型
,mixRange = '2018/01/01 '//最小日期
,headerIndex = 0   //头部索引值
,blurChange = jieliu(function(self, des) { // 消息弹窗节流
    self && $(self).hasClass('error') ? '' : $(self).parent().addClass("error");
    myFun.layer.msg(des, 0, 1500);
}, 2000)
,dataValidatorDebounce = debounce(function(elem) { // 日期非空验证点击事件添加防抖
    myFun.utils.dateValidatorClick(elem);
}, 150);

//公用函数 ，引入即可
var myFun = {
    /**
     * 防抖函数，返回函数连续调用时，空闲时间必须大于或等于wait,func才会执行
     * @param {function} func       回调函数
     * @param {number} wait         表示func执行的间隔
     * @param {boolean} immediate   设置为true是，是否立即调用函数
     * @return {function}           返回客户调用函数
     */
    debounce(func, wait = 50, immediate = true) {
        let timer, context, args;

        // 延迟执行函数
        const later = () => setTimeout(() => {
            timer = null;
            if(!immediate) {
                func.apply(context, args);
                context = args = null;
            }
        }, wait);

        // 返回实际调用函数
        return function(...params) {
            if(!timer) {
                timer = later();
                if(immediate) {
                    func.apply(this, params);
                } else {
                    context = this;
                    args = params;
                }
            // 如果已有延迟执行函数（later），调用的时候清楚原来的并重新设定一个
            // 这样做延迟函数会重新计时
            } else {
                clearTimeout(timer);
                timer = later();
            }
        }
    },
    //第一次执行，比如引入模块
    first:function(){
        //给header绑定点击事件
        //全局设置headerIndex 设置当前选中哪个 headerIndex = 5;
        var timer1 = null;
        var timer2 = null;
         //不能移入除了my-options元素里面
        $('body').on('drop',null,function(e){
            e = e || window.event;
            console.log(e.target)
            if(e.target.className != 'my-options'){
                e.stopPropagation();
                return false;
            }
        })
        //加载examine-header.html文件
        $('.examine-header').load('./module/examine-header.html',function(){
            $('.navbar-item1').eq(headerIndex || 0).addClass('active');
        });
        //加载header.html文件 的回调函数
        $('.header').load('./module/header.html',function(){
            // console.log(headerIndex)
            //实现移入移出显示隐藏二级导航
            $('.nav-item').not('.navbar-logo').on('mouseenter',function(){
                var th = this;
                clearTimeout(timer1)
                clearTimeout(timer2)
                $('.nav-item').removeClass('on');
                $(th).addClass('on');
                $(th).parent().siblings().find('.navbar-lev2').stop().slideUp(100);
                $(th).hasClass('on')?$(th).siblings('.navbar-lev2').slideDown(300,function(){
                    $(th).siblings('.navbar-lev2').css('overflow','auto')
                }):$(th).siblings('.navbar-lev2').stop().slideUp(100);
            }).on('mouseleave',function(){
                var th = this;
                timer1 = setTimeout(function(){
                    $(th).siblings('.navbar-lev2').stop().slideUp(100);
                    $('.nav-item').removeClass('on');
                    $(th).siblings('.navbar-lev2').css('overflow','hidden')
                },200)
            }).eq(headerIndex||0).addClass('active');

            $('.navbar-lev2').on('mouseleave',function(){
                var th = this;
                $(th).css('overflow','auto')
                timer2 = setTimeout(function(){
                    $(th).stop().slideUp(100);
                    $('.nav-item').removeClass('on');
                    $(th).siblings('.navbar-lev2').css('overflow','hidden')
                },200)
            }).on('mouseenter',function(){
                clearTimeout(timer1);
            });

            $('.navbar').on('click','a',function(e){
                $('.navbar').find('.navbar-lev2').stop().slideUp(0);
            })
            //绑定修改密码
            $('.updated').on('click',function(){
                myFun.layer.opens('#change-password','修改密码','small');

                $('.change-password .my-btn-yes').on('click',function(){
                    myFun.layer.msg('您的密码已修改成功！')
                })
            });
        });
        //点击置顶
        $('.toTop').on('click',null,function(){
            $('html').animate({scrollTop: 0},250);
        });
        //修改iframe input获取不到的问题
        $('body').append('<input id="my-input-focus" style="display:none;" tabindex="1"/>').find('#my-input-focus').focus()
    },
    //window点击事件
    windowClick:function(){
        $(window).click(function(e){
            var event = e || window.event;
            var target = event.target || event.srcElement;
            var targetClass = target.className;
            var tarParent = $(target).parent()[0];
            var staffInput = $('.my-options-staff>.my-input>.my-input-list:visible').length > 0 || $('.my-options-staff>.my-input>.my-search-list:visible').length > 0 ?
                                $('.my-options-staff>.my-input>.inputs.not-empty') : '';
            if(tarParent == undefined) return;
            if(!$(target).hasClass("layui-table-tips-main") && !$(target).hasClass("layui-icon-close")) { // 解决组织架构中表格里的文字悬浮框没有关闭的问题
                $(".layui-icon-close").click();
            }
            //给select组件点击其他地方收起
            // if(tarParent.className.indexOf('my-select-btn')===-1){
            if(targetClass.indexOf('my-select-btn')==-1&&targetClass.indexOf('my-select-list')==-1){
                $('.my-select-list').stop().hide(0);
                $('.my-select-list-child').stop().hide(0).siblings('.my-select-list').find('li.on').removeClass("on");
                $('.my-select').removeClass('on');
            }
            if(targetClass.indexOf('my-input-list')==-1&&targetClass.indexOf('my-search-list')==-1){//全局开启关闭部门单选组件下拉
                $('.my-input-list').stop().slideUp(100);
                $('.my-search-list').stop().slideUp(100);
                if(!staffInput) return;
                if(!myFun.utils.IsNotEmpty(staffInput.val())){
                    staffInput.parent('.my-input').addClass('error');
                    blurChange(self, '必填项不能为空');
                }else{
                    staffInput.parent('.my-input').removeClass('error');
                }
            }
            // }
            // console.log(target)
            //添加点击选项 关闭导航下拉
            if(targetClass.indexOf('lev2-item') == -1){
                $('.navbar-lev2').slideUp(100);
            }
            //睁眼闭眼
            // if(targetClass.indexOf('iconzhengyan') !== -1){
            //     var th = $(target);
            //     var text = th.siblings('.eye-text').addClass('onon');
            //     // console.log(th.hide().siblings())
            //     th.hide().siblings('.iconbiyan').show();
            //     text.attr('eye-text',text.text())
            //     text.text('***********')
            // }
            // if(targetClass.indexOf('iconbiyan') !== -1){
            //     var th = $(target);
            //     var text = th.siblings('.eye-text').removeClass('onon');
            //     th.hide().siblings('.iconzhengyan').show();
            //     // console.log(th.hide().siblings('.iconzhengyan'))
            //     text.text(text.attr('eye-text'))
            // }
        });
    },
    //绑定layerData组件时间选择事件
    bindBtnsName:function(input){
        if(!input){ throw ('参数错误'); return ;}
        // 判断是否为双日期时间控件
        if($('.layui-laydate').find('.laydate-footer-btns1').length > 0 &&
            $('.layui-laydate').find('.laydate-btns-time').length > 0) {
                $('.layui-laydate').find('.laydate-footer-btns1').addClass('exist-btn-time');
        }
        var myBtns = $('.layui-laydate').find('.laydate-btns');//btns
        var that = this;
        var startDate = {}, endDate = {};
        myBtns.on('click',function(e){
            e = e || window.event;
            var timer = myFun.judgeTimer(myFun.getDataset(e.target).type,that.format,that.range),
                startTimer = (new Date(timer.split('-')[0])),
                endTimer = (new Date(timer.split('-')[1]));
            // console.log(myFun.getDataset (e.target).type);
            input.value = timer;
            that.dateTime = startTimer;
            startDate = {
                year: startTimer.getFullYear(),
                month: startTimer.getMonth() + 1,
                date: startTimer.getDate(),
                hours: startTimer.getHours(),
                minutes: startTimer.getMinutes(),
                second: startTimer.getSeconds()
            };
            endDate = {
                year: endTimer.getFullYear(),
                month: endTimer.getMonth() + 1,
                date: endTimer.getDate(),
                hours: endTimer.getHours(),
                minutes: endTimer.getMinutes(),
                second: endTimer.getSeconds()
            }

            that.done(timer, startDate, endDate);
            $('.layui-laydate').remove();
        })
    },
    //判断时间 type,比如 age7七天以前，now现在，near7近七天
    judgeTimer:function(type, format, range){
        range = ` ${range} `
        var nowTime = new Date().getTime()
            ,nowStr = myFun.utils.dateFormat(nowTime,format)
            ,dateRange='';
        if(type === 'now'){
            return nowStr + range + nowStr;
        }
        //不是现在时间
        var pattern = new RegExp("[0-9]+")
            ,strIndex = type.match(pattern).index;

        var num = type.slice(strIndex);
        var myType = type.slice(0,strIndex);
        nowTime -= num * 86400 * 1000;
        dateRange = myType === 'near' ?  myFun.utils.dateFormat(nowTime,format) + range + nowStr
            : myFun.utils.dateFormat(nowTime,format) + range + myFun.utils.dateFormat(nowTime,format);
        return dateRange;
        // if(myType === 'near'){//近几天
        //     nowTime -= num * 86400 * 1000;
        //     return myFun.getTimeFormat(nowTime) + ' - ' + nowStr;
        // }
        // if(myType === 'ago'){//多少天前
        //     nowTime -= num * 86400 * 1000;
        //     return myFun.getTimeFormat(nowTime) + ' - ' + myFun.getTimeFormat(nowTime);
        // }
    },
    //时间戳转换时间
    getTimeFormat:function(nowTime){
        nowTime = new Date(nowTime)
        return myFun.dealTimer(nowTime.getFullYear()) + '/' + myFun.dealTimer(nowTime.getMonth()+1) + '/' + myFun.dealTimer(nowTime.getDate())
    },
    //小于10前面加零
    dealTimer:function(text){
        return text<10?'0'+text:text;
    },
    //获取dataset的值，具有兼容性
    getDataset:function(ele){
        if(ele.dataset){
            return ele.dataset;
        }else{
            var attrs = ele.attributes,//元素的属性集合
                dataset = {},
                name,
                matchStr;

            for(var i = 0;i<attrs.length;i++){
                //是否是data- 开头
                matchStr = attrs[i].name.match(/^data-(.+)/);
                if(matchStr){
                    //data-auto-play 转成驼峰写法 autoPlay
                    name = matchStr[1].replace(/-([\da-z])/gi,function(all,letter){
                        return letter.toUpperCase();
                    });
                    dataset[name] = attrs[i].value;
                }
            }
            return dataset;
        }
    },
    //my-options组件js方法
    /*
        textClick 部门点击事件 ，
        affirm转移部门点击的确认时间，
        searchClick 点击搜索结果选项  ,
        onDarg是否开启拖拽,默认开启,
        target1目标元素，
        sectionData绑定数据
        onfold是否展开，默认不展开
    */
   options: function(textClick,affirm,searchClick,onDarg,target1,sectionData,onfold){
        target1 = target1 || 'body';
        console.log($(target1))
        //添加模板
        if($('#tmp').length === 0){
            $('body').append('<script type="text/x-jsrender" id="tmp">'
            + '<template id="transfer-section">'
            +     '<div class="transfer-section apply-layer">'
            +        ' <div class="layer-line1">'
            +             '<p>你当前的上级部门为【{{:parent}}】，确定转移到【{{:to}}】吗？</p>'
            +   '</div>'
            +    '<div class="btn-group">'
            +         '<div class="my-btn my-btn-cancel">取消</div>'
            +          '<div class="my-btn my-btn-green my-btn-result">确定</div>'
            +       '</div>'
            +    '</div>'
            + '</template>'
            +'</script>');
        }
        if(onDarg == undefined) onDarg = true;
        // console.log(sectionData)
        //开启options功能
        var self = this;
        //拖拽，点击收起展开，搜索功能
        var obj =  {
            targetEle: null,  //保存原本元素
            onNum: [],
            searchData: [],
            sectionData: sectionData,
            btnsIconOnData: [],
            //设置点击收起或者展开
            bindMySelect:function(target){
                //设置点击收起或者展开
                target = target || 'body';
                $(target).find('.option-title').on('click','.btns-icon',jieliu(function(){
                    var th  = $(this).parent();
                    if(th.siblings('.option-content').find(".div-item").length === 0) return;

                    var a = th.siblings('.option-content');
                    a.css('display') == 'none'?th.addClass('on'):th.removeClass('on');
                    a.stop(true,true).slideToggle(250,function(){
                        a.css('display') == 'none'?th.removeClass('on'):th.addClass('on');
                    });
                    var myDepartCode = th.find('.text').attr('DepartCode') || th.attr('DepartCode');
                    obj.search2(obj.sectionData,myDepartCode,$(this).hasClass('down'));
                    // obj.useBtnsIconOn($(target).find('.my-options:first'));
                },300));
                //设置拖拽按钮
                onDarg && ($(target).find('.div-item:not(.option-title)').mousedown(function(e){
                    e.stopPropagation();
                    $(this).attr("draggable","true");
                }))
                //设置点击span.text 选中样式
                $(target).find('.my-option-content').on('click','.text,.text-label',function(){
                    $('.my-option-content').find('.text').removeClass('on').siblings('.text-label').removeClass('on');
                    // console.log($(this).siblings('.text-label'))
                    $(this).siblings('.text-label').addClass('on')
                    $(this).siblings('.text').addClass('on')
                    $(this).addClass('on')
                    // obj.search1()
                    if(sectionData !== undefined){
                        var myDepartCode = $(this).attr('DepartCode') || $(this).parent().attr('DepartCode');
                        obj.searchStart(sectionData,myDepartCode,sectionData[0]);
                    }
                    // console.log(obj.searchData)
                    textClick && textClick.call(this,obj.searchData)
                }).on('dblclick','.text,.text-label',function(){
                    if($('#departmentDetails').length!==0)
                        myFun.layer.opens("#departmentDetails", "部门详情", "normal", function() {});
                })
                obj.setSearchData(sectionData);
            },
            //添加拖拽开始事件，保存当前拖拽的值
            bingLiDrop:function(){
                var $li = document.getElementsByClassName('div-item')
                ,$select = document.getElementsByClassName('my-options')
                ,th = this;

                for(var i= 0,length = $li.length;i<length;i++){
                    //为每个li添加ondragstart事件
                    $li[i].ondragstart = function(e) {
                        //存储innerHTML
                        th.targetEle = e.target;
                        e.dataTransfer.setData('text/plain', '<'+e.target.nodeName.toLowerCase()+'>' + e.target.innerHTML);
                    }
                    $li[i].ondragover = function(e){
                    //阻止默认行为
                        e.preventDefault();
                    }
                }
                for(var i= 0,length = $select.length;i<length;i++){
                    $select[i].ondrop = th.bingDrop;
                    $select[i].ondragover = function(e){
                    //阻止默认行为
                        e.preventDefault();
                    }
                }
            },
            //获取挡圈
            bingDrop:function(e){
            // if(e.target.nodeName.toLowerCase() === 'span'||e.target.nodeName.toLowerCase() === 'div'){
            //   return false;
            // }
                var currentTarget = e.currentTarget;
                var $currentTarget = $(currentTarget);
                var $targetEle = $(obj.targetEle);
                e.stopPropagation();//防止触发父子drop
                // console.log($currentTarget)
                // myFun.layer.opens('','转移部门','')
                $('.div-item').attr("draggable",false);//删除元素可拖动性
                //不能再本部门移动
                if($currentTarget.children('.option-content').is($targetEle.parent()[0])) return ;

                //获取数据 obj.targetEle需要移动的部门，currentTarget需要移动到的部门
                var html = e.dataTransfer.getData('text/plain');

                var test1 = $currentTarget;
                var test2 = $targetEle;
                console.log(test2)
                var flag = true;
                test1.children().each(function(index,item){
                    if($(item).html() == test2.html()){myFun.layer.msg('不能把部门从大移到小',0);flag=false;  return false;}
                })
                while(!test1.hasClass('my-option-content')){
                    test1 = test1.parent();
                    if(test1.html() == test2.html()) {myFun.layer.msg('不能把部门从大移到小',0);flag=false;  return false;}
                    test1.siblings().each(function(index,item){
                        if($(item).html() == test1.html())  {myFun.layer.msg('不能把部门从大移到小',0);flag=false;  return false;}
                    })
                }
                if(!flag) return false;

                var parent = $targetEle.parent().siblings('.option-title').find('.text').html();
                //删除之前的
                $('#transfer-section').remove();
                //弹窗是否转移部门
                console.log($currentTarget.children('.option-content').find('.option-title:first .text').html())
                var currentText = $currentTarget.find('.option-title:first .text').html() || $currentTarget.children('.option-content').find('.option-title:first .text').html();
                //判断不能一级部门不能移动到所有部门
                if(currentText == '所有部门' && test2.parent().siblings('.option-title').find('.text').html() == '所有部门'){return false;}
                var data = {
                    "to": currentText,
                    'parent': parent
                };
                var html1 = $("#tmp").render(data);
                $("body").append(html1);
                myFun.layer.opens('#transfer-section','转移部门','small',function(target,index){
                    //确定点击事件
                    $(target).on('click','.my-btn-result',function(){
                        layer.close(index);
                        if(html.indexOf('<li>')==0){
                            var _li = document.createElement('li');     //创建新元素
                            // _li.id = (new Date()).getTime();            //生成唯一ID
                            _li.draggable = true;
                            _li.className = 'div-item';
                            _li.innerHTML = html.substring(4);        //字符串截取
                            // console.log(currentTarget)
                            $targetEle.remove();  //删除原本元素

                            if($currentTarget.children(".option-content").find(".div-item").length !== 0)
                                $currentTarget.children(".option-content").children(":last").after(_li);
                            else {
                                $currentTarget.children(".option-content").html(_li)
                            }
                            // _li.ondrop = obj.bingDrop;
                            //添加ondragstart

                            $(_li).on('mousedown',function(e){
                                e.stopPropagation();
                                $(this).attr("draggable","true");
                            })
                            _li.ondragstart = function(e){
                                obj.targetEle = e.target;
                                e.dataTransfer.setData('text/plain', '<'+e.target.nodeName.toLowerCase()+'>' + e.target.innerHTML);
                            }
                            obj.bindMySelect(_li);
                            obj.bingLiDrop(_li);

                            if(sectionData !== undefined){
                                var myDepartCode = [$currentTarget.find('.text').attr('DepartCode'),$(_li).find('.text:first').attr('DepartCode')];
                                obj.searchStart(sectionData,myDepartCode,sectionData[0]);
                                // console.log(obj.searchData)
                            }
                            // textClick && textClick.call(this,obj.searchData)
                            console.log(obj.searchData)
                            affirm && affirm.call(null,obj.searchData);;
                            return;
                        }
                    })
                    // $(target).on('click','.my-btn-cancel',function(){

                    // })
                })
            },
            //设置拖拽部门的时候能移动滚动条
            ready:function(){
                var myOptions = $(target1).find('.my-options:first'), //第一个my-options
                    myOptionsHeight = myOptions.parent().height(),
                    ONETOP = myOptionsHeight / 5,// 每次移动的距离值
                    myOptionsTop = getH(myOptions[0]),// 距离顶部的距离值
                    myOptionsBox = myOptions.parent();
                var dragoverFun = jieliu(function(e){
                    e = window.event;
                    e.preventDefault();
                    var clientY = e.clientY - myOptionsTop,
                        speed = parseFloat((Math.abs(clientY - (myOptionsHeight / 2))) / myOptionsHeight,2),
                        scrollTop = myOptionsBox.scrollTop();
                    //距离中间就不动
                    if(speed < 0.05) return;
                    if(clientY >= (myOptionsHeight / 2)){ //向下移动
                        console.log('向下移动')
                        myOptionsBox.animate({scrollTop: scrollTop + speed * ONETOP},200)
                    }else{//向上移动
                        console.log('向上移动')
                        myOptionsBox.animate({scrollTop: scrollTop - speed * ONETOP},200)
                    }
                    // console.log(myOptions)
                },200)
                myOptionsBox.on('dragover',null,dragoverFun)
                // console.log(myOptions)
                // console.log( $('.option-title').siblings('.option-content'))
                // $('.option-title').siblings('.option-content').children().css("text-indent","2em")
            },
            //添加onNum字段 onNum为选中的数据
            setOnNum:function(target){
                this.onNum = [];
                this.getON(target);
            },
            //给对象添加btnIconsOn键名，作用是记录子部门是否收起
            setSearchData:function(sectionData){
                if(sectionData!== undefined){
                    $.each(sectionData,function(index,item){
                        item['btnsIconOn'] = onfold?false:true;
                        if(sectionData[index].DepartmentListZtree.length != 0){
                            obj.setSearchData(sectionData[index].DepartmentListZtree);
                        }
                    });
                }else return false;
            },
            //解释同setOnNum事件
            getON:function(target){
                var self = this;
                if(target.children().length == 0 && !target.hasClass('option-title')) return;
                target.children().each(function(index,item){
                    if($(item).hasClass('my-checks') && $(item).hasClass('on')){
                        // if($(item).parent().parent().parent().parent().siblings('.option-title').find('.text'))
                        self.onNum.push({text:$(item).siblings('.text').text(),parent:$(item).parent().parent().parent().parent().siblings('.option-title').find('.text').text()})
                    }
                    self.getON($(item))
                })
            },
            //搜索功能
            searchStart:function(ele,DepartCode,parent){
                obj.searchData = [];
                // console.log(typeof DepartCode)
                if(typeof DepartCode == 'object'){
                    for (const key in DepartCode) {
                        if (DepartCode.hasOwnProperty(key)) {
                            obj.search1(ele,DepartCode[key],parent);
                        }
                    }
                }else obj.search1(ele,DepartCode,parent);
                // console.log(obj.searchData);

            },
            //搜索功能
            search1:function(ele,DepartCode,parent){
                $.each(ele,function(index,item){
                    if(ele[index].DepartCode == DepartCode){
                        // console.log(ele[index])
                        obj.searchData.push({
                            DepartName:ele[index].DepartName,
                            DepartCode:ele[index].DepartCode,
                            DepartNo:ele[index].DepartNo,
                            PDepartName:parent.DepartName,
                            PDepartCode:parent.DepartCode,
                            PDepartNo:parent.DepartNo
                        })
                    }
                    // console.log(ele[index])
                    if(ele[index].DepartmentListZtree.length != 0){
                        obj.search1(ele[index].DepartmentListZtree,DepartCode,ele[index]);
                    }
                })
            },
            //点击btn-icons设置数据的btnIconsOn的值
            search2:function(ele,DepartCode,flag){
                $.each(ele,function(index,item){
                    if(ele[index].DepartCode == DepartCode){
                        // console.log(ele[index])
                        item.btnsIconOn = flag;
                        return false;
                    }
                    // console.log(ele[index])
                    if(ele[index].DepartmentListZtree.length != 0){
                        obj.search2(ele[index].DepartmentListZtree,DepartCode,flag);
                    }
                })
            },
            // search3:function(data1,data2){
            //     $.each(data1,function(index,item){
            //         data2[index] = {};
            //         data2[index].DepartmentListZtree = {};
            //         if(data1[index].btnsIconOn !== undefined){
            //             data2[index].btnsIconOn = data1[index].btnsIconOn;
            //         }
            //         // console.log(data1[index])
            //         if(data1[index].DepartmentListZtree.length != 0){
            //             obj.search3(data1[index].DepartmentListZtree,data2[index].DepartmentListZtree);
            //         }
            //     })
            // },
            // setBtnsIconOnData:function(){
            //     obj.search3(obj.sectionData,obj.btnsIconOnData)
            // },
            // useBtnsIconOn:function(ele){
            //     ele.children().each(function(index,item){
            //         if($(item).children().length !== 0 || $(item).hasClass('option-title')){
            //             if($(item).hasClass('option-content')){
            //                 console.log($(item),$(item).parent().parent().index())
            //             }
            //             // if($(item).children('.text').text().indexOf(text) !== -1) {
            //             //     var parent = null;
            //             //     $(item).children('.text').attr('data-result',result_num++);
            //             //     if($(item).parent().hasClass('my-options')){
            //             //         parent = $(item).parent().parent().parent().siblings('.option-title').find('.text').text()
            //             //     }else{
            //             //         parent = $(item).parent().siblings('.option-title').find('.text').text()
            //             //     }
            //             //     myInputList.data.push({
            //             //         local: $(item).children('.text').text().replace(text,"<span class='green'>"+text+"</span>"),
            //             //         parent: parent,
            //             //         top: $(item).offset().top - $('.my-option-content').offset().top - 30
            //             //     })
            //             // }
            //             obj.useBtnsIconOn($(item));
            //         }else{
            //             return false;
            //         }
            //     })
            // },
            init:function(){
                if(onDarg){
                    this.bindMySelect(target1);
                    this.bingLiDrop();
                    this.ready();
                }else{
                    this.bindMySelect(target1);
                }
            },
            //收起二级菜单
            fold:function(target){
                var second = $(target+' .my-option-content>.my-options .my-options:first').find('>.option-content >.div-item >.my-options');
                second.find('.option-title').removeClass('on');
                second.find('.option-content').slideUp(0);
            }
        }

        //处理一下target
        var result_num = 0; //记录搜索结果的排序
        //部门控件上搜索的方法处理
        var myInputList = {
            init:function(){
                var self = this;
                    self.data = [];
                //inputs搜索函数
                var myKeyUp = debounce(function(e){
                    //重置搜索结果的记录值
                    if(result_num !== 0) $('.my-option-content .text').attr('data-result','');
                    result_num = 0;
                    // console.log($(this).val())
                    if($(this).val() == ''){
                        $(this).siblings('.my-input-list').hide(0)
                        return false;
                    }
                    $(this).siblings('.my-input-list').attr("style","").show(0);

                    //判断是否是字符串，是的话进行字符串查找
                    if((/^[a-zA-Z]*$/.test($(this).val()))){
                        self.searchStr(sectionData,$(this).val(),sectionData)
                        console.log(myInputList.data)
                    }else{
                        self.search($('.my-option-content'),$(this).val());
                    }
                    console.log(self.data)
                    // console.log(11111)
                    // console.log(self.data)
                    //循环遍历数据
                    var myStr = '';
                    if(self.data.length === 0){
                        myStr = '<li class="big">搜索无结果</li>'
                    }
                    $.each(self.data,function(index,item){
                        myStr += '<li data-top="'+item.top+'">' + item.parent + " - " + item.local + '</li>';
                    })
                    $(this).siblings('.my-input-list').html(myStr);
                    self.data = [];
                }, 666, false);
                onfold && self.fold(target1); //如果fold为true则收起二级菜单
                //inputs输入框事件
                $(target1).find('.my-input-options').on('keyup','.inputs',myKeyUp)
                .on('focus','.inputs',function(){
                    console.log("")
                    var self = this;
                    // $(self).siblings('.my-input-list').stop().show();
                    $(self).val() == ''?$(self).siblings('.my-input-list').hide(0):$(self).siblings('.my-input-list').show(0);
                })
                .on('blur','.inputs',function(){
                    var self = this;
                    setTimeout(function() {
                        $(self).siblings('.my-input-list').slideUp();
                    }, 150);
                })
                .on('click','.inputs',function(e){
                    e = e || window.event;
                    e.stopPropagation();
                }).find('.my-input-list').on('click','li',function(){//搜索出来结果
                    // console.log(1)
                    if(!$(this).hasClass('big')){
                        var targetText = $('.my-option-content').find('.text[data-result='+$(this).index()+']');
                        $('.my-option-content').scrollTop(myFun.getDataset($(this)[0]).top);
                        $('.my-option-content .text').removeClass('on')
                        self.unfold(targetText)
                        targetText.addClass('on')

                        // console.log(targetText)
                        if(onDarg && sectionData !== undefined){
                            var myDepartCode = targetText.attr('DepartCode') || targetText.parent().attr('DepartCode');
                            obj.searchStart(sectionData,myDepartCode,sectionData[0]);
                        }
                        searchClick && searchClick.call(this,obj.searchData);
                    }
                })

            },
            //检索开始，检索DOM ，保存自己，父级以及距离上偏移值
            search:function(ele,text){
                ele.children().each(function(index,item){
                    if($(item).children().length !== 0 || $(item).hasClass('option-title')){
                        if($(item).children('.text').text().indexOf(text) !== -1) {
                            console.log($(item).children('.text').text())
                            var parent = null;
                            $(item).children('.text').attr('data-result',result_num++);
                            if($(item).parent().hasClass('my-options')){
                                // parent = $(item).parent().parent().parent().siblings('.option-title').find('.text').text()
                                parent = $(item).parents(".my-option-content>.my-options>.option-content>.div-item>.my-options").children('.option-title').find('.text').text()
                            }else{
                                // parent = $(item).parent().siblings('.option-title').find('.text').text()
                                parent = $(item).parents(".my-option-content>.my-options>.option-content>.div-item>.my-options").children('.option-title').find('.text').text()
                            }
                            myInputList.data.push({
                                local: $(item).children('.text').text().replace(text,"<span class='green'>"+text+"</span>"),
                                parent: parent,
                                top: $(item).offset().top - $('.my-option-content').offset().top - 30
                            })
                        }
                        myInputList.search($(item),text);
                    }else{
                        return false;
                    }
                })
            },
            //检索字符串
            searchStr:function(ele,text,parent){
                $.each(ele,function(index,item){
                    var namePyIndex = item.DepartNamePy.indexOf(text);
                    if(namePyIndex !== -1){
                        // console.log(ele[index].DepartName+']')
                    var arr = item.DepartName.split("");
                    arr.splice(namePyIndex + text.length,0 ,'<span/>');
                    arr.splice(namePyIndex,0 ,'<span class="green">');
                    // console.log($(target1).find('[DepartCode='+ele[index].DepartCode+']'))
                    myInputList.data.push({
                            // local: $(item).children('.text').text().replace(text,"<span class='green'>"+text+"</span>"),
                            local: arr.join(""),
                            parent: parent.DepartName,
                            top: $(target1).find('[DepartCode='+ele[index].DepartCode+']').offset().top - $('.my-option-content').offset().top - 30
                        })
                    }
                    if(ele[index].DepartmentListZtree.length != 0){
                        myInputList.searchStr(ele[index].DepartmentListZtree,text,ele[index],ele[index]);
                    }
                })
            },
            //展开
            unfold:function(ele){
                var eleParent = ele.parent();
                // console.log(eleParent)
                if(eleParent.hasClass('my-option-content')){
                    return false;
                }else{
                    if(eleParent.css('display') == 'none' && eleParent.hasClass('option-content')){
                        eleParent.slideDown(0);
                        eleParent.siblings('.option-title').addClass('on')
                    }
                    this.unfold(eleParent);
                }
            },
            //收起二级菜单
            fold:function(target){
                var second = $(target+' .my-option-content>.my-options .my-options:first').find('>.option-content >.div-item >.my-options');
                second.find('.option-title').removeClass('on');
                second.find('.option-content').slideUp(0);
            }
        }
        obj.init();
        myInputList.init();
        return obj;
    },
    //表单滚动加载
    // 1.绑定表格元素 2.请求函数 3.开始执行的页数 4.是否调用函数就执行一次接口
    scrollLoad:function(target,request,startPage,firstExe){
        if($(target).length <= 0 ) return;
        target = $(target).hasClass('scroll-box')?$(target):$(target).children('.scroll-box');
        target.attr("data-index", Math.ceil(Math.random() * 10));
        var self = this,
            scrollOn = true,//只执行一次请求
            content = target.find('.table-content'),
            item = content.find('.table-item'),
            lastItemHeight = target.find('.table-item:last-child').outerHeight(),
            textLength = content.siblings('.table-title').children().length;//保存当前表单的列数

        // console.log(content.css("height"));
        //设置表格里面高度
        content.height() >= item.outerHeight() * item.length
                            ? content.height(content.height()- lastItemHeight)
                            : content.height();
        self.pageNum = startPage || 0;
        self.getLoadTop = function(){
            // console.log(target.find('.table-item:last-child')[0].offsetTop);
            self.loadTop = target.css("overflow") != "overflow-y"
                            ? target.find('.table-item:last-child')[0].offsetTop - 13
                            : target.find('.table-item:last-child')[0].offsetTop;
        }
        self.append = function(data){
            var talbeItem = data;
            if(data == undefined) {//停止滚动加载
                target.find('.table-loading').addClass('table-loading-finish');
                return;
            }else{
                target.find('.table-loading').removeClass('table-loading-finish');
            }
            target.find('.table-loading').parent().before(talbeItem);
            self.pageNum++;
            scrollOn = true;
            self.getLoadTop();
        }
        self.init = function(){
            self.getLoadTop();
            self.CONTENT_HEIGHT = content.height();
            content.on('scroll',function(e){
                var top = $(this).scrollTop() - self.loadTop + self.CONTENT_HEIGHT;
                if(top>=0 && scrollOn){
                    // console.log('`````````````````````````')
                    scrollOn = false;
                    request && request.call(self,self.pageNum);
                }
            })
            if(self.CONTENT_HEIGHT>self.loadTop) target.find('.table-loading').hide().parent().hide();
            firstExe && content.scroll();
        }
        self.init();
    },
    ELementScroll: function(){
        //吸顶效果
        var self = this;
        this.init = function(){
            $('.ele-scroll').each(function(index,item){
                var left = item.getBoundingClientRect().left
                  , right = document.body.clientWidth - item.clientWidth - left;
                $(item).css({'left': left + 'px','right': right + 'px'});
                self.ELementScroll11(item, function (target) {
                    $(target).hasClass('table-header') && $(target).next().css('marginTop',$(target).height())
                    $(target).hasClass('ele-scroll-fixed') ? '' : $(target).addClass('ele-scroll-fixed');
                    // console.log($(target))
                }, function (target) {
                    $(target).next().css('marginTop',0)
                    $(target).hasClass('ele-scroll-fixed') ? $(target).removeClass('ele-scroll-fixed') : '';
                }, false); //第一个参数为滚动到触发的函数，第二个是没有滚动到触发函数，第三个高度比较的时候是否加上页面宽度
            })

        }
        // 判断方法
        //第四个参数是按顶部计算（吸顶）false ,true为滚动加载，以最底部计算
        self.ELementScroll11 = function (ele, fn1, fn2, flag) { //第一个参数为滚动到触发的函数，第二个是没有滚动到触发函数，第三个高度比较的时候是否加上页面宽度
            var eleHei;
            // fn1 = jieliu(fn1),
            // fn2 = jieliu(fn2);


            if (!flag) eleHei = flag ? getH(ele) - $(window).height() : getH(ele);
            $(window).scroll(jieliu(function (e) {
                if (flag) eleHei = flag ? getH(ele) - $(window).height() : getH(ele);
                if (eleHei - 60 > $(this).scrollTop()) {
                    fn2(ele);
                } else {
                    fn1(ele);
                }
            }, 150));
        }
        self.init();
        return self;
    },
    //
    errorBespread: function(element) {
        var element = $(".container-single") || element, clientTop = 0;
        element.each(function(index, elem) {
            clientTop = getH(elem, true);
            $(elem).css('min-height', `calc(100vh - ${ clientTop }px)`);
        })
    },
    //房源列表
    houseList:{
        houseType_text: [],
        //添加标签
        addLabel:function(type,text){
            // console.log(type)
            if(type.indexOf('houseState')!=-1){
                var num = /\d+/g.exec(type)[0];
                text = this.houseType_text[num-1] + '：' + text;
            }
            var selectsList = $('.selects-result-list li');
            for(var i=0;i<selectsList.length;i++){
                if(type == $(selectsList[i]).data('type')){
                    // console.log(selectsList[i])
                    $(selectsList[i]).html(text +'<span class="iconfont">×</span>')
                    return ;
                }
            }
            $('.empty-result').before('<li data-type="'+type+'">'+text+'<span class="iconfont">×</span></li>');
        },
        //删除标签
        removeLabel:function(target,all){
            if(all){//删除全部
                $('.selects-result-list li').each(function(i,item){
                    // console.log(item.dataset.type);
                    if($(item).data('type')){
                        myFun.houseList.resetLabel($(item).data('type'));
                    }
                })
                $('.selects-result-list li').not(target).remove();
                return;
            }
            if(target==undefined) return false;
            $(target).remove();
            myFun.houseList.resetLabel($(target).data('type'));
        },
        // 重置标签
        resetLabel:function(type){
            var target = null,
                index = null;
            if(type==undefined) return false;
            if(type.indexOf('houseState')!=-1){
                index = type.slice(type.length-1)-1;
                target = $('.selects-selected').find('[data-type='+type+']');
                if(target.hasClass('my-select')) { // 删除下拉列表
                    target.find('li').removeClass('on');
                    target.find('.btn-text').html(this.houseType_text[index]);
                }
                if(target.hasClass('my-timer')) { // 删除时间类型
                    target.find('.my-select-btn').val('');
                }
            }else{
                target = $('.selects-div').eq(addType.indexOf(type));
                target.find('li').removeClass('on');
                target.find('.input-text').val('');
            }
        }
    },
    section_area:{
        //部门多选下拉效果，商圈部门多选
        area:function(target,Data,flag,onfold){
            var self = this;
            flag = flag || false;//判断是否使用部门多选还是商圈部门多选
            var indexData = 0;//计数的
            //Data数据深拷贝以及修改键名'on'
            self.currentData = [];//保存当前选择的
            self.searchData = [];//保存搜索的
            self.targetData = [];//保存目标的
            self.areaOptions = null;//保存options对象
            var parentText = self.data;//记录搜索结果的父子集
            self.inputs = function(target1){
                if($('#Tmpl-area').length === 0){
                    $('body').append('<script type="text/x-jsrender" id="Tmpl-area"><div class="my-option-content"><div  class="my-options">{{!-- 这层div是占位子的 --}}<div class="div-item option-title"></div><ul class="option-content"><li class="div-item"><div class="my-options">{{!-- 这层title才是最顶部的 --}}<div class="div-item option-title on"><span class="text" >{{:DepartName}}</span><div class="my-checks fl-le"><i class="iconfont iconUtubiao-12"></i></div><i class="fl-le btns-icon iconfont icon1-unfold up"></i><i class="fl-le btns-icon iconfont icon1-fold down"></i></div><ul class="option-content">{{for DepartmentListZtree tmpl="#tmpChild" /}}</ul></div></li></ul></div></div></script>');
                }
                // 防抖函数
                // var debounce1 = debounce(function(text){
                //     // console.log(self.currentData)
                //     self.searchData = [];//清空选择的内容
                //     if(text == ''){
                //         self.useData();
                //         self.areaOptions = new myFun.options(null,null,null,false,target1);
                //         return;
                //     }
                //     flag?self.search1(self.data,text,self.data[0]):self.search2(self.data,text);
                //     // console.log(self.searchData)
                //     $(target1).find('.my-list-content').html(self.setSearchData(self.searchData))
                //     myFun.componentFun.checks(target1);
                //     // console.log(self.setSearchData(self.data));
                // },600, false);
                $(target1).find('.search').on('keyup',null,debounce(function(e){
                    // console.log(self.currentData)
                    self.searchData = [];//清空选择的内容
                    if($(this).val() == ''){
                        self.useData();
                        self.areaOptions = new myFun.options(null,null,null,false,target1);
                        return;
                    }
                    flag?self.search1(self.data,$(this).val(),self.data[0]):self.search2(self.data,$(this).val());
                    // console.log(self.searchData)
                    $(target1).find('.my-list-content').html(self.setSearchData(self.searchData))
                    myFun.componentFun.checks(target1);
                    // console.log(self.setSearchData(self.data));
                } ,600 ,false))
            }
            //初始页面
            self.useData = function(){
                var self = this;
                var html2 = self.setContentData(self.data);
                $(target).find('.my-list-content').html(html2);
                myFun.componentFun.checks(target);
            }
            //循环数组的
            self.mapData = function(goal,index2,parent,on){ //index每个值都不同，传进来找到这对象以及上级的对象 ，parent为父级对象, on ： true为开启，false为关闭
                // console.log(goal)
                $.each(goal,function(index,item){
                    if(goal[index].index == index2){
                        if(on === undefined) on = true;
                        goal[index].on = on;
                        // console.log(goal[index])
                        self.targetData = [goal[index],parent];
                        return;
                    }else{
                        if(goal[index].DepartmentListZtree.length != 0){
                            return self.mapData(goal[index].DepartmentListZtree,index2,goal[index],on);
                        }
                    }
                })
            }//返回两个对象，1. 自己的对象，2. 父级的对象
            //搜索部门的
            self.search1 = function(ele,text,parent){
                $.each(ele,function(index,item){
                    if(ele[index].DepartName.indexOf(text) !== -1){
                        console.log(ele[index])
                        self.searchData.push({text:ele[index].DepartName.replace(text,"<span class='green'>"+text+"</span>"),on:ele[index].on,DepartCode:ele[index].DepartCode,parent:parent.DepartName,index:ele[index].index})
                    }
                    // console.log(ele[index])
                    if(ele[index].DepartmentListZtree.length != 0){
                        self.search1(ele[index].DepartmentListZtree,text,ele[index]);
                    }
                })
            }
            //搜索商圈的
            self.search2 = function(ele,text){
                for(var i in ele){
                    if(typeof ele[i]=="object"){
                        self.search2(ele[i],text)
                    }
                    if(i == 'DepartName'){
                        if(ele[i].indexOf(text) !== -1){
                            // console.log(ele)
                            self.searchData.push({text:ele[i].replace(text,"<span class='green'>"+text+"</span>"),on:ele['on'],DepartCode:ele['DepartCode'],index:ele['index']})
                        }
                    }
                }
            }
            self.init = function(target1,Data){
                // // 添加部门树，商圈树
                self.useData();
                onfold && self.fold(target1);
                //阻止公共select1组件效果
                $(target1).find('.my-select-list').on('click',null,function(e){
                    e = e || window.event;
                    e.stopPropagation();
                })
                $(target1).find('.my-list-content').on('click',null,function(e){
                    e = e || window.event;
                    if(e.target.className.indexOf('text') !== -1 && e.target.nodeName.toLowerCase() == 'span'){
                        $(e.target).siblings('.my-checks').toggleClass('on');
                        //关闭或开启子代选中
                        $(e.target).removeClass('on');
                        // console.log($(e.target).parent().find('.my-checks').hasClass('on'))
                        if($(e.target).parent().find('.my-checks').hasClass('on')){ //点击文字添加
                            self.mapData(self.data,$(e.target).parent().attr('index'),self.data[0],true);
                            self.currentData.push({text:self.targetData[0].DepartName,parent: self.targetData[1].DepartName,DepartCode:self.targetData[0].DepartCode,index:self.targetData[0].index});
                            // console.log(self.currentData)
                        }else{//点击文字删除
                            self.mapData(self.data,$(e.target).parent().attr('index'),self.data[0],false);
                            // console.log(self.targetData)
                            var $parent = self.targetData[1].DepartName;
                            var $son = $(e.target).text();
                            if($(e.target).text().indexOf('-') !== -1){
                                var $parent = $(e.target).text().split('-')[1].trim();
                                var $son = $(e.target).text().split('-')[0].trim();
                            }
                            self.currentData = self.currentData.filter(function(k){
                                return self.targetData[0].index != k.index;
                            })
                        }
                    }
                    //判断是否添加还是删除
                    if($(e.target).parent().hasClass('my-checks')||$(e.target).hasClass('my-checks')){
                        var divItem = $(e.target).parent().hasClass('my-checks')? $(e.target).parent().parent(): $(e.target).parent();
                        // console.log(divItem)
                        if(divItem.find('.my-checks').hasClass('on')){ //点击图标添加
                            self.mapData(self.data,divItem.attr('index'),self.data[0],true);
                            self.currentData.push({text:self.targetData[0].DepartName,parent: self.targetData[1].DepartName,DepartCode:self.targetData[0].DepartCode,index:self.targetData[0].index})
                        }else{//点击图标删除
                            self.mapData(self.data,divItem.attr('index'),self.data[0],false);
                            var $parent = self.targetData[1].DepartName;
                            var $son = divItem.find('.text').text();
                            console.log(divItem)
                            if(divItem.find('.text').text().indexOf('-') !== -1){
                                $parent = divItem.find('.text').text().split('-')[1].trim();
                                $son = divItem.find('.text').text().split('-')[0].trim();
                            }
                            self.currentData = self.currentData.filter(function(k){
                                return self.targetData[0].index != k.index;
                            })
                        }
                    }
                    var texts = self.currentData.map(function(k){return k.text;})
                    // console.log(self.currentData)
                    // console.log(texts)
                    $(e.currentTarget).parent().parent().parent().attr('title',texts.join('、'))  //设置元素移入显示全部内容 设置title属性
                    $(e.currentTarget).parent().parent().siblings('.my-select-btn').find('.btn-text').html(self.setResultData(self.currentData));
                })
                $(target1).find('.my-select-btn').on('click','.close-data',function(e){
                    e = e || window.event;
                    e.stopPropagation();
                    var curr = $(this).parent(),
                        currIndex = curr.attr('index');
                    self.searchOn(self.data,currIndex,false);//设置on值
                    $(target).find('.div-item[index="'+currIndex+'"]').removeClass('on').children('.my-checks').removeClass('on');//删除选中样式
                    self.currentData = self.currentData.filter(function(index){return index.index != currIndex;});//处理当前data的数据

                    var texts = self.currentData.map(function(k){return k.text;});//获取选中的部门
                    $(target).attr('title',texts.join('、'))  //设置元素移入显示全部内容 设置title属性
                    curr.remove();
                })
                self.areaOptions = new myFun.options(null,null,null,false,target1);
                self.areaOptions.setOnNum($(target1).find('.my-select-list .my-option-content'))//返回checks为on的元素内容
                self.currentData = self.areaOptions.onNum;
                //调用options组件
                self.inputs(target1);
            }
            self.setResultData = function(data){
                var str = '';
                $.each(data,function(index,item){
                    str += '<span class="mydata" index="'+item.index+'">'+item.text+'<i class="iconfont iconshanchu close-data" departcode="'+item.DepartCode+'"></i></span>';
                })
                // console.log(str)
                return str;
            }
            self.setContentData = function(Data){
                // 添加部门树，商圈树
                if($('#Tmpl-area').length === 0){
                    // $('body').append('<script type="text/x-jsrender" id="Tmpl-area"><div class="my-option-content"><div class="my-options">{{!-- 这层div是占位子的 --}}<div class="div-item option-title"}></div><ul class="option-content"><li class="div-item"><div class="my-options">{{!-- 这层title才是最顶部的 --}}<div class="div-item option-title on" index={{:index}}><span class="text" >{{:DepartName}}</span><div {{if on}}class="my-checks fl-le on"{{else}}class="my-checks fl-le"{{/if}}><i class="iconfont iconUtubiao-12"></i></div><i class="fl-le btns-icon iconfont icon1-unfold up"></i><i class="fl-le btns-icon iconfont icon1-fold down"></i></div><ul class="option-content">{{for DepartmentListZtree tmpl="#tmpChild" /}}</ul></div></li></ul></div></div></script>');
                    $('body').append(`
                        <script type="text/x-jsrender" id="Tmpl-area">
                            <div class="my-option-content">
                                <div class="my-options">{{!-- 这层div是占位子的 --}}
                                    <div class="div-item option-title on" index={{: index}}>
                                        <span class="text">{{: DepartName}}</span>
                                        <i class="fl-le btns-icon iconfont icon1-unfold up"></i>
                                        <i class="fl-le btns-icon iconfont icon1-fold down"></i>
                                    </div>
                                    <ul class="option-content">{{for DepartmentListZtree tmpl="#tmpChild" /}}</ul>
                                </div>
                            </div>
                        </script>
                    `);
                    $('body').append('<script type="text/x-jsrender" id="tmpChild"><li class="div-item"><div class="my-options"><div class="div-item option-title" index={{:index}}><span  class="text" name={{:index}}>{{:DepartName}}</span><div {{if on}}class="my-checks fl-le on"{{else}}class="my-checks fl-le"{{/if}}><i class="iconfont iconUtubiao-12"></i></div>{{if DepartmentListZtree.length !== 0}}<i class="fl-le btns-icon iconfont icon1-unfold up"></i><i class="fl-le btns-icon iconfont icon1-fold down"></i>{{else}}<span></span>{{/if}}</div>{{if DepartmentListZtree.length !== 0}}<ul class="option-content">{{for DepartmentListZtree tmpl="#tmpChild" /}}</ul>{{/if}}</div></li></script>')
                }
                return $("#Tmpl-area").render(Data);
            }
            self.setSearchData = function(Data){//flag  true:打开搜索部门   false:打开搜索结果
                // 添加搜索结果样式
                if($('#Tmpl-search').length === 0){
                    $('body').append('<script type="text/x-jsrender" id="Tmpl-search">{{for data ~flag1 = flag}}{{if on}}<div class="div-item option-title on" index={{:index}}>{{if ~flag1}}<span class="text">{{:text}} - {{:parent}}</span>{{else}}<span class="text">{{:text}}</span>{{/if}}<div class="my-checks fl-le on"><i class="iconfont iconUtubiao-12"></i></div></div>{{else}}<div class="div-item option-title" index={{:index}}>{{if ~flag1}}<span class="text">{{:text}} - {{:parent}}</span>{{else}}<span class="text">{{:text}}</span>{{/if}}<div class="my-checks fl-le"><i class="iconfont iconUtubiao-12"></i></div></div>{{/if}}{{/for}}</script>');
                }
                if( Data.length === 0){
                    return '<div class="no-data"><span class="text">搜索无结果</span></div>';
                }
                Data = {flag:flag,data: Data};
                // console.log(Data)
                var html2 = $("#Tmpl-search").render(Data);
                return html2;
            }
            self.getCurrentData = function(){ //返回当前选择的DepartCode 值
                // console.log(self.currentData)
                return self.currentData.map(function(k){return k.DepartCode});
            }
            self.dataProcess = function(data){//给data添加标识
                self.addDataIndex(data);
                data = JSON.parse(JSON.stringify(data).replace(/"DepartName"/g,'"on":false,"DepartName"'));
                self.data = data;   //保存Date的值
                // console.log(self.data)
            }
            self.addDataIndex = function(ele){//给data添加index字段
                for(var i in ele){
                    if(typeof ele[i]=="object"){
                        self.addDataIndex(ele[i])
                    }else{
                        if(ele['index'] === undefined){
                            // console.log(ele[index]);
                            ele['index'] = indexData++;
                        }
                　　}
                }
            }
            self.searchOn = function(ele,index1,on){
            //点击btn-icons设置数据的btnIconsOn的值
                $.each(ele,function(index,item){
                    if(ele[index].index == index1){
                        // console.log(ele[index])
                        item.on = on;
                        return false;
                    }
                    if(ele[index].DepartmentListZtree.length != 0){
                        self.searchOn(ele[index].DepartmentListZtree,index1,on);
                    }
                })
            }
            //收起二级菜单
            self.fold = function(target){
                var second = $(target+' .my-option-content>.my-options .my-options:first').find('>.option-content >.div-item >.my-options');
                second.find('.option-title').removeClass('on');
                second.find('.option-content').slideUp(0);
            }
            self.dataProcess(Data);
            // console.log(self.data)
            self.init(target,Data)
            return self;
        },
        //员工单选
        //target目标元素，Data目标数据，clickFun点击事件（搜索的也算），optionFlag是否开启点击出现全部部门（默认打开,false不打开），keyup inputs的keyup事件。
        staff:function(target,Data,clickFun,optionFlag,keyup,onfold){
            var self = this;
            if(optionFlag == undefined) optionFlag = true;

            self.data = Data;   //保存Date的值
            // self.currentData = [];//保存当前选择的
            self.searchData = [];//保存搜索的
            self.targetData = [];//保存目标的
            self.onSearch = '';//保存搜索的值
            var indexData = 0;
            // self.areaOptions = null;//保存options对象

            self.init = function(target1){
                // // 添加部门树，商圈树
                self.addDataIndex(Data,Data[0]); //数据添加唯一字段
                // console.log(Data)
                self.addDataIndex1(Data);//数据添加商圈字段
                // console.log(self.data)
                optionFlag && self.useData();
                $(target1).find('.my-input').on('click',null,function(e){
                    e.stopPropagation();
                    e = e || window.event;
                    //关闭其��staff插件
                    $('.my-input-list').not($(target1).find('.my-input-list')).stop().slideUp(100);
                    $('.my-search-list').not($(target1).find('.my-search-list')).stop().slideUp(100);
                    if(e.target.className.indexOf('close-js') !== -1){
                        $(target1).find('.active').removeClass('active'); // 2019年8月9日14:46:44 改动
                        optionFlag?$(target1).find('.my-search-list').hide().siblings('.my-input-list').show():$(target1).find('.my-search-list').hide();
                        self.onSearch = '';
                        self.targetData = [];
                        return;
                    }
                    if((e.target.nodeName.toLowerCase() == 'span') || e.target.className == 'div-item'){
                        $(e.target).removeClass('on');
                        console.log($(e.target))
                        if(e.target.className.indexOf('green') !== -1 && e.target.nodeName.toLowerCase() == 'span'){
                            self.mapData(self.data,$(e.target).parent().parent().attr('index'),self.data[0]);
                        }
                        if(e.target.className.indexOf('text') !== -1){
                            // 2019年8月9日14:40:45 改动
                            $(target1).find('.active').removeClass('active');
                            if(self.onSearch == '') {
                                $(e.target).parent().addClass('active');
                            } else {
                                self.onSearch = '';
                                $(target1).find('.option-title').removeClass('on');
                                $(target1).find('.option-content').hide();
                                var myOptionsParent = $(target1).find(`.div-item[index=${$(e.target).parent().attr('index')}]`).parents('.my-options');
                                myOptionsParent.find('>.option-title').addClass('on');
                                myOptionsParent.find('>.option-content').show();
                                $(target1).find(`.div-item[index=${$(e.target).parent().attr('index')}]`).addClass('active');
                            }
                            // 2019年8月9日14:40:51 改动
                            self.mapData(self.data,$(e.target).parent().attr('index'),self.data[0]);
                        }
                        if(e.target.className == 'div-item'){
                            self.mapData(self.data,$(e.target).attr('index'),self.data[0]);
                        }
                        //关闭下拉
                        $('.my-input-list').stop().slideUp(100);
                        $('.my-search-list').stop().slideUp(100);

                        // 将选择文字影射到input上
                        $(target1).find('.inputs').val(self.targetData[0].DepartName);
                        $(target1).find('.my-input').removeClass('error');  // 清空错误类名
                        if(typeof clickFun === "function") clickFun && clickFun.call(self,self.targetData);
                        $(e.currentTarget).find('.inputs').val() !== '' && $(e.currentTarget).addClass('clear-js');
                    }
                    //点击到inputs
                    if(e.target.className.indexOf('inputs') !== -1){
                        if(optionFlag){
                            // console.log(self.onSearch)
                            if(self.onSearch == ''){
                                $(target1).find('.my-search-list').slideUp(0).siblings('.my-input-list').slideDown(100);
                                // $(target1).find('.inputs').val(''); 2019年8月9日14:20:36 改动
                                // console.log(self.targetData)
                                return;
                            }
                            if($(e.target).val() != ''){
                                $(target1).find('.my-search-list').slideDown(100).siblings('.my-input-list').slideUp(100);
                            }else{
                                $(target1).find('.my-search-list').slideUp(0).siblings('.my-input-list').slideDown(100);
                            }
                        }else {
                            if($(e.target).val() != ''){
                                $(target1).find('.my-search-list').slideDown(100).siblings('.my-input-list').slideUp(0);
                            }
                        }
                        // if($(e.target).val() != ''){
                        //     debounce1($(e.target).val())
                        // }
                    }
                }).find('.inputs').on('click',null,function(){
                    $('.my-select').removeClass('on');
                    $('.my-select-list').stop().hide();
                    $('.my-select-list-child').stop().hide();
                    var self = $(this);
                    (document.body.clientHeight - self[0].getBoundingClientRect().top - 280 < 0)?self.parent().parent().addClass('top'):self.parent().parent().removeClass('top');
                    // console.log(document.body.clientHeight - self[0].getBoundingClientRect().top - 280)
                })
                optionFlag && new myFun.options(null,null,null,false,target1,[],true);
                //调用options组件
                // myFun.componentFun.checks(target1);
                self.inputs(target1);
                // onfold && self.fold(target1);//是否展开或者收起  true为收起
            }
            // 防抖函数
            // var debounce1 = debounce(function(text){
            //     // console.log(self.setSearchData(self.searchData));
            //     self.inputsDebounce(target,text);
            // }, 600, false);
            // var parentText = self.data;//记录搜索结果的父子集
            self.inputs = function(target1){
                $(target1).find('.inputs').on('keyup',null,myFun.debounce(function(e){
                    $(target1).find('.active').removeClass('active'); // 2019年8月9日14:46:44 改动
                    self.onSearch = $(this).val();
                    // debounce1($(this).val());
                    self.inputsDebounce(target,$(this).val());
                }, 600, false))
                .on('blur',null,function(){
                    // console.log(self.targetData)
                    // 2019年8月9日14:19:54 改动
                    // var that = this;
                    // setTimeout(function(){
                    //     if(self.targetData.length !== 0 && self.onSearch == '') $(that).val(self.targetData[0].DepartName)
                    // },150)
                    // 2019年8月9日14:20:00 改动
                })
            }
            self.inputsDebounce = function(target1,text){
                // console.log(self.currentData)
                self.searchData = [];//清空选择的内容
                if(text == ''){
                    if(optionFlag){
                        $(target1).find('.my-search-list').hide().siblings('.my-input-list').show();
                    }else $(target1).find('.my-search-list').hide();
                    return;
                }
                self.search1(self.data,text,self.data[0])
                console.log(self.searchData)
                $(target1).find('.my-input-list').hide().siblings('.my-search-list').html(self.setSearchData(self.searchData)).show();
                keyup && keyup.call(null,self.searchData);
            }
            //初始页面
            self.useData = function(){
                var self = this;
                var html2 = self.setContentData(self.data, self.disabledChecks);
                $(target).find('.my-input-list').html(html2);
            }
            //循环数组的
            self.mapData = function(goal,index2,parent){ //index每个值都不同，传进来找到这对象以及上级的对象 ，parent为父级对象, on ： true为开启，false为关闭
                $.each(goal,function(index,item){
                    if(goal[index].index == index2){
                        // console.log(goal[index])
                        self.targetData = [goal[index],parent];
                    }else{
                        if(goal[index].DepartmentListZtree.length != 0){
                            self.mapData(goal[index].DepartmentListZtree,index2,goal[index]);
                        }
                    }
                })
            }//返回两个对象，1. 自己的对象，2. 父级的对象
            //搜索部门的
            self.search1 = function(ele,text,parent){
                $.each(ele,function(index,item){
                    if(ele[index].DepartName.indexOf(text) !== -1){
                        var divItem = $(target).find('.my-input-list .option-title[index="'+ele[index].index+'"]');
                        // var parentItem = divItem.parent().parent().parent().siblings('.option-title');
                        var parentItem = divItem.parents(".my-option-content>.my-options>.option-content>.div-item>.my-options>.option-content>.div-item>.my-options").find(">.div-item");
                        self.searchData.push({
                            text:divItem.find('.text').html().replace(text,"<span class='green'>"+text+"</span>") + (divItem.find('.text-label').html() || ''),
                            parent:parentItem.find('.text').html() + (parentItem.find('.text-label').html() || ''),
                            index:ele[index].index
                        })
                    }
                    // console.log(ele[index])
                    if(ele[index].DepartmentListZtree.length != 0){
                        self.search1(ele[index].DepartmentListZtree,text,ele[index]);
                    }
                })
            }
            self.setContentData = function(Data){
                // 添加部门树，商圈树
                // 添加部门树，商圈树
                // if($('#Tmpl-area').length === 0){
                //     $('body').append('<script type="text/x-jsrender" id="Tmpl-area"><div class="my-option-content"><div class="my-options">{{!-- 这层div是占位子的 --}}<div class="div-item option-title" index={{:index}}></div><ul class="option-content"><li class="div-item"><div class="my-options">{{!-- 这层title才是最顶部的 --}}<div class="div-item option-title on" index={{:index}}><span class="text" >{{:DepartName}}</span><div {{if on}}class="my-checks fl-le on"{{else}}class="my-checks fl-le"{{/if}}><i class="iconfont iconUtubiao-12"></i></div><i class="fl-le btns-icon iconfont icon1-unfold up"></i><i class="fl-le btns-icon iconfont icon1-fold down"></i></div><ul class="option-content">{{for DepartmentListZtree tmpl="#tmpChild" /}}</ul></div></li></ul></div></div></script>');
                //     $('body').append('<script type="text/x-jsrender" id="tmpChild"><li class="div-item"><div class="my-options"><div class="div-item option-title" index={{:index}}><span  class="text" name={{:index}}>{{:DepartName}}</span><div {{if on}}class="my-checks fl-le on"{{else}}class="my-checks fl-le"{{/if}}><i class="iconfont iconUtubiao-12"></i></div>{{if DepartmentListZtree.length !== 0}}<i class="fl-le btns-icon iconfont icon1-unfold up"></i><i class="fl-le btns-icon iconfont icon1-fold down"></i>{{else}}<span></span>{{/if}}</div>{{if DepartmentListZtree.length !== 0}}<ul class="option-content">{{for DepartmentListZtree tmpl="#tmpChild" /}}</ul>{{/if}}</div></li></script>')
                // }
                // return $("#Tmpl-area").render(Data);
                if($('#Tmpl-staff').length === 0){
                    $('body').append('<script type="text/x-jsrender" id="Tmpl-staff"><div class="my-option-content"><div class="my-options">{{!-- 这层div是占位子的 --}}<div class="div-item option-title" index={{:index}}></div><ul class="option-content"><li class="div-item"><div class="my-options">{{!-- 这层title才是最顶部的 --}}<div class="div-item option-title on" index={{:index}}><span class="text" >{{:DepartName}}</span><i class="fl-le btns-icon iconfont icon1-unfold up"></i><i class="fl-le btns-icon iconfont icon1-fold down"></i></div><ul class="option-content">{{for DepartmentListZtree tmpl="#tmpChild-staff" /}}</ul></div></li></ul></div></div></script>');
                }
                if($('#tmpChild-staff').length === 0){
                    $('body').append('<script type="text/x-jsrender" id="tmpChild-staff"><li class="div-item"><div class="my-options"><div class="div-item option-title" index={{:index}}><span  class="text" name={{:DepartCode}}>{{:DepartName}}</span>{{if DepartmentListZtree.length !== 0}}<i class="fl-le btns-icon iconfont icon1-unfold up"></i><i class="fl-le btns-icon iconfont icon1-fold down"></i>{{else}}<span></span>{{/if}}</div>{{if DepartmentListZtree.length !== 0}}<ul class="option-content">{{for DepartmentListZtree tmpl="#tmpChild-staff" /}}</ul>{{/if}}</div></li></script>')
                }
                return $("#Tmpl-staff").render(Data);
            }
            self.setSearchData = function(Data){//flag  true:打开搜索部门   false:打开搜索结果
                // 添加搜索结果样式
                if($('#Tmpl-staff-search').length === 0){
                    $('body').append('<script type="text/x-jsrender" id="Tmpl-staff-search">{{if data.length == 0}}<div class="div-item nostaff" index={{:index}}>搜索无结果</div>{{else}}{{for data}}<div class="div-item" index={{:index}}><span class="text">{{:text}} - {{:parent}}</span></div>{{/for}}{{/if}}</script>');
                }
                // console.log(Data)
                Data = {data: Data};
                // console.log(Data)
                var html2 = $("#Tmpl-staff-search").render(Data);
                return html2;
            }
            self.getCurrentData = function(){ //返回当前选择的DepartCode 值
                return self.currentData.map(function(k){return k.DepartCode});
            }
            self.addDataIndex = function(ele,parnet){//给data添加index字段
                for(var i in ele){
                    if(typeof ele[i]=="object"){
                        self.addDataIndex(ele[i],ele)
                    }else{
                        if(ele['index'] === undefined){
                            // console.log(ele[index]);
                            ele['index'] = indexData++;
                        }
                　　}
                }
            }
            //tier字段：1为区域，2为片区，3为商圈
            self.addDataIndex1 = function(ele){//给data添加商圈片区字段
                ele[0].tier = 1;
                for(var i in ele[0].DepartmentListZtree){
                    var v = ele[0].DepartmentListZtree[i];
                    v.tier = 2;
                    for(var j in v.DepartmentListZtree){
                        var k = v.DepartmentListZtree[j];
                        k.tier = 3;
                    }
                }
            }
            self.refresh = function(){
                $(target).find('.inputs').val('')
                self.searchData = [];//保存搜索的
                self.targetData = [];//保存目标的//关闭下拉
                $('.my-input-list').stop().slideUp(100);
                $('.my-search-list').stop().slideUp(100);
            }
            //收起二级菜单
            self.fold = function(target){
                // console.log(target)
                setTimeout(function(){
                    var second = $(target+' .my-option-content>.my-options .my-options:first').find('>.option-content >.div-item >.my-options');
                    second.find('.option-title').removeClass('on');
                    second.find('.option-content').slideUp(0);
                },0)
            }
            self.init(target,Data)
            return self;
        },
        areaSearch:function(target,clickFun,keyup,listShow){
            var self = this;
            self.init = function(target1){
                // console.log(self.data)
                if(listShow){
                    keyup.bind(this)();
                }
                self.useData();
                $(target1).find('.my-input').on('click',null,function(e){
                    e.stopPropagation();
                    e = e || window.event;
                    //关闭其他staff插件
                    $('.my-input-list').not($(target1).find('.my-input-list')).stop().slideUp(100);
                    $('.my-search-list').not($(target1).find('.my-search-list')).stop().slideUp(100);
                    if(e.target.className.indexOf('close-js') !== -1){
                        $(target1).find('.my-search-list').hide()
                        return;
                    }
                    if((e.target.className.indexOf('text') !== -1 && e.target.nodeName.toLowerCase() == 'span') || (e.target.className.indexOf('green') !== -1 && e.target.nodeName.toLowerCase() == 'span')){
                        $(e.target).removeClass('on');
                        // self.mapData(self.data,$(e.target).parent().attr('index'),self.data[0]);
                        //关闭下拉
                        $('.my-input-list').stop().slideUp(100);
                        $('.my-search-list').stop().slideUp(100);
                        // 将选择文字影射到input上
                        // $(target1).find('.inputs').val(self.targetData[0].DepartName)
                        var index1 = $(e.target).parent().hasClass('div-item')?$(e.target).parent().index():$(e.target).parent().parent().index();
                        // console.log(self.searchData[index1])
                        if(typeof clickFun === "function") clickFun && clickFun.call(self,self.searchData[index1])
                    }
                    //点击到inputs
                    if(e.target.className.indexOf('inputs') !== -1){
                        $('.my-select').removeClass('on');
                        $('.my-select-list').stop().hide();
                        $('.my-select-list-child').stop().hide();
                        if($(e.target).val() != '' || listShow){
                            // self.inputsDebounce(target1,$(e.target).val())
                            $(target1).find('.my-search-list').stop().slideDown(200);
                            if(listShow){
                                self.inputsDebounce(target1,$(e.target).val());
                            }
                        }
                    }
                })
                //调用options组件
                // myFun.componentFun.checks(target1);
                self.inputs(target1);
                self.refresh();
            }
            self.refresh = function(){
                $(target).find('.inputs').val('')
                self.searchData = [];//保存搜索的
                self.targetData = [];//保存目标的//关闭下拉
                $('.my-input-list').stop().slideUp(100);
                $('.my-search-list').stop().slideUp(100);
            }

            //初始页面
            self.useData = function(){
                var self = this;
                if(self.data != undefined)
                    var html2 = self.setContentData(self.data);
                $(target).find('.my-search-list').html(html2);
            }

            self.setContentData = function(Data){//flag  true:打开搜索部门   false:打开搜索结果
                // 添加搜索结果样式
                if($('#Tmpl-area1-search').length === 0){
                    $('body').append('<script type="text/x-jsrender" id="Tmpl-area1-search">{{if data.length == 0}}<div class="div-item nostaff">搜索无结果</div>{{else}}{{for data}}<div class="div-item" code="{{:code}}"><p class="text">{{:text}}</p></div>{{/for}}{{/if}}</script>');
                }
                // console.log(Data)
                Data = {data: Data};
                console.log(Data)
                var html2 = $("#Tmpl-area1-search").render(Data);
                return html2;
            }
            self.inputs = function(target1){
                // 防抖函数
                // console.log($(target1))
                var debounce1 = debounce(function(text){
                    // console.log(111);
                    self.inputsDebounce(target1,text);
                },400);
                // console.log($(target1))
                $(target1).find('.inputs').on('keyup',null,function(e){
                    debounce1($(this).val());
                })
            }
            self.inputsDebounce = function(target1,text){
                // console.log('```````````````````')
                if(text == '' && !listShow){
                    self.useData();
                    // self.areaOptions = new myFun.options(null,null,null,true,target1);
                    $(target1).find('.my-search-list').hide()
                    return;
                }
                keyup && keyup.call(this,text);
                // if(self.searchData == undefined) self.searchData = [];
                // $(target1).find('.my-search-list').html(self.setContentData(self.setHighLight(text))).show();
            }
            self.setSearchData = function(data,text){
                self.searchData = data;
                if(self.searchData == undefined) self.searchData = [];
                $(target).find('.my-search-list').html(self.setContentData(self.setHighLight(text)));
                if(!listShow){
                    $(target).find('.my-search-list').show();
                }else{
                    $(target).find(".div-item").off("click").on("click",function(e){
                        e.stopPropagation();
                        console.log($(this));
                        $(target).find('.inputs').val($(this).find(".text").text()).attr('code', $(this).attr('code'));
                        $(this).parent().stop().slideUp(100);
                        if($(target).find(".num").length >0 && $(target).find("[data-type=wordLimit]").length>0){
                            $(target).find(".num").text($(this).find(".text").text().length);
                        }
                    });
                }
            }
            //添加高亮
            self.setHighLight = function(text){
                return self.searchData.map(function(k){
                    return {text:k.text.replace(text,"<span class='green'>"+ text +"</span>"),code:k.code}
                })
            }
            self.init(target);
        },
        areaAll:function(target,Data,clickFun){
            var self = this;
            var indexData = 0;//计数的
            //Data数据深拷贝以及修改键名'on'
            self.currentData = [];//保存当前选择的
            self.targetData = [];//保存目标的
            self.areaOptions = null;//保存options对象
            var onRepetition = true;//控制添加on的只添加一次
            //初始页面
            self.useData = function(){
                var self = this;
                var html2 = self.setContentData(self.data);
                $(target).find('.my-list-content').html(html2);
                myFun.componentFun.checks(target);
            }
            //循环数组的改变on值
            self.mapData = function(goal,index2,parent,on){ //index每个值都不同，传进来找到这对象以及上级的对象 ，parent为父级对象, on ： true为开启，false为关闭
                // console.log(goal)
                $.each(goal,function(index,item){
                    if(goal[index].index == index2){
                        if(on === undefined) on = true;
                        self.targetData = [goal[index],parent];
                        if(goal[index].on == on){
                            onRepetition = false;
                            return false;
                        }else{
                            goal[index].on = on;
                            onRepetition = true;
                            return true;
                        }
                    }else{
                        if(goal[index].DepartmentListZtree.length != 0){
                            return self.mapData(goal[index].DepartmentListZtree,index2,goal[index],on);
                        }
                    }
                })
            }
            // 修改选中和不选中样式
            self.getEleOn = function(index,on){
                on?$(target).find('[index='+index+']').find('.my-checks').addClass('on'):$(target).find('[index='+index+']').find('.my-checks').removeClass('on');
            }
            self.init = function(target1,Data){
                // // 添加部门树，商圈树
                self.addDataIndex1(Data);//数据添加商圈字段
                self.useData();
                self.fold(target1)
                $(target1).find('.my-list-content').on('click',null,function(e){
                    e = e || window.event;
                    var on1 = null;
                    if(e.target.className.indexOf('text') !== -1 && e.target.nodeName.toLowerCase() == 'span'){
                        $(e.target).siblings('.my-checks').toggleClass('on');
                        //关闭或开启子代选中
                        $(e.target).removeClass('on');
                        // console.log($(e.target).parent().find('.my-checks').hasClass('on'))
                        if($(e.target).parent().find('.my-checks').hasClass('on')){ //点击文字添加
                            self.mapData(self.data,$(e.target).parent().attr('index'),self.data[0],true);
                            self.currentData.push({text:self.targetData[0].DepartName,parent: self.targetData[1].DepartName,DepartCode:self.targetData[0].DepartCode,index:self.targetData[0].index});
                            // console.log(self.currentData)
                            on1 = true;
                        }else{//点击文字删除
                            self.mapData(self.data,$(e.target).parent().attr('index'),self.data[0],false);
                            // console.log(self.targetData)
                            var $parent = self.targetData[1].DepartName;
                            var $son = $(e.target).text();
                            if($(e.target).text().indexOf('-') !== -1){
                                var $parent = $(e.target).text().split('-')[1].trim();
                                var $son = $(e.target).text().split('-')[0].trim();
                            }
                            self.currentData = self.currentData.filter(function(k){
                                return self.targetData[0].index != k.index;
                            })
                            on1 = false;
                        }
                        clickFun && clickFun.call(self,on1,self.targetData)
                    }
                    //判断是否添加还是删除
                    if($(e.target).parent().hasClass('my-checks')||$(e.target).hasClass('my-checks')){
                        var divItem = $(e.target).parent().hasClass('my-checks')? $(e.target).parent().parent(): $(e.target).parent();
                        // console.log(divItem)
                        if(divItem.find('.my-checks').hasClass('on')){ //点击图标添加
                            self.mapData(self.data,divItem.attr('index'),self.data[0],true);
                            self.currentData.push({text:self.targetData[0].DepartName,parent: self.targetData[1].DepartName,DepartCode:self.targetData[0].DepartCode,index:self.targetData[0].index})
                            on1 = true;
                        }else{//点击图标删除
                            self.mapData(self.data,divItem.attr('index'),self.data[0],false);
                            var $parent = self.targetData[1].DepartName;
                            var $son = divItem.find('.text').text();
                            // console.log(divItem)
                            if(divItem.find('.text').text().indexOf('-') !== -1){
                                $parent = divItem.find('.text').text().split('-')[1].trim();
                                $son = divItem.find('.text').text().split('-')[0].trim();
                            }
                            self.currentData = self.currentData.filter(function(k){
                                return self.targetData[0].index != k.index;
                            })
                            on1 = false;
                        }
                        clickFun && clickFun.call(self,on1,self.targetData)
                    }
                    // var texts = self.currentData.map(function(k){return k.text;})
                    // // console.log(self.targetData)
                    // console.log(texts)
                    // // $(e.currentTarget).parent().parent().attr('title',texts.join('、'))  //设置元素移入显示全部内容 设置title属性
                    // // $(e.currentTarget).parent().siblings('.my-select-btn').find('.btn-text').text(texts.join('、'));
                    // console.log(this)
                (document.body.clientHeight - this.getBoundingClientRect().top - 200 < 0)?$(this).parent('.my-select').addClass('bottom'):$(this).parent('.my-select').removeClass('bottom')
                })
                self.areaOptions = new myFun.options(null,null,null,false,target1);
                self.areaOptions.setOnNum($(target1).find('.my-select-list .my-option-content'))//返回checks为on的元素内容
                self.currentData = self.areaOptions.onNum;
                //调用options组件
            }
            self.setContentData = function(Data){
                // 添加部门树，商圈树
                // console.log(Data)
                if($('#Tmpl-area').length === 0){
                    $('body').append('<script type="text/x-jsrender" id="Tmpl-area"><div class="my-option-content"><div class="my-options">{{!-- 这层title才是最顶部的 --}}<div class="div-item option-title on" index={{:index}} ><span class="text" >{{:DepartName}}</span><div {{if on}}class="my-checks fl-le on"{{else}}class="my-checks fl-le"{{/if}}><i class="iconfont iconUtubiao-12"></i></div><i class="fl-le btns-icon iconfont icon1-unfold up"></i><i class="fl-le btns-icon iconfont icon1-fold down"></i></div><ul class="option-content">{{for DepartmentListZtree tmpl="#tmpChild" /}}</ul></div></li></div></script>');
                    $('body').append('<script type="text/x-jsrender" id="tmpChild"><li class="div-item"><div class="my-options"><div class="div-item option-title" index={{:index}} tier={{:tier}}><span  class="text" name={{:index}}>{{:DepartName}}</span><div {{if on}}class="my-checks fl-le on"{{else}}class="my-checks fl-le"{{/if}}><i class="iconfont iconUtubiao-12"></i></div>{{if DepartmentListZtree.length !== 0}}<i class="fl-le btns-icon iconfont icon1-unfold up"></i><i class="fl-le btns-icon iconfont icon1-fold down"></i>{{else}}<span></span>{{/if}}</div>{{if DepartmentListZtree.length !== 0}}<ul class="option-content">{{for DepartmentListZtree tmpl="#tmpChild" /}}</ul>{{/if}}</div></li></script>')
                }
                return $("#Tmpl-area").render(Data);
            }
            self.getCurrentData = function(){ //返回当前选择的DepartCode 值
                // console.log(self.currentData)
                return self.currentData.map(function(k){return k.DepartCode});
            }
            self.dataProcess = function(data){//给data添加标识
                self.addDataIndex(data);
                data = JSON.parse(JSON.stringify(data).replace(/"DepartName"/g,'"on":false,"DepartName"'));
                self.data = data;   //保存Date的值
                // console.log(self.data)
            }
            self.addDataIndex = function(ele){//给data添加index字段
                for(var i in ele){
                    if(typeof ele[i]=="object"){
                        self.addDataIndex(ele[i])
                    }else{
                        if(ele['index'] === undefined){
                            // console.log(ele[index]);
                            ele['index'] = indexData++;
                        }
                　　}
                }
            }
            //tier字段：1为区域，2为片区，3为商圈
            self.addDataIndex1 = function(ele){//给data添加商圈片区字段
                ele[0].tier = 1;
                console.log(ele[0])
                for(var i in ele[0].DepartmentListZtree){
                    var v = ele[0].DepartmentListZtree[i];
                    v.tier = 2;
                    for(var j in v.DepartmentListZtree){
                        var k = v.DepartmentListZtree[j];
                        k.tier = 3;
                    }
                }
                self.data = ele;
            }
            //传入index值，取消列表选中样式
            self.removeData = function(index){
                self.mapData(self.data,index,self.data[0],false);
                var $parent = self.targetData[1].DepartName;
                var $son = self.targetData[0].DepartName;
                self.currentData = self.currentData.filter(function(k){
                    return self.targetData[0].index != k.index;
                })
                console.log(self.currentData)
                self.getEleOn(index,false);
            }
            //传入index值，开启列表选中样式
            self.addData = function(index){
                self.mapData(self.data,index,self.data[0],true)
                onRepetition && self.currentData.push({text:self.targetData[0].DepartName,parent: self.targetData[1].DepartName,DepartCode:self.targetData[0].DepartCode,index:self.targetData[0].index})
                console.log(self.currentData)
                self.getEleOn(index,true);
            }
            //展开
            self.unfold = function(ele){
                var eleParent = ele.parent();
                // console.log(eleParent)
                if(eleParent.hasClass('my-option-content')){
                    return false;
                }else{
                    if(eleParent.css('display') == 'none' && eleParent.hasClass('option-content')){
                        eleParent.slideDown(0);
                    }
                    this.unfold(eleParent);
                }
            }
            //收起二级菜单
            self.fold = function(target){
                $(target+' .my-option-content>.my-options').find('[tier=2]').each(function(index,item){
                    $(item).siblings('.option-content').slideUp(0);
                })
            }
            self.dataProcess(Data);
            self.init(target,Data)
            return self;
        },
    },
    //layer弹窗
    layer:{
        /**
         * 生成表格表格 (*为必填项)
         * @param {object} {
         *  {String} *target:指定原始table容器的选择器；
         *  {Array} *data: 赋值数据。既适用于只展示一页数据，也非常适用于对一段已知数据进行多页展示；
         *  {Array} *cols: 设置表头。值是一个二维数组。参数有
         *                 {String} field: 设定字段名。字段名的设定非常重要，且是表格数据的唯一标识；
         *                 {String} title: 设定标题名称。
         *                 {Number/String} width: 设定列宽，若不填写，则自动分配；若填写，则支持值为：数字、百分比。
         *                 {Number} minWidth: 局部定义当前常规单元格的最小宽度，一般用于列宽自动分配的情况。
         *                 {String} type: 设定列类型。可选值有: normal(常规列，无需设定) checkbox(复选框列) radio(单选框列) number(序号列) space(空列)
         *                 {Boolean} LAY_CHECKED: 是否全选状态(默认：false)。必须复选框列开启后才有效，如果设置true，则表示复选框默认全部选中。
         *                 {String} fixed: 固定列。可选值有：left（固定在左）、right（固定在右）。一旦设定，对应的列将会被固定在左或在右，不随滚动条而滚动。注意：如果是固定在左，该列必须放在表头最前面；如果是固定在右，该列必须放在表头最后面。
         *                 {Boolean} hide: 是否初始隐藏列。默认：false；
         *                 {Boolean} sort: 是否允许排序（默认：false）。如果设置true，则在对应的表头显示排序icon，从而对开启排序功能。（ASCII码对比排序）
         *                 {Boolean} unresize: 是否禁用拖拽列宽（默认：false）。默认复选框列，会自动禁用。普通列，默认允许拖拽列宽，可以使用true来禁用拖拽。
         *                 {String} align: 单元格排序。可选值有：left（默认）、center（居中）、right（居右）。
         *  {Number} minWidth: 单元格的最小宽度；
         *  {Number} maxHeight: 设置表格的最大高度。主要作用是，在默认风格下的skin固定高度又数据不多的情况下，下面会留很多空白。
         *  {String/Number} height: 设定容器高度；参数："-":默认情况，高度随数据列表而适应，表格容器不会出现纵向滚动条。
         *                                            "height: 350"：固定值，设置一个数字，用于定义容器高度，当容器中的内容超出了该高度时，会自动出现纵向滚动条。
         *                                            "full-差值"：高度将始终铺满，无论浏览器尺寸如何。这是一个特定的语法格式，其中 full 是固定的，而 差值 则是一个数值，这需要你来预估，比如：表格容器距离浏览器顶部和底部的距离“和”。
         *  {Number} limit: 每页显示的条数，默认: 10。值务必对应limits参数的选项。
         *  {Boolean} allUnresize: 设置cols里的全部列不可手动拖拽列宽。
         *  {Array} limits: 每页条数的选择项，默认: [5,10,20,30,50]。(一般不用设置)
         *  {String} skin: 用于设定表格风格，参数：line(行表框风格) row(列边框风格) nob(无表框风格)。若使用默认风格不设置该属性即可(设置默认风格可以为false)。
         *  {String} size: 用于设定表格尺寸，参数：sm(小尺寸) lg(大尺寸)。若使用默认尺寸不设置该属性即可。
         * }
         * @param {function} callback   回调函数，可接受到table对象与当前生成表格对象
         */
        table: function({target= "", data= [], cols= [], minWidth= 86, height= "-", maxHeight= 0, limit= 10,
                        limits= [5,10,20,30,50,90], skin="line", size= "",allUnresize = false} = {}, callback) {
            const UniqueId = myFun.utils.createUniqueId(1).toString();
            if(allUnresize) cols.filter((item) => item.unresize = allUnresize);
            cols.filter((item) => {  // 解决字数大于4个以上会显示省略号的问题
                if(!item.title) return;
                if(item.title.length > 4) item.minWidth = (myFun.utils.IsHanZi(item.title) ? (item.title.length * 14) : (item.title.length * 8)) + 30; // (字数 * 字的长度) + 内边距
            });
            layui.use("table", function() {
                _tableIns = layui.table.render({
                    id: "table"+UniqueId,
                    elem: document.querySelector(target),
                    cellMinWidth: minWidth, //全局定义常规单元格的最小宽度，layui 2.2.1 新增
                    height: maxHeight || height,
                    data: data,
                    limit: limit,
                    limits: limits,
                    skin: skin,
                    // even: true,
                    size: size,
                    text: { none: '-'},
                    cols: [cols]
                })
                // 获取到表格DOM
                const $table_view = $("[lay-id=table]")
                      $table_body = $table_view.find(".layui-table-body");
                if(maxHeight) { // 实现
                    var form_height = $table_view.css("height")
                        body_height = $table_body.css("height");
                    $table_view.css({"height": "auto", "max-height": form_height});
                    $table_body.css({"height": "auto", "max-height": body_height});
                }
                callback && callback(layui.table,_tableIns);
                var widthListener = setInterval(function() {
                    if($table_view.width() >= 0) {
                        // 解决table宽度与父标签不一致
                        var $layui_table = $table_view.find(".layui-table"),
                            // 判断表格内容高度是否超出，没超出不计算滚动条的高度，超出会计算一个滚动条的宽度
                            min_width = parseInt($table_body.css("max-height")) > $layui_table.eq(1).height() ?
                                            $table_view.width() : $table_view.width() - ($table_view.width() - $layui_table.width())
                        $layui_table.css("min-width", min_width);
                        clearInterval(widthListener);
                    }
                }, 100);
            })
        },
        //打开相册
        photoImg:function (imgTarget,parent,children,type){
            type = type || 'default';
            layer.open({
                type: 1,
                title: false,
                closeBtn: 0,
                shadeClose: true,
                skin:'swiper-img layer-photo-all',
                // maxWidth: 650,
                scrollbar: false,
                // area: ['650px','650px'],
                area:'80%',
                anim: -1,
                isOutAnim: false,
                content: '<div class="swiper '+ type +'"><div class="swiper-container">'
                +'<div class="swiper-wrapper">'
                    //    +' <div class="swiper-slide"><img src="./images/1.png"/></div>'
                    //    +' <div class="swiper-slide"><img src="./images/pool.jpg"/></div>'
                    //    +' <div class="swiper-slide"><img src="./images/2.png"/></div>'
                +' </div>'
                    +' </div>'
                + '<div class="swiper-button-prev swiper-button-white"></div>'
                    +'<div class="swiper-button-next swiper-button-white"></div>'
                        +'<div class="close-layer">'
                            +'<i class="iconfont iconshanchu"></i>'
                        +'</div>'
                    + '</div>'
                + '<div class="btns">'
                + '<div class="button-icon-save"></div>'
                + '<div class="button-icon-rotate js-button-rotate"></div>'
                + '<div class="button-icon-in js-zoom-in"></div>'
                + '<div class="button-icon-out js-zoom-out"></div>'
                +'</div>'

                ,success :function(e){
                    $(imgTarget).parents(parent).find(children).each(function(index,item){
                        var itemBox = $(item).siblings(".js-pa-main");
                        var imgCloneNode = $(item.cloneNode()).attr("src", $(item).attr('data-url') || $(item).attr('src'));
                        $('.swiper-wrapper').append($('<div class="swiper-slide"></div>').append(imgCloneNode).append(itemBox.length > 0 ? itemBox.html():''))
                    })
                    var mySwiper = new Swiper('.swiper-container',{
                        // width: imgTarget.clientWidth,
                        nextButton: '.swiper-button-next',
                        prevButton: '.swiper-button-prev',
                        initialSlide: $(imgTarget).parents(parent).find(children).index(imgTarget)
                    });
                    //旋转按钮
                    e.find('.js-button-rotate').click(function(){

                        var activeSwiper = $(mySwiper.slides[mySwiper.activeIndex]).children("img");
                        var activeImg = activeSwiper.attr("style") || '';
                        var arr1 = activeImg.split("deg)") || "";
                        var arr2 = arr1[0].split('rotate(')[0] || '';
                        var translateleft = '';
                        var translateright = "";
                        var rotate = parseInt(arr1[0].split('rotate(')[1] || 0) + 90;

                        if(activeImg === ''){
                            translateleft = "translate(-50%, -50%) scale(0.8)";
                        }else{
                            translateleft = arr2.split("transform:")[1];
                            translateright = arr1[1].split(";")[0];
                        }

                        activeSwiper.css({
                            "transform": translateleft  + "rotate("+rotate+"deg)" + translateright
                        })

                    });
                    //关闭按钮
                    e.find('.close-layer').on('click',null,function(){
                        var index = e.attr('times')
                        layer.close(index);
                    });
                    // 放大
                    e.find(".js-zoom-in").on("click",function(){
                        zoomFn(1);
                    });
                    // 缩小
                    e.find(".js-zoom-out").on("click",function(){
                        zoomFn(0.6);
                    })

                    function zoomFn(zoom){
                        var activeSwiper = $(mySwiper.slides[mySwiper.activeIndex]).children("img");
                        var activeImg = activeSwiper.attr("style") || '';
                        var scc = "scale("+zoom+")";
                        var arr1 = activeImg.split("deg)") || "";
                        var rotate = parseInt(arr1[0].split('rotate(')[1] || 0);
                        var arr2 = activeImg.match(/scale\(([^)]*)/);

                        if(arr2 && arr2[1] === zoom+""){
                            scc = "scale(0.8)";
                        }

                        activeSwiper.css({
                            "transform": "translate(-50%, -50%) " + scc + "rotate("+rotate+"deg)"
                        })
                    }
                }
            });
        },
        opens: function(target,title,type,success,end){
            var area = '',classN = '';
            switch(type){
                case 'big': classN = 'layer-big ' + target.slice(1); area='1000px'; break;
                case 'normal': classN = 'layer-normal '  + target.slice(1); area='740px'; break;
                case 'small': classN = 'layer-small '  + target.slice(1); area='480px'; break;
                default: classN = 'layer-option' + target.slice(1); area=type; break;
            }
            $(target)[0].classList.length != 0 ?  classN += ' ' + $(target)[0].className : '';
            // console.log(area)
            index = layer.open({
                type: 1,
                title: title,
                area: area,
                shadeClose: false,
                skin: 'layer-window '+classN,
                content: target.indexOf('#')==-1?target:$(target).html(),
                resize:false,//不能拉伸
                scrollbar:false,
                moveOut: false,
                success: function(layero, index) {
                    success && success.call(layer,layero,index)
                    $(layero).on("click", ".my-btn-green", function() {
                        // loadingIndex = myFun.layer.loading();
                    });
                    for(var key in myFun.componentFun){
                        if(myFun.componentFun.hasOwnProperty(key)){
                    　　　　myFun.componentFun[key](layero);
                    　　}
                    }
                    try {
                        if(zFun != undefined) {
                            for(var key in zFun.componentFun) {
                                if(zFun.componentFun.hasOwnProperty(key)) {
                                    zFun.componentFun[key](layero);
                                }
                            }
                        }
                    } catch (error) {
                        console.log("zFun未找到！");
                    }
                    // myFun.componentFun.checks('.layer-window');
                    // myFun.componentFun.select('.layer-window');
                    if($('#layerdata').length !== 0) myFun.layer.layerdata1('#layerdata');

                    $('.layer-window').on('click','.my-btn-cancel',function(){
                        layer.close(index)
                    })
                },
                yes:function(index, layero){
                    // console.log(obj)
                    obj.success();
                },
                btn2:function(index, layero){

                },
                end:function(){
                    // console.log("end");
                    layer.close(loadingIndex);
                    end && end();
                }
            });
            // return obj;
            return index;
        },
        /**
         * 打开弹窗
         * @param {String} target：可以是链接或者是ID选择器、类选择器。
         * @param {String} title： 弹窗标题名称。
         * @param {String} areaType：弹窗大小，如big(大型)、normal（中型）、small（小型）、其它自定义弹窗宽度。
         * @param {Function} success：弹窗打开成功后的回调函数，传入参数有layer对象、layero(打开弹窗的DOM)、index(打开弹窗的索引)、iframeDom（打开iframe弹窗，获取到的iframeDOM对象）
         * @param {Function} end：弹窗关闭成功后的回调函数。
         */
        opens: function(target,title,areaType,success,end, flag = false){
            var area = ''
              , type = 1
              , content = ''
              , classN = ''
            if(myFun.utils.judgmentSelector(target) || flag) { // 判断是否为iframe弹窗
                !flag ? classN += target.slice(1) : '';  // 加上调用选择器
                $(target)[0].classList.length != 0 ? classN += ' ' + $(target)[0].className + ' ' : classN += ' ';  // 加上类选择器
                content = target.toString().indexOf('#') == -1 ? target : $(target).html();
            } else {
                type = 2;
				classN += 'layer-window-padding ';
                content = [target, 'no'];
            }
            switch(areaType){
                case 'big': classN += 'layer-big'; area='1000px'; break;
                case 'normal': classN += 'layer-normal'; area='740px'; break;
                case 'small': classN += 'layer-small'; area='480px'; break;
                default: classN += 'layer-option'; area=areaType; break;
            }
            index = layer.open({
                type: type,
                title: title,
                area: area,
                shadeClose: false,
                skin: 'layer-window '+classN,
                content: content,
                resize:false,//不能拉伸
                scrollbar:false,
                moveOut: false,
                success: function(layero, index) {
					var iframeDom = '';
					if(!myFun.utils.judgmentSelector(target) && !flag) { // 打开iframe弹窗
						iframeDom = layer.getChildFrame('body', index); // 获取到iframe的DOM
                        layer.iframeAuto(index);
                        iframeDom.find('.audit-apply-container').addClass('scrollbar');
						iframeDom.find('.btn-group').length === 0 ?
                            (iframeDom.find('.audit-apply').addClass('is-padding'),iframeDom.find('.audit-apply-container').css('max-height', '90vh')) :
                            iframeDom.find('.audit-apply-container').css('max-height', '86vh');
						if(layero.height() >= (document.documentElement.clientHeight * 0.8)) {
							layero.find('iframe').css('max-height', '80vh');
							iframeDom.css('overflow-y', 'hidden');
							layer.iframeAuto(index);
						}
						layer.style(index, {
							top: (document.documentElement.clientHeight - layero[0].clientHeight) / 2 + 'px',
						})
					}
                    for(var key in myFun.componentFun){
                        if(myFun.componentFun.hasOwnProperty(key)){
                    　　　　myFun.componentFun[key](layero);
                    　　}
                    }
                    try {
                        if(zFun != undefined) {
                            for(var key in zFun.componentFun) {
                                if(zFun.componentFun.hasOwnProperty(key)) {
                                    zFun.componentFun[key](layero);
                                }
                            }
                        }
                    } catch (error) {
                        console.log("zFun未找到！");
                    }
                    if($('#layerdata').length !== 0) myFun.layer.layerdata1('#layerdata');

					$(iframeDom || layero).on("click", ".my-btn-green", function() {
					    // loadingIndex = myFun.layer.loading();
					});
                    $(iframeDom || layero).on('click','.my-btn-cancel',function(){
                        layer.close(index)
                    })
                    success && success.call(layer,layero,index,iframeDom);
                },
                yes:function(index, layero){
                    // console.log(obj)
                    // obj.success();
                },
                btn2:function(index, layero){

                },
                end:function(){
                    // console.log("end");
                    layer.close(loadingIndex);
                    end && end();
                }
            });
            // return obj;
            return index;
        },
        /**
         * 日期控件（年、月、日）
         * @param {String} target：类、ID、标签选择器
         */
        layerdata1:function(target, type="date", format = 'yyyy/MM/dd'){
            //委托时间
            laydate.render({
                elem: target, //指定元素
                // theme: '#0078FF',
                type: type,
                format: format,
                value: $(target).val(),
                ready: function(date) {
                    dataValidatorDebounce(this.elem[0]); // 日期非空验证点击事件
                },
                done: function(value, date, endDate){
                    myFun.utils.dateValida(this.elem[0]); // 日期非空验证
                    var element = document.querySelector(target).parentNode;
                    var callBack = element.getAttribute("@click-list");
                    callBack && eval(callBack).call(element, value, date, endDate);
                }
            });
        },
        /**
         * 双日期控件（年、月、日）
         * @param {String} target：类、ID、标签选择器
         */
        layerdata2:function(target,option){
            //跟进时间
            var ins1 = laydate.render({
                elem: target //指定元素
                ,range: true //或 range: '~' 来自定义分割字符
                ,format: 'yyyy/MM/dd'
                ,isInitValue: true
                ,value: $(target).val(),
                // ,theme: '#0078FF'
                option
                ,btnsName:[
                    {name:'今天',type:'now'},
                    {name:'近3天',type:'near2'},
                    {name:'近7天',type:'near6'},
                    {name:'7天前',type:'ago7'},
                ]//自定义按钮组件
                ,ready: function(date){
                    myFun.bindBtnsName.call(this,this.elem[0]);//绑定时间事件
                    dataValidatorDebounce(this.elem[0]); // 日期非空验证点击事件
                }
                ,change: function(value, date, endDate) {
                    console.log(value, date, endDate);
                }
                ,done: function(value, date, endDate){
                    // let firstDate = value.split('-')[0], lastDate = value.split('-')[1];
                    // if(new Date(firstDate) > new Date(lastDate)) {
                    //     value = `${lastDate} - ${firstDate}`;
                    //     // date = new Date(lastDate);
                    //     // endDate = new Date(firstDate);
                    // }
                    this.value = this.elem[0].value = value;
                    myFun.utils.dateValida(this.elem[0]); // 日期非空验证
                    var element = document.querySelector(target).parentNode;
                    var callBack = element.getAttribute("@click-list");
                    callBack && eval(callBack).call(element, value, date, endDate);
                }
            });
        },
        /**
         * 时间控件（小时、分钟）
         * @param {String} target：类、ID、标签选择器
         */
        layerdata3:function(target){
            var btns = ["clear", "now", "confirm"];
            if(document.querySelector(target).classList.contains("integral-point")) btns.splice(1,1);
            //小时/分钟
            laydate.render({
                elem: target //指定元素
                // ,theme: '#0078FF'
                ,type: 'time'
                ,format: 'HH:mm'
                ,btns: btns
                ,ready: function(date) {
                    dataValidatorDebounce(this.elem[0]); // 日期非空验证点击事件
                    // 添加.min-minutes类名，防止污染全局时间组件
                    $(".layui-laydate").addClass("min-minutes");
                    // 加上.integral-point类名的为整点时间类型
                    if(this.elem[0].className.indexOf("integral-point") !== -1) {
                        var $olMinutes = $(".layui-laydate .laydate-time-list>li:eq(1)>ol");
                        $olMinutes.find("li:not(:eq(0)):not(:eq(29))").remove();
                    }
                }
                ,done: function(value, date, endDate){
                    this.elem[0].value = value;
                    myFun.utils.dateValida(this.elem[0]); // 日期非空验证
                    var element = document.querySelector(target).parentNode;
                    var callBack = element.getAttribute("@click-list");
                    callBack && eval(callBack).call(element, value, date, endDate);
                },
            });
        },
        /**
         * 日期与时间控件（年、月、日 小时、分钟、秒）
         * @param {String} target：类、ID、标签选择器
         */
        layerdata4:function(target){
            //日期时间选择器
            laydate.render({
                elem: target //指定元素
                // ,theme: '#0078FF'
                ,type: "datetime"
                ,format: 'yyyy/MM/dd HH:mm:ss'
                ,ready: function(date) {
                    dataValidatorDebounce(this.elem[0]); // 日期非空验证点击事件
                }
                ,done: function(value, date, endDate){
                    this.elem[0].value = value;
                    myFun.utils.dateValida(this.elem[0]); // 日期非空验证
                    var element = document.querySelector(target).parentNode;
                    var callBack = element.getAttribute("@click-list");
                    callBack && eval(callBack).call(element, value, date, endDate);
                },
            });
        },
        /**
         * 双日期时间控件（年、月、日、时、分、秒）
         * @param {String} target：类、ID、标签选择器
         */
        layerDoubleDateTime:function(target,option,afterChangeHandler){
            //跟进时间
            var ins1 = laydate.render({
                elem: target, //指定元素
                type: "datetime",
                range: true, //或 range: '~' 来自定义分割字符
                format: 'yyyy/MM/dd HH:mm:ss' ,
                isInitValue: true,
                value: $(target).val(),
                ...option,
                // ,theme: '#0078FF'
                btnsName:[
                    {name:'今天',type:'now'},
                    {name:'近3天',type:'near2'},
                    {name:'近7天',type:'near6'},
                    {name:'7天前',type:'ago7'},
                ],//自定义按钮组件
                ready: function(date){
                    myFun.bindBtnsName.call(this,this.elem[0]);//绑定时间事件
                    dataValidatorDebounce(this.elem[0]); // 日期非空验证点击事件
                },
                change: function(value, date, endDate) {
                    afterChangeHandler && eval(afterChangeHandler).call(ins1, value, date, endDate);
                },
                done: function(value, date, endDate){
                    // let firstDate = value.split('-')[0], lastDate = value.split('-')[1];
                    // if(new Date(firstDate) > new Date(lastDate)) {
                    //     value = `${lastDate} - ${firstDate}`;
                    //     // date = new Date(lastDate);
                    //     // endDate = new Date(firstDate);
                    // }
                    this.value = this.elem[0].value = value;
                    myFun.utils.dateValida(this.elem[0]); // 日期非空验证
                    var element = document.querySelector(target).parentNode;
                    var callBack = element.getAttribute("@click-list");
                    callBack && eval(callBack).call(element, value, date, endDate);
                }
            });
        },
        msg: function(text,icon,timer){
            var skin = null;
            switch (icon) {
                case 0: icon = 0,skin=5; break;   //不需要iconfont
                case 1: icon = '错误'; break;
                case 3: icon = '警告'; break;
                case 4: icon = 'icontishi'; break;//提示
                case 2: ;//成功
                default: icon = 'iconUtubiao-12'
            }
            if(icon===0){
                layer.msg(text,{time: timer|| 2000,icon: skin});
            }else
                layer.msg('<i class=\'iconfont icon1 '+icon+'\'></i>'+text,{time: timer|| 2000});
            layer.close(loadingIndex);
        },
        loading:function(){
            var index = layer.load(2, {
                // shade: [0.6,'#fff'], //0.1透明度的白色背景
                content:'正在加载中',
                skin:'layer-loading',
                area: ['160px'],
            });
            return index;
        },
        //打开房源信息详情
        openMsgs:function(target,success){
            index = layer.open({
                type: 1,
                title: '房源信息概览',
                area: ['660px','680px'],
                shadeClose: true,
                shade: 0.01,
                skin: 'layer-window house-apply',
                content: target.indexOf('#')==-1?target:$(target).html(),
                resize:false,//不能拉伸
                scrollbar:true,
                offset: 'rb',
                moveOut: false,
                success: function(layero, index) {
                    success && success.call(layer,layero,index)
                    for(var key in myFun.componentFun){
                        if(myFun.componentFun.hasOwnProperty(key)){
                    　　　　myFun.componentFun[key](layero);
                    　　}
                    }
                    try {
                        if(zFun != undefined) {
                            for(var key in zFun.componentFun) {
                                if(zFun.componentFun.hasOwnProperty(key)) {
                                    zFun.componentFun[key](layero);
                                   }
                            }
                        }
                    } catch (error) {
                        console.log("zFun未找到！");
                    }
                    // myFun.componentFun.checks('.layer-window');
                    // myFun.componentFun.select('.layer-window');
                    if($('#layerdata').length !== 0) myFun.layer.layerdata1('#layerdata');

                    $('.layer-window').on('click','.my-btn-cancel',function(){
                        layer.close(index)
                    })
                },
            });
        },
        // 留存-打电话
        retainPhone:function(target,even,success,end){
            var j = {
                type: 1,
                title:false,
                content: target.indexOf('#')==-1?target:$(target).html(),
                area: '440px',
                skin: 'layer-retain-call',
                resize:false,//不能拉伸
                moveOut: false,
                success: function(layero, index) {
                    success && success.call(layer,layero,index);
                    $(".js-close-btn").on("click",function(){
                        layer.close(index);
                    });
                    if(even.hasClass('js-add-contact')){
                        myFun.componentFun.select('body');
                    }
                },
                end:function(){
                    end && end();
                }
            }

            if(!even.hasClass('js-add-contact')){
                j.offset =  [
                    even.offset().top - $(window).scrollTop()
                    ,even.offset().left - 460 - $(window).scrollLeft()
                  ];
                j.shade = 0;
                j.fixed= false;
                j.zIndex=80;
            }else{
                j.scrollbar = false;
                j.area='600px';
            }

            index = layer.open(j);
        }
    },
    //组件绑定事件（基本）
    componentFun:{
        tableClick: function(target) {
            $(target).on('click','.table-item:not(.table-title)', function(e) {
                $(this).addClass('table-click').siblings('.table-click').removeClass('table-click');
            });
        },
        select:function(target){
            // 绑定select点击事件
            $(target).find('.my-select-btn').click(function(e){
                e = e || window.event;
                e.stopPropagation();
                var self = $(this);
                (!self.parent().hasClass('my-options-area') && document.body.clientHeight - getH(self[0]) - 200 < 0) ?
                    self.parent('.my-select').addClass('bottom') : self.parent('.my-select').removeClass('bottom')
                // console.log(self[0].getBoundingClientRect().top)
                $('.my-input-list').stop().hide();
                $('.my-search-list').stop().hide();
                $('.my-select-list').not($(this).siblings('.my-select-list')).stop().hide(0);
                $('.my-select-list-child').not($(this).siblings('.my-select-list-child')).stop().hide();
                $('.my-select').removeClass('on');
                $(this).siblings('.my-select-list').stop().toggle(0,function(a){
                    // console.log($(this))
                    $(this).css('display')=='none'?self.parent().removeClass('on'):self.parent().addClass('on');
                    $(this).siblings(".my-select-list-child").hide();
                    if($(this).find("li").length <= 0) {
                        $(this).html('<p class="found">无数据</p>');
                    }
                });
                //关闭日历控件
                console.log($(this).parent().hasClass('my-timer'))
                !$(this).parent().hasClass('my-timer') && $('.layui-laydate').remove();
            })
            $(target).on('click','.my-select1 .my-select-list',function(e){
                e = e || window.event;
                e.stopPropagation();
                var event = e || window.event;
                var target = event.target || event.srcElement;
                var funName = $(this).parent().attr('@click-list');
                var clor = $(this).prev().find(".js-cl-999");
                // console.log(target)
                if(target.className.indexOf('my-select-list') != -1){return false;}
                if(target.classList.contains('found')) return;

                if($(this).parent().removeClass('on').find('.btn-text').length>0){
                    $(this).parent().removeClass('on').find('.btn-text').addClass("active").text(target.textContent)
                }else{
                    $(this).parent().removeClass('on').find('.btn-inputs-val').addClass("active").val(target.textContent)
                }
                $(this).stop().hide(0);
                $(this).children('li').removeClass('on');
                $(target).addClass('on');
                if(funName !== undefined) eval(funName).call(this,e);
                if(clor.length>0){
                    clor.removeClass("js-cl-999 cl-999").removeClass("");
                }
            })
            //多选选项
            // console.log($(target).find('.my-select2'))
            // //绑定下拉组件交互
            // $(target).find('.my-select2').each(function(index,item){
            //     var text = $(item).find('.my-select-btn').attr('data-text') || $(item).find('.my-select-btn .btn-text').html();
            //     var selectList = $(item).find('.my-select-content').size()===0? $(item).find('.my-select-list'):$(item).find('.my-select-content');
            //     var eleHTML = '<li class="check-all"><div class="my-label"><div class="my-checks"><i class="iconfont iconUtubiao-12"></i></div><span class="li-text">'+text+'</span></div></li>';
            //     selectList.find('.check-search').size() === 0?selectList.prepend(eleHTML):selectList.find('.check-search').after(eleHTML)
            // })
            // $(target).find('.my-select2 .my-select-list li')
            $(target).find('.my-select2 .my-select-list').on('click','li',function(e){
                e = e || window.event;
                e.stopPropagation();
                var text = '',
                    checkAll = true,
                    btnText = $(this).parent('.my-select-list').size() == 0 ? $(this).parent().parent().siblings('.my-select-btn').find('.btn-text'):$(this).parent().siblings('.my-select-btn').find('.btn-text');
                if($(this).hasClass('check-search')) return;
                if($(this).hasClass('check-all')){//点到全选
                    if($(this).find('.my-checks').hasClass('on')){
                        $(this).parent().children(':not(.check-all):not(.check-search)').each(function(index,item){
                            var checks = $(item).find('.my-checks');
                            checks.addClass('on')
                            if(text == ''){
                                // text += checks.siblings('.li-text').text();
                                text += '全部';
                            }else{
                                // text += '、' + checks.siblings('.li-text').text();
                            }
                        })
                    }else{
                        $(this).parent().children(':not(.check-all):not(.check-search)').find('.my-checks').removeClass('on')
                        text = '';
                    }
                    checkAll = false;
                } else {
                    $(this).parent().children(':not(.check-search)').each(function(index,item){
                        if($(item).hasClass('check-all')) {
                            $(item).find('.my-checks').removeClass('on');
                            text = text.replace('全部、', '');
                            return;
                        }
                        if($(item).find('.my-checks').hasClass('on')){
                            if(text == ''){
                                text += $(item).find('.li-text').text();
                            }else{
                                text += '、' + $(item).find('.li-text').text();
                            }
                        }
                    })
                    checkAll = false;
                }
                // console.log(checkAll)
                // var texts = ;
                checkAll?btnText.html(btnText.removeClass("active").attr('default-text') || ''):btnText.addClass("active").html(text).attr('title',text);
            })

            $(target).find('.my-select2 .search').on('keyup',null,jieliu(function(e){
                e = e || window.event;
                var th = $(this);
                var currText = th.val();
                var selectList = th.parent().parent().parent();
                var cloneEles = null;
                var isNull = false;//判断是否搜索无结果 true为有结果
                console.log(selectList)
                if(currText == ''){//输入为空
                    selectList.children('.my-select-content').children(':not(.check-search)').each(function(index,item){
                        $(item).show();
                        var childText = $(item).find('.li-text');
                        // console.log(childText.attr('data-text'))
                        childText.text(childText.attr('data-text'));
                    })
                    selectList.find('.no-data').hide();
                    return;
                }
                selectList.children('.my-select-content').children(':not(.check-search)').each(function(index,item){
                    var childText = $(item).find('.li-text');
                    if(childText.text().indexOf(currText)!==-1){
                        childText.html(childText.text().replace(currText,'<span class="green">'+currText+'</span>'));
                        // cloneEles = $(item).clone(true).show().addClass('search-item');
                        // cloneEles.find('.li-text').html(childText.replace(currText,'<span class="green">'+currText+'</span>'))
                        // selectList.append(cloneEles)
                        // console.log(cloneEles)
                        $(item).show();
                        isNull = true;
                    }else{
                        $(item).hide();
                    }
                })
                if(!isNull) {
                    if(selectList.find('.no-data').size() === 0)
                        selectList.find('.my-select-content').append('<div class="no-data">搜索无结果</div>');
                    else selectList.find('.no-data').show();
                }
            },333))

            // $(target).find('.my-select2 .my-select-list .my-checks').on('click',null,function(e){
            //     e = e || window.event;
            //     e.stopPropagation();
            // })
            // 下拉列表验证非空
            $(target).on('blur', '.my-select.monitor:not(.my-double-select)', function(e) {
                var event = e.event || window.event;
                e.stopPropagation();
                if($(this).hasClass('disabled')) return;
                if(!$(this).find(".my-select-list>li").hasClass("on")) {
                    $(this).addClass('error');
                    blurChange(this, '必选项不能为空');
                }else {
                    $(this).removeClass('error');
                }
            })
            // 双下拉列表验证非空
            $(target).on('blur', '.my-double-select.monitor', function(e) {
                var event = e.event || window.event
                  , value = $(this).find("input[type=hidden]").val();
                e.stopPropagation();
                if($(this).hasClass('disabled')) return;
                if(!myFun.utils.IsNotEmpty(value)) {
                    $(this).addClass('error');
                    blurChange(this, '必选项不能为空');
                }else {
                    $(this).removeClass('error');
                }
            })
        },
        doubleSelect: function(target) {
            var index = 0;
            // 双行下拉列表
            $(target).on('click','.my-double-select .my-select-list-parent',function(e){
                e = e || window.event;
                e.stopPropagation();
                var event = e || window.event
                  , target = event.target || event.srcElement;
                target.classList.contains('iconfont') ? target = target.parentElement : '';
                var value = '';
                var funName = $(this).parent().attr('@click-list');
                if(target.classList.contains('my-select-list-parent')) return;
                if(target.classList.contains('found')) return;
                $(this).parent().removeClass('on').find('.btn-text').addClass("active").text(target.textContent);
                $(this).stop().hide(0);
                $(this).children('li').removeClass('on');
                $(target).addClass('active').siblings().removeClass('active');
                // $(target).addClass('on');
                $(this).siblings('.my-select-list-child').eq($(target).index()).hide();
                value = $(this).parent().find('.my-select-btn>.btn-text').text();
                $(this).parent().find("input[type=hidden]").val(value);
                if(funName !== undefined) eval(funName).call(target, value);
            })

            $(target).on('mouseover', '.my-select-list-parent li', function(e) {
                // console.log(e.relatedTarget);
                var event = event || window.event;
                var top = 0
                  , bottom = 0;
                var $listChild = $(this).parent().parent().children('.my-select-list-child').hide();
                if($(this).find(".iconfont").length <= 0) return;
                var $child = $listChild.eq($(this).index());
                $(this).removeClass('on').siblings().removeClass('on');
                $(this).parent().parent().hasClass('bottom')
                    ? ( bottom = (42 + 100 - $(this).position().top), top = 'auto' )
                    : ( bottom = 'auto', top = $(this).position().top + 32);
                $child.show().css({'top': top, 'bottom': bottom});
            });

            $(target).on('mouseover', '.my-select-list-child', function() {
                index = $(this).parent().find('.my-select-list-child').index(this);
                $(this).siblings(".my-select-list-parent ").find('li').eq(index).addClass('on').siblings().removeClass('on');
            });

            $(target).on('click', '.my-select-list-child li', function(e) {
                var event = e || window.event
                  , target = event.target || event.srcElement
                  , value = '';
                var funName = $(this).parent().parent().attr('@click-list');
                var $selectChild = $(this).parent().hide();
                var $selectParent = $selectChild.siblings('.my-select-list-parent').find("li").eq(index);
                var parentText = $selectParent.text();
                $(this).parent().siblings(".my-select-list-parent").find('li').eq(index).addClass('active').siblings().removeClass('active');
                $(this).parents('.my-double-select').find(".my-select-list-child").find('.active').removeClass('active');
                $(this).addClass('active');
                $selectChild.siblings('.my-select-btn').find('.btn-text').addClass("active").text(parentText + '-' +e.target.textContent);
                value = $(target).parent().parent().find('.my-select-btn>.btn-text').text();
                $(this).parent().parent().find("input[type=hidden]").val(value);
                if(funName !== undefined) eval(funName).call(this, value, $selectParent, $selectChild);
            });
        },
        checks:function(target){
            $(target).find('.my-check-one').on('click',function(e){
                e = e || window.event;
                var checkName = $(this).attr('check-box');
                // console.log(checkName)
                if(checkName === undefined) $(this).addClass('on');
                else{
                    $('.my-check-one').each(function(index,item){
                        // console.log(item.getAttribute('check-box'))
                        if(item.getAttribute('check-box') == checkName){
                            $(item).removeClass('on');
                        }
                    })
                }
                $(this).addClass('on');
            })
            $(target).find('.my-checks:not(.disabled)').on('click',function(e){
                $(this).toggleClass('on');
                // $(this).hasClass('on')?$(this).parent().siblings('.option-content').find('.my-checks').addClass('on'):$(this).parent().siblings('.option-content').find('.my-checks').removeClass('on')
            })
            // $(target).find('.my-check').on('click',function(e){
            //     e = e || window.event;
            //     e.stopPropagation();
            //     $(this).toggleClass('on');
            // })
            //另一个单选框样式
            $(target).find('.my-check:not(.disabled)').on('click',function(e){
                $(this).toggleClass('on')
            })
        },
        tabs:function(target){
            //切换按钮
            $(target).find('.navbar1').on('click','.nav-item1',function(){
                var nav_item = ''
                  , nav_tab = ''
                  , success = '';
                $(this).parent().children('.nav-item1').removeClass('on');
                nav_item = $(this).addClass('on');
                nav_tab = $(this).parent().siblings('.nav-tab').hide().eq($(this).index()).show();
                success = $(this).parent().attr("click-handler");
                success && eval(success).call(this, nav_item, nav_tab);
                // console.log($(this))
            });
            // console.log($(target))
            // $(target).find('.navbar1 .nav-item1:first').click();
            $(target).find('.navbar1').each(function(index,item){
                $(item).find('.nav-item1:first').click()
            })
        },
        switch:function(target){
            $(target).find('.a-switch').on('click',function(){
                var success = $(this).parent().attr('@click-list');
                $(this).toggleClass('on');
                success && eval(success).call($(this).parent(), $(this).hasClass('on'));
            })
        },
        label:function(target){
            $(target).find('.my-label:not(.disabled)').on('click',function(e){
                if(e.target.className.indexOf('my-check') === -1 && e.target.parentElement.className.indexOf('my-check') === -1){
                    $(this).find('[class*="my-check"]').click()
                }
                var success = $(this).attr("@click-list");
                success && eval(success).call(this, e, $(this).text());
            })
        },
        inputs:function(target){
            // 输入框禁止输入空格
            $(target).on('blur', '.empty', function(e) {
                return $(this).val($.trim($(this).val()));
            })
            // 只能输入数字(包括2位小数)
            $(target).on('input', '.number', function(e) {
                // this.value = this.value.replace(/^[^0-9]|[0-9]{7}|[0-9]+\.?[0-9]{3,}?$/, '');
                // this.value = this.value.replace(/[^\d\.]/, "");
                // if(/^[0-9]+(\.[0-9]{3,})$/.test(this.value)) {
                //     this.value = this.value.substring(0,this.value.indexOf(".")+3);
                // }
                // if(this.value.match(/\./g)) {
                //     if(this.value.match(/\./g).length >= 2) {
                //         this.value = this.value.replace(/\.$/,"");
                //     }
                //     if(!/\d/.test(this.value) && this.value.match(/\./g).length == 1) {
                //         this.value = '';
                //     }
                // } else if(this.value.match(/0/g).length > 1) {
                //     this.value = parseFloat(this.value || 0);
                // }
                if(!/^[0-9]+(\.[0-9]{0,2})?$/.test(this.value)) {
                    this.value = this.value.replace(/[^\d\.]/, "");
                    // this.value = this.value.substring(0,this.value.indexOf(".") + this.value.lastIndex("."));
                    this.value = this.value.match(/\./g).length > 1 ? this.value.substring(0,this.value.lastIndexOf("."))
                        : this.value.substring(0, this.value.indexOf(".")+3);
                };
            })
            // 只能输入数字(包括2位小数)
            $(target).on('blur', '.number', function(e) {
                this.value = parseFloat(this.value || 0);
            })
            $(target).on('paste', '.number', function(e) {
                return false;
            })
            // 只能输入数字(不包括小数)
            $(target).on('input', '.pnumber', function(e) {
                this.value == ''
                    ? this.value = 0
                    : '';
                this.getAttribute('min')
                    ? (parseInt(this.value) < parseInt(this.getAttribute('min'))
                    ? this.value = 0 : '' ) : '';
                this.getAttribute('max')
                    ? (parseInt(this.value) > parseInt(this.getAttribute('max'))
                    ? this.value = 100 : '' ) : '';
                this.value = this.value.replace(/\D/, '');
                this.value = parseInt(this.value);
            })
            // 输入框非空
            $(target).find('.my-input').on('blur','.monitor',function(e){
                var self = this;
                if($(this).hasClass('disabled')) return;
                if($(this).hasClass('delay')) {
                    setTimeout(function() {
                        if(!myFun.utils.IsNotEmpty(self.value)){
                            $(self).parent('.my-input').addClass('error');
                            blurChange(self, '必填项不能为空');
                        }else{
                            $(self).parent('.my-input').removeClass('error');
                        }
                    }, 200)
                } else {
                    if(!myFun.utils.IsNotEmpty(this.value)){
                        $(this).parent('.my-input').addClass('error');
                        blurChange(this, '必填项不能为空');
                    }else{
                        $(this).parent('.my-input').removeClass('error');
                    }
                }
            })
            // select部门员工下拉非空验证
            $(target).find('.my-select-btn').on('blur','.inputs.monitor',function(e){
                var self = this;
                if($(this).hasClass('disabled')) return;
                if($(this).hasClass('delay')) {
                    setTimeout(function() {
                        if(!myFun.utils.IsNotEmpty(self.value)){
                            $(self).parents(".my-select-btn").addClass('error');
                            blurChange(self, '必填项不能为空');
                        }else{
                            $(self).parents(".my-select-btn").removeClass('error');
                        }
                    }, 200)
                } else {
                    if(!myFun.utils.IsNotEmpty(this.value)){
                        $(this).parents(".my-select-btn").addClass('error');
                        blu
                        rChange(this, '必填项不能为空');
                    }else{
                        $(this).parents(".my-select-btn").removeClass('error');
                    }
                }
            })
            // 本文框非空
            $(target).on('blur','.textarea.monitor',function(e){
                var e = e || window.event;
                e.stopPropagation();
                var self = this;
                if($(this).hasClass('disabled')) return;
                if(!myFun.utils.IsNotEmpty(this.value)){
                    $(this).parent().addClass('error');
                    blurChange(this, '必填项不能为空');
                }else{
                    $(this).parent().removeClass('error');
                }
            })
            $(target).find ('.clear').on('keyup','.inputs',function(){
                // console.log($(this))
                $(this).val() != ''?$(this).parent('.clear').addClass('clear-js'):$(this).parent('.clear').removeClass('clear-js')
            })
            /*
             * 元素验证器，绑定blur事件进行正则验证，并进行相应操作
             * @paramter element 要求验证的元素
             * @paramter regExpName utils中正则名称
             * @paramter errorMsg 报错提示
             */
            function validationHandler(element, regExpName, errorMsg) {
                $(element).each(function(index, ele) {
                    $(ele).blur(function() {
                        var _val = $(this).val().trim();
                        if(_val === '') return;
                        if(myFun.utils[regExpName](_val)){
                            $(this).parent().removeClass('error');
                            return true;
                        } else {
                            $(this).parent().addClass('error');
                            blurChange(this, errorMsg);
                            return false;
                        }
                    });
                });
            }
            validationHandler('.identityCard', 'IsIdentityCard', '身份证格式有误!');
            validationHandler('.telphone', 'IsTelCode', '手机号格式有误!');
            validationHandler('.email', 'IsEmail', '邮箱格式有误!');
            validationHandler('.social', 'IsSocial', '社保格式有误!');
            validationHandler('.back-card', "IsBackCard", "银行卡号格式有误!");
            $(target).find ('.my-input').on('click','.close-js',function(){
                $(this).siblings('.inputs').val('').keyup();
            })
        },
        //展开收起
        fold:function(target){
            var target = $(target);
            target.find('.put-on').on('click',null,function(){
                var putOnContent = $(this).siblings('.put-on-content')
                  , handler = $(this).attr("call-back") ;
                var parent = putOnContent.size() == 0? $(this).parent().siblings('.put-on-content'):putOnContent;
                parent.slideToggle(300,function(){
                    $(this).css('overflow','visible');
                    handler && eval(handler).call(this);
                });
                $(this).toggleClass('on');
            })
        },
        tips:function(target) {
            var tips_index = 0
              , max_size = 0;
            $(target).find('.tips').on('mouseenter', null, function() {
                max_size = myFun.utils.IsNotEmpty($(this).attr("maxsize"))
                    ? $(this).attr("maxsize")
                    : 15;
                if($(this).text().length > max_size) {
                    tips_index = layer.tips(this.innerText, this, {
                        tips: [1, '#FFF', 'word-break: break-word'],
                        tipsMore: false,
                        time: 0
                    });
                }
                $(this).attr('tips_index', tips_index);
            }).on('mouseleave', null, function() {
                layer.close($(this).attr('tips_index'));
            });
        },
        tipslength: function(target) {
            var $tips_parent = ''
              , $tips_child = ''
              , tips_index = 0
              , value = '';
            $(target).find('.tip').on('mouseenter', null, function() {
                $tips_parent = $(this);
                $tips_child = $(this).children("p,span,input");
                value = $tips_child.is('input') ? $tips_child.val() : this.innerText;
                if($tips_child.is('input') && myFun.utils.IsNotEmpty(value))  {
                    tips_index = layer.tips(myFun.utils.TagsReplace(value || ''), this, {
                        tips: [1, '#FFF', 'word-break: break-word'],
                        tipsMore: false,
                        time: 0
                    })
                }
                if($tips_child.is('p,span') && ($tips_parent.width() - 10) <= $tips_child.outerWidth()) {
                    tips_index = layer.tips(myFun.utils.TagsReplace(value || ''), this, {
                        tips: [1, '#FFF', 'word-break: break-word', 'min-width: 300px'],
                        tipsMore: false,
                        time: 0
                    })
                }
                $(this).attr('tips_index', tips_index);
            }).on('mouseleave', null, function() {
                layer.close($(this).attr('tips_index'));
            });
        }
    },
    utils: {
        // 格式化时间
        dateFormat: function(date, format) {
            var dates = new Date(date),
                dateTime= {
                'y+': dates.getFullYear(),
                'M+': dates.getMonth()+1,
                'd+': dates.getDate(),
                'H+': dates.getHours(),
                'm+': dates.getMinutes(),
                's+': dates.getSeconds(),
            }
            if(/(y+)/i.test(format)) {
                format = format.replace(RegExp.$1, (''+dateTime['y+']).substr(4 - RegExp.$1.length));
            }
            for(let key of Object.keys(dateTime)) {
                if(new RegExp("("+key+")").test(format)) {
                    format = format.replace(RegExp.$1, RegExp.$1.length == 1 ? date[key] :
                            ("00"+dateTime[key]).substr((''+dateTime[key]).length))
                }
            }
            return format;
        },
        // 创建一个唯一id
        createUniqueId: function(n) {
            var random = function() {
                return Number(Math.random().toString().substr(2)).toString(36);
            }
            var arr = [];
            function createId() {
                var num = random();
                var _bool = false;
                arr.forEach(v => {
                    if(v === num) _bool = true;
                });
                if(_bool) {
                    createId();
                } else {
                    arr.push(num);
                }
            }
            var i = 0;
            while(i < n) {
                createId();
                i++;
            }
            return arr;
        },
        // 判断是否为汉字
        IsHanZi: function(text) {
            var reg = /^[\u4e00-\u9fa5]{0,}$/;
            return reg.test(text);
        },
        // 判断是否为ID选择器、类选择器
        judgmentSelector: function(selector) {
            var reg = /^[#|.]/;
            return reg.test(selector);
        },
        /* 获取当前的时间 */
        now() {
            return +new Date();
        },
        /* 日期非空验证点击事件 */
        dateValidatorClick: function(ele) {
            $(window).one("click", function() {
                myFun.utils.dateValida(ele);
            })
        },
        /* 日期非空验证 */
        dateValida: function(ele) {
            if($(ele).hasClass('disabled')) return;
            if(!$(ele).hasClass('monitor')) return;
            if(!myFun.utils.IsNotEmpty(ele.value)){
                $(ele).parent().addClass('error');
                blurChange(ele, '必填项不能为空');
            }else{
                $(ele).parent().removeClass('error');
            }
        },
        /* 验证身份证格式 /(^\d{15}$)|(^\d{18}$)|(^\d{17}(\d|X|x)$)/*/
        IsIdentityCard: function(str) {
            var reg = /^\d{6}(18|19|20)?\d{2}(0[1-9]|1[012])(0[1-9]|[12]\d|3[01])\d{3}(\d|X)$/i;
            return reg.test(str);
        },
        /*校验电话码格式 */
        IsTelCode: function(str) {
            var reg = /^((0\d{2,3}-\d{7,8})|(1[3584]\d{9}))$/;
            return reg.test(str);
        },
        /*校验邮件地址是否合法 */
        IsEmail: function(str) {
            var reg = /^\w+@[a-zA-Z0-9]{2,10}(?:\.[a-z]{2,4}){1,3}$/;
            return reg.test(str);
        },
        /*验证是为非空 */
        IsNotEmpty: function(str) {
            var reg = /\S/;
            return reg.test(str);
        },
        /*验证是否为银行卡号 */
        IsBackCard: function(str) {
            var reg = /^([1-9]{1})(\d{15}|\d{16}|\d{17}|\d{18})$/;
            return reg.test(str);
        },
        /*验证是否为社保账号 */
        IsSocial: function(str) {
            var reg = /^[\d]{3}-[\d]{2}-[\d]{4}$/;
            return reg.test(str);
        },
        /* 标签转文本 */
        TagsReplace: function(str) {
            const tagsToReplace = {
                '&': '&amp',
                '<': '&lt',
                '>': '&gt'
            };
            function replaceTag(tag) {
                return tagsToReplace[tag] || tag;
            }
            return str.replace(/[&<>]/g, replaceTag);
        }
    },
    // 表格头部固定
    // tableScrollFixed:function(){

    //     var childV = ".ele-scroll";
    //     var fixedV = ".";
    //     var addClassName = "table-fixed-top";
    //     var tableLeft = 0;
    //     var tableRight = 0;

    //     if(!($(childV).length > 0 && $(fixedV).length > 0) ){
    //         return false
    //     }

    //     tableLeft = $(childV).offset().left;
    //     tableRight = $(window).width() - $(childV).offset().left - $(childV).width();

    //     $(window).off("scroll",winScroll).on("scroll",winScroll);

    //     function winScroll(){
    //         var tableTop = $(fixedV).offset().top;
    //         var tableHeight = $(childV).height();
    //         var winScroll = $(window).scrollTop();
    //         var topNavH = $(".header-content").height();

    //         if(winScroll >= tableTop - topNavH ){
    //             $(fixedV).css({
    //                 "padding-top":tableHeight
    //             })
    //             //头部固定
    //             $(childV).addClass(addClassName).css({
    //                 "left":tableLeft,
    //                 "right":tableRight
    //             });
    //         }else{
    //             // 不固定
    //             $(fixedV).removeAttr("style");
    //             $(childV).removeClass(addClassName).removeAttr("style");
    //         }
    //     }
    // },
    init: function(){
        this.windowClick();
        this.first();
        this.ELementScroll();
        // this.tableScrollFixed();
        for(var key in this.componentFun){
            if(this.componentFun.hasOwnProperty(key)){
        　　　　this.componentFun[key]('body');
        　　}
        }
    }
};
function debounce(method,delay) {
    let timer = null;
    return function () {
        let self = this,
            args = arguments;
        timer && clearTimeout(timer);
        timer = setTimeout(function () {
            method.apply(self,args);
        },delay);
    }
}
/**
 * 防抖函数，返回函数连续调用时，空闲时间必须大于或等于wait,func才会执行
 * @param {function} func       回调函数
 * @param {number} wait         表示func执行的间隔
 * @param {boolean} immediate   设置为true是，是否立即调用函数
 * @return {function}           返回客户调用函数
 */
function debounce(func, wait = 50, immediate = true) {
    let timer, context, args;

    // 延迟执行函数
    const later = () => setTimeout(() => {
        timer = null;
        if(!immediate) {
            func.apply(context, args);
            context = args = null;
        }
    }, wait);

    // 返回实际调用函数
    return function(...params) {
        if(!timer) {
            timer = later();
            if(immediate) {
                func.apply(this, params);
            } else {
                context = this;
                args = params;
            }
        // 如果已有延迟执行函数（later），调用的时候清楚原来的并重新设定一个
        // 这样做延迟函数会重新计时
        } else {
            clearTimeout(timer);
            timer = later();
        }
    }
}
function jieliu(method,delay){
    var timer = false;
    return function () {
        var self = this,
            args = arguments;
        if(timer) return;
        timer = true;
        method.apply(self,args);
        setTimeout(function () {
            timer = false;
        },delay);
    }
}
/**
 * 节流函数，返回函数连续调用是，func执行频率限定为次/wait
 *
 * @param {function} func       函数
 * @param {number}   wait       表示时间窗口的间隔
 * @param {object}   options    如果想忽略开始函数的调用，传入{leading: false}。
 *                              如果想忽略结尾函数的调用，传入{trailing: false}。
 * @return {function}           返回客户调用函数
 */
function jieliu(func, wait, options) {
    var context, args, result;
    var timeout = null;
    // 之前的时间戳
    var previous = 0;
    // 如果options 没传则设为空对象
    if(!options) options = {};
    // 定时器回调函数
    var later = function() {
        // 如果设置了leading，就将previous设为0
        // 用于下面函数的第一个if判断
        previous = options.leading === false ? 0 : myFun.utils.now();
        // 置空一是为了防止内存泄漏，二是为了下面的定时器判断
        timeout = null;
        result = func.apply(context, args);
        if(!timeout) context = args = null;
    }
    return function() {
        // 获得当前时间戳
        var now = myFun.utils.now();
        // 首次进入前者肯定为true
        // 如果需要第一次不执行函数
        // 就将上次时间戳设为当前的
        // 这样在接下来计算remaining的值时会大于0
        if(!previous && options.leading === false) previous = now;
        // 计算剩余时间
        var remaining = wait - (now - previous);
        context = this;
        args = arguments;
        // 如果当前调用已经大于上次调用时间 + wait
        // 或者用户手动调了时间
        // 如果设置了trailing, 只会进入这个条件
        // 如果没有设置leading, 那么第一次会进入这个条件
        // 还有一点，你可能会觉得开启了定时器那么应该不会进入这个if条件了
        // 其实还是会进入的，应为定时器的延迟
        // 并不是准确的时间,很可能你设置了2秒
        // 但是他需要2.2秒才触发，这时候就会进入这个条件
        if(remaining <= 0 || remaining > wait) {
            // 如果存在定时器就清理掉否则会调用二次回调
            if(timeout) {
                clearTimeout(timeout);
                timeout = null;
            }
            previous = now;
            result = func.apply(context, args);
            if(!timeout) context = args = null;
        } else if(!timeout && options.trailing !== false) {
            // 判断是否设置了定时器和trailing
            // 没有的话就开启一个定时器
            // 并且不能同时设置leading 和 trailing
            timeout = setTimeout(later, remaining);
        }
        return result;
    }
}

//公告初始化文件
$(function(){
    myFun.init();
})
//自定义拖拽效果
function Tdrag(ele,data){
    this.$element = ele;
    this.data = data;
    this.init(ele)
}
Tdrag.prototype = {
    init:function(obj){
        var self = this;
        self.disX = 0;
        self.disY = 0;
        self.tops = [];
        self.obj = obj;
        self.maxLength = 13;
        var Element = '<div class="Tdrag-item"><i class="iconfont iconshangxiayidong Tdrag-handle"></i><span class="text"></span><i class="iconfont iconshanchu close"></i></div>'
        var str = '';
        $.each(self.data,function(index,item){
            self.tops.push({index:index,top: index * 30,text: self.data[index]});
            str += Element;
        })
        $(obj).append(str)
        self.refresh(true);
        console.log(self.tops)
        $(obj).on('mousedown','.Tdrag-item',function(e){self.start(e,this.parentElement.parentElement)})
            .on('mousemove','.Tdrag-item',function(e){self.move(e,this.parentElement.parentElement,this)})
            .on('mouseup','.Tdrag-item',function(e){self.end(e,this.parentElement.parentElement)});
    },
    scroll: function(e) {
        ev = e || window.event;
        ev.preventDefault();
        var self = this;
        if(!self._move) return;
        var target = ev.currentTarget,
            tDragHeight = self.obj.height(),
            oneTop = tDragHeight / 15,
            tDrageTop = getH(self.obj[0]);
            ev.preventDefault(),
            clientY = ev.clientY - tDrageTop,
            speed = parseFloat( (Math.abs(clientY - (tDragHeight / 2))) / tDragHeight, 2),
            scrollTop = self.obj.scrollTop();
        if(speed < 0.05) return; // 距离中间就不动
        if(clientY >= (tDragHeight / 2)) {
            // 向下移动
            self.obj.scrollTop(scrollTop + speed * oneTop);
            // test();
        } else {
            // 向上移动
            self.obj.scrollTop(scrollTop - speed * oneTop);
        }
    },
    start:function(ev, obj){
        var self = this;
        ev = ev || window.event;
        self._start = true;
        self._move = false;
        self._removeIf = false;
        self.disX = ev.clientX - getW(obj);  // 获取一段宽度
        // self.disY = ev.clientY - getH(obj) - ev.currentTarget.offsetParent.offsetTop; // 获取一段高度（getH()离浏览器的顶部距离，currentTarget.offsetParent离父元素的顶部距离
        self.disY = ev.clientY + ev.currentTarget.parentElement.scrollTop - getH(obj) - ev.currentTarget.offsetParent.offsetTop;
        self.mouseY = self.disY - ev.currentTarget.offsetTop;   // 获取一段高度
        self.mouseX = self.disX;
        // console.log(self.disX,self.disY)
        // console.log(self.mouseX,self.mouseY)
        // console.log(self.tops[parseInt(self.testY/30)].top);
        // self.disY = $(ev.target).parent().position().top;
        // $(obj).css("zIndex",self.zIndex++);
        // self.cloneEle = $(ev.target).parent().css({'zIndex':'9'}).clone().css('visibility','hidden');
        // self.oneAgin = true;
        $(ev.currentTarget).css({'zIndex':'9'});
        $(ev.currentTarget).on('mouseleave',function(){
            self._start = false;
            self._move = false;
            $(this).animate({'left':'0px','top':self.tops[parseInt(self.disY/30)].top+'px'},200,function(){
                // console.log(111)
            })
            // console.log('leave事件触发')
            $(this).css({'zIndex':'1'});
            $(this).unbind('mouseleave')
        })
    },
    move:function(ev,obj){
        var self = this;
        if(!self._start) return false;
        self._move = true;
        // if(self.cloneEle == null) return false;
        // else
        // if(self.oneAgin) {
            // $(ev.target).parent().after(self.cloneEle);
            // self.oneAgin = false;
        // }
        // self.cloneEle
        ev = ev || window.event;
        var target = ev.currentTarget;
        // console.log(ev.clientX,ev.clientY)
        var clientX = ev.clientX - getW(obj)
        var clientY = ev.clientY - getH(obj)
        var l = clientX - self.mouseX;
        var t = clientY + ev.currentTarget.parentElement.scrollTop - ev.currentTarget.offsetParent.offsetTop - self.mouseY;

        // var t = $(ev.target).parent().position().top;
        // console.log($(target).parent('.my-Tdrag'))
        if(!(clientX>0 && clientX<$(target).parent('.my-Tdrag').width()) || !(clientY>0 && clientY<$(target).parent('.my-Tdrag').height()+100) ){
            this.end.call(this,ev,obj,false);
        }
        $(target).css({'left':l +'px','top':t +'px'})
        this.scroll.call(self,ev);
    },
    end:function(ev,obj,result){
        var self = this;
        //判断是否开始点击
        if(self._start === false) return;
        self._move = false;
        self._start = false;
        //判断是否开始点击
        // console.log(self._removeIf)
        ev = ev || window.event;
        var l = ev.clientX - getW(obj);
        var t = ev.clientY + ev.currentTarget.parentElement.scrollTop - getH(obj) - ev.currentTarget.offsetParent.offsetTop;

        var target = ev.currentTarget
            ,chilren = $(target).parent().children();
            // console.log(chilren)
        //移除鼠标移除事件
        $(target).unbind('mouseleave')
        // console.log(parseInt(self.disY/30),targetIndex);
        // console.log(self.tops)
        var targetIndex = parseInt(self.disY/30);//我的
        var currentIndex = parseInt(t/30); //目标
        // console.log(self.tops)
        var currentObj= self.tops[parseInt(self.disY/30)]; //我的
        var targetObj = self.tops[parseInt(t/30)];  //目标
        if(t/30<chilren.length && result === undefined){

            //需要切换节点
            // self.tops[parseInt(t/30)].index = currentObj.index;
            // self.tops[parseInt(self.disY/30)].index = targetObj.index;
            if(targetIndex>currentIndex){//向上排序
                self.tops = self.tops.map(function(item,index){
                    if(index>=currentIndex && index<=targetIndex){
                        return {index:item.index,top:item.top + 30,text:item.text};
                    }else return item;
                })
                self.tops.splice(currentIndex,0,currentObj)
                self.tops.splice(targetIndex+1,1)
                self.tops[currentIndex].top = targetObj.top;
                // self.tops[currentObj.index].index = targetObj.index;
            }else{//向下排序
                if(targetIndex<currentIndex){
                    self.tops = self.tops.map(function(item,index){
                        if(index<=currentIndex && index>=targetIndex){
                            return {index:item.index,top:item.top - 30,text:item.text};
                        }else return item;
                    })
                    self.tops.splice(currentIndex+1,0,currentObj)
                    self.tops.splice(targetIndex,1)
                    self.tops[currentIndex].top = targetObj.top;
                }
            }
            // console.log(self.tops)
            // console.log(currentTop,targetTop)
            self.refresh.call(self,target);
            return;
        }
        // console.log(currentIndex)
        $(target).animate({'left':'0px','top':self.tops[parseInt(self.disY/30)].top+'px'},200,function(){
            console.log(111)
        })
        console.log('down事件结束')
        $(ev.target).css({'zIndex':'1'});
    },
    refresh:function(flag){
        var self = this
            ,chilren = $(self.obj).children()
        // console.log(flag)
        if(flag === true){//是否是第一次 true 为第一次
            $.each(self.tops,function(index,item){
                chilren.eq(item.index).find('.text').text(item.text)
                chilren.eq(item.index).stop().animate({'left':'0px','top':item.top+'px'},0,function(){
                    $(this).css({'zIndex':'1'});
                })
            })
        }else{
            $.each(self.tops,function(index,item){
                chilren.eq(item.index).stop().animate({'left':'0px','top':item.top+'px'},300,function(){
                    $(this).css({'zIndex':'1'});
                })
            })
        }
    },
    add:function(text){
        var self = this;
        var children = $(self.obj).children()
            ,newEle = children.eq(0).clone(true);
            // console.log(newEle)
            if(self.maxLength !== null && children.length >= self.maxLength) { myFun.layer.msg('列表最多'+self.maxLength+'列',0);return false;}
            // console.log(222)
            newEle.css({'top': children.length * 30}).find('.text').text(text);

            self.tops.push({index: children.length,top:children.length * 30,text:text })
            // console.log(children.eq(children.length-1));
            children.eq(children.length-1).after(newEle);
            return true;
    },
    remove:function(text){
        var self = this;
        self.refresh(true);
        var children = $(self.obj).children()
            ,myIndex = null
            ,myTop = null;
        if(children.length<=1){ myFun.layer.msg('列表至少有一列',0); return false;}
        children.each(function(index,item){
            if($(item).find('.text').text() == text) $(item).remove();
        })
        for(var i = 0; i<self.tops.length;i++){
            if(self.tops[i].text == text){
                myIndex = self.tops[i].index;
                myTop = self.tops[i].top;
                self.tops.splice(i,1);
            }
        }
        for(var i = 0; i<self.tops.length;i++){
            if(self.tops[i].index>myIndex){ self.tops[i].index--;}
            if(self.tops[i].top>myTop){ self.tops[i].top-=30;}
        }

        // console.log(self.tops)
        // console.log(myIndex)
        self.refresh(true);
        // self.tops
        return true;
    },
    getData:function(){
        return this.tops.map(function(item){return item.text;});
    }
}
//获取元素距离顶部的高度
function getH(ele, padding) {
    var h = 0;
    while (ele) {
        h += ele.offsetTop;
        padding ?
            h += parseInt(ele.currentStyle ? ele.currentStyle["paddingBottom"] : getComputedStyle(ele, false)["paddingBottom"])
            : '';
        ele = ele.offsetParent; //  parentElement
    }
    return h;
}
//获取元素距离左边的宽度
function getW(ele) {
    var h = 0;
    while (ele) {
        h += ele.offsetLeft;
        ele = ele.offsetParent;
    }
    return h;
}
(function() {
    // 扩展Date
    Date.prototype.format = function(format) {
        var date = {
            "M+": this.getMonth() + 1,
            "d+": this.getDate(),
            "h+": this.getHours(),
            "m+": this.getMinutes(),
            "s+": this.getSeconds(),
            "q+": Math.floor((this.getMonth() + 3) / 3),
            "S+": this.getMilliseconds()
        };
        if(/(y+)/i.test(format)) {
            format = format.replace(RegExp.$1, (this.getFullYear() + "").substr(4 - RegExp.$1.length));
        }
        for (const key in date) {
            if(new RegExp(`(${key})`).test(format)) {
                format = format.replace(RegExp.$1, RegExp.$1.length == 1 ? date[k] : ("00" + date[key]).substr(("" + date[key]).length));
            }
        }
        return format;
    }
})();
