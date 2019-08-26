<?php
/**
 * 操作vtourskin.xml文件数据
 * @param $vtourXmlUrl
 */
function changVtourskinXml($vtourXmlUrl)
{

    /***********************************************************
     * 小行星场景设置
     * a.只显示小行星而不显示任何皮肤,只待小行星结束后才显示皮肤
     * b.保证在HTML5以及Flash下热点hotspot都不会再小行星视图中出现
     ***********************************************************/
    $vtourDocXml = new \DOMDocument();
    $vtourDocXml->load($vtourXmlUrl);
    $actionDom = $vtourDocXml->getElementsByTagName("action");
    $actionDom->item(9)->nodeValue = "
set(global.lpinfo, scene=get(xml.scene), hlookat=get(view.hlookat),vlookat=get(view.vlookat), fov=get(view.fov), fovmax=get(view.fovmax), limitview=get(view.limitview) );
set(view,fovmax=170, limitview=lookto, vlookatmin=90, vlookatmax=90);set_hotspot_visible(false);set(layer[skin_layer],visible=false,alpha=0);
set(layer[skin_control_bar],visible=false,alpha=0);set(layer[skin_splitter_bottom],visible=false,alpha=0);
lookat(calc(global.lpinfo.hlookat - 180), 90, 150, 1, 0, 0);set(global.lp_running,true);set(events[lp_events].onloadcomplete,delayedcall(0.5,
if(lpinfo.scene === xml.scene,set(control.usercontrol, off);set(view, limitview=get(lpinfo.limitview), vlookatmin=null, view.vlookatmax=null);
tween(view.hlookat|view.vlookat|view.fov|view.distortion, calc('' + lpinfo.hlookat + '|' + lpinfo.vlookat + '|' + lpinfo.fov + '|' + 0.0),
3.0, easeOutQuad,set(control.usercontrol, all);tween(view.fovmax, get(lpinfo.fovmax));set(lp_running,false);set_hotspot_visible(true);
set(layer[skin_layer].visible,true);tween(layer[skin_layer].alpha,1,1);set(layer[skin_control_bar].visible,true);tween(layer[skin_control_bar].alpha,1,1);
set(layer[skin_splitter_bottom].visible,true);tween(layer[skin_splitter_bottom].alpha,1,1);skin_deeplinking_update_url();
delete(global.lpinfo););,delete(global.lpinfo););););";

    //b.保证在HTML5以及Flash下热点hotspot都不会再小行星视图中出现(创建action节点)
    $hotspotAction = "for(set(i,0),i LT hotspot.count,inc(i),if(%1 == false,if(hotspot[get(i)].visible == true,set(hotspot[get(i)].mark,true);set(hotspot[get(i)].visible,%1);
);,if(hotspot[get(i)].mark == true OR hotspot[get(i)].mark2 == true,set(hotspot[get(i)].visible,%1);););)";
    $rootNode = $vtourDocXml->getElementsByTagName("krpano")->item(0);    //根节点
    $actionNode = $vtourDocXml->createElement("action");                        //创建action节点
    if ($actionNode->nodeValue == "") {
        $actionNode->nodeValue = $hotspotAction;
        $actionNode->setAttribute("name", "set_hotspot_visible");
        $rootNode->appendChild($actionNode);
    }
    $vtourDocXml->save($vtourXmlUrl);


    /*******************************
     * 控制条
     ******************************/
    $vtourXmlStr = file_get_contents($vtourXmlUrl);
    $vtourXmlObj = new \SimpleXMLElement($vtourXmlStr);

    // [控制条皮肤]  -->  skin_scroll_window
    $skin_scroll_window = $vtourXmlObj->layer[2]->layer[0];
    $skin_scroll_window['y'] = "0";
    $skin_scroll_window['width'] = "101%";

    //[控制条皮肤]   -->   skin_scroll_container
    $skin_scroll_container = $skin_scroll_window->layer[0]->layer[2];
    $skin_scroll_container['y'] = "50";

    /**
     * [skin_scroll_layer]
     * 添加 头像 在线聊 打电话 三个层
     */
    $skin_scroll_layer = $skin_scroll_window->layer[0];
    $skin_scroll_layer['width'] = 'get:skin_settings.layout_maxwidth';

    //添加 skin_user 层
    $skin_user = $skin_scroll_layer->addChild("layer");
    $skin_user->addAttribute("name","skin_user");
    $skin_user->addAttribute("type","container");
    $skin_user->addAttribute("url","%SWFPATH%/plugins/textfield.swf");
    $skin_user->addAttribute("zorder","24");
    $skin_user->addAttribute("backgroundcolor","0x007F4916");
    $skin_user->addAttribute("roundedge","25");
    $skin_user->addAttribute("devices","mobile");
    $skin_user->addAttribute("keep","true");
    $skin_user->addAttribute("align","lefttop");
    $skin_user->addAttribute("edge","leftbottom");
    $skin_user->addAttribute("width","40");
    $skin_user->addAttribute("height","42");
    $skin_user->addAttribute("bg","false");
    $skin_user->addAttribute("x","5%");
    $skin_user->addAttribute("y","50");
    $user_icon = $skin_user->addChild("layer");
    $user_icon->addAttribute("name","user_icon");
    $user_icon->addAttribute("url","../../../../static/imgs/manager.png");
    $user_icon->addAttribute("zorder","25");
    $user_icon->addAttribute("style","skin_base|skin_glow");
    $user_icon->addAttribute("scale","0.3");
    $user_icon->addAttribute("align","top");
    $user_icon->addAttribute("keep","true");
    $user_icon->addAttribute("y","2");
    $user_icon->addAttribute("onclick","if(layer[iframelayer].visible,
								remove_iframe();
								set(layer[iframelayer].visible,false),
								call_iframe(/pano/krpano/fr););");

    //添加 skin_talk 层
    $skin_talk = $skin_scroll_layer->addChild("layer");
    $skin_talk->addAttribute("name","skin_talk");
    $skin_talk->addAttribute("type","container");
    $skin_talk->addAttribute("url","%SWFPATH%/plugins/textfield.swf");
    $skin_talk->addAttribute("zorder","24");
    $skin_talk->addAttribute("keep","true");
    $skin_talk->addAttribute("align","lefttop");
    $skin_talk->addAttribute("edge","leftbottom");
    $skin_talk->addAttribute("style","skin_base|skin_glow");
    $skin_talk->addAttribute("devices","mobile");
    $skin_talk->addAttribute("width","45");
    $skin_talk->addAttribute("height","42");
    $skin_talk->addAttribute("bg","false");
    $skin_talk->addAttribute("x","16%");
    $skin_talk->addAttribute("y","50");
    $talk_icon = $skin_talk->addChild("layer");
    $talk_icon->addAttribute("name","talk_icon");
    $talk_icon->addAttribute("url","../../../../static/imgs/message.png");
    $talk_icon->addAttribute("alpha","8");
    $talk_icon->addAttribute("zorder","25");
    $talk_icon->addAttribute("scale","0.2");
    $talk_icon->addAttribute("align","top");
    $talk_icon->addAttribute("keep","true");
    $talk_icon->addAttribute("y","2");
    $talk_icon->addAttribute("style","skin_base|skin_glow");
    $talk_title = $skin_talk->addChild("layer");
    $talk_title->addAttribute("name","talk_title");
    $talk_title->addAttribute("url","%SWFPATH%/plugins/textfield.swf");
    $talk_title->addAttribute("zorder","25");
    $talk_title->addAttribute("scale","0.1");
    $talk_title->addAttribute("css","color:#FFFFFF; font-family:Arial;font-size:8px;");
    $talk_title->addAttribute("bg","false");
    $talk_title->addAttribute("html","在线聊");
    $talk_title->addAttribute("x","-18");
    $talk_title->addAttribute("y","2");
    $talk_title->addAttribute("align","buttom");
    $talk_title->addAttribute("keep","true");

    //添加 skin_phone 层
    $skin_phone = $skin_scroll_layer->addChild("layer");
    $skin_phone->addAttribute("name","skin_phone");
    $skin_phone->addAttribute("type","container");
    $skin_phone->addAttribute("url","%SWFPATH%/plugins/textfield.swf");
    $skin_phone->addAttribute("zorder","24");
    $skin_phone->addAttribute("keep","true");
    $skin_phone->addAttribute("align","lefttop");
    $skin_phone->addAttribute("edge","leftbottom");
    $skin_phone->addAttribute("style","skin_base|skin_glow");
    $skin_phone->addAttribute("devices","mobile");
    $skin_phone->addAttribute("width","45");
    $skin_phone->addAttribute("height","42");
    $skin_phone->addAttribute("bg","false");
    $skin_phone->addAttribute("x","29%");
    $skin_phone->addAttribute("y","50");
    $phone_icon = $skin_phone->addChild("layer");
    $phone_icon->addAttribute("name","phone_icon");
    $phone_icon->addAttribute("url","../../../../static/imgs/phone.png");
    $phone_icon->addAttribute("alpha","8");
    $phone_icon->addAttribute("zorder","25");
    $phone_icon->addAttribute("scale","0.4");
    $phone_icon->addAttribute("align","top");
    $phone_icon->addAttribute("keep","true");
    $phone_icon->addAttribute("y","2");
    $phone_icon->addAttribute("style","skin_base|skin_glow");
    $phone_title = $skin_phone->addChild("layer");
    $phone_title->addAttribute("name","phone_title");
    $phone_title->addAttribute("url","%SWFPATH%/plugins/textfield.swf");
    $phone_title->addAttribute("zorder","25");
    $phone_title->addAttribute("scale","0.1");
    $phone_title->addAttribute("css","color:#FFFFFF; font-family:Arial;font-size:8px;");
    $phone_title->addAttribute("bg","false");
    $phone_title->addAttribute("html","打电话");
    $phone_title->addAttribute("x","-18");
    $phone_title->addAttribute("y","2");
    $phone_title->addAttribute("align","buttom");
    $phone_title->addAttribute("keep","true");


    //隐藏整个 [skin_control_bar] 层 为了自定义这个层 todo
    $skin_control_bar = $vtourXmlObj->layer[2]->layer[2];
    $skin_control_bar['y'] = "-9999999";

    //[skin_title]
    $skin_title = $skin_scroll_window->layer[0]->layer[0];
    $skin_title['align'] = "righttop";
    $skin_title['edge'] = "centerbuttom";
    $skin_title['type'] = "text";
    $skin_title['x'] = "10%";
    $skin_title['y'] = "30";
    $skin_title['backgroundcolor'] = "0x006A6C6E";
    $skin_title['bg'] = "true";
    $skin_title['width'] = "60";
    $skin_title['height'] = "30";
    $skin_title['roundedge'] = "15";
    $skin_title['enabled'] = "true";
    $skin_title['visible'] = "true";
    $skin_title['style'] = "skin_base|skin_glow";
    $skin_title['css'] = "calc:skin_settings.design_text_css + 'font-size:15px;text-align:center;line-height:30px'";
    $skin_title->addAttribute("ondown2","skin_showmap(false); skin_showthumbs();");

    //更改全景图载入时显示的 [加载中..] 样式
    $skin_loadingtext = $vtourXmlObj->layer[2]->layer[3];
    $skin_loadingtext['css'] = "calc:skin_settings.design_text_css + ' text-align:center; font-style:italic; font-size:12px;'";

    //不显示 [hide]
    $skin_btn_hide = $vtourXmlObj->layer[2]->layer[2]->layer[0]->layer[7];
    $skin_btn_hide->addAttribute("visible", "false");

    //不显示 [prev]
    $skin_btn_prev = $vtourXmlObj->layer[2]->layer[2]->layer[0]->layer[0];
    $skin_btn_prev['y'] = "9999999";

    //不显示 [next]
    $skin_btn_next = $vtourXmlObj->layer[2]->layer[2]->layer[0]->layer[9];
    $skin_btn_next['y'] = "9999999";

    //不显示 [navi]
    $skin_btn_navi = $vtourXmlObj->layer[2]->layer[2]->layer[0]->layer[3];
    $skin_btn_navi->addAttribute("visible", "false");


    //不显示 [map]
    $skin_btn_map = $vtourXmlObj->layer[2]->layer[2]->layer[0]->layer[2];
    $skin_btn_map['y'] = "9999999";

    file_put_contents($vtourXmlUrl, $vtourXmlObj->asXML());
}


/**
 * 全景漫游启动画面
 * @param null $xmlFile
 */
function startpic($xmlFile = null)
{
    $vtourDocXml = new \DOMDocument();
    $vtourDocXml->load($xmlFile);
    $krpanoNode = $vtourDocXml->getElementsByTagName("krpano")->item(0);
    //container背景
    $layerContainer = $vtourDocXml->createElement("layer");
    $layerContainer->setAttribute("name", "startpic_container");
    $layerContainer->setAttribute("preload", "true");
    $layerContainer->setAttribute("alpha", "1");
    $layerContainer->setAttribute("children", "true");
    $layerContainer->setAttribute("visible", "true");
    $layerContainer->setAttribute("zorder", "90");
    $layerContainer->setAttribute("url", "../../../static/imgs/background.jpg");
    $layerContainer->setAttribute("maskchildren", "true");
    $layerContainer->setAttribute("keep", "true");
    $layerContainer->setAttribute("width", "100%");
    $layerContainer->setAttribute("height", "100%");
    $layerContainer->setAttribute("bgalpha", "4");

    //loading_back_icon
    $layerLoadingBack = $vtourDocXml->createElement("layer");
    $layerLoadingBack->setAttribute("name","loading_back");
    $layerLoadingBack->setAttribute("type","container");
    $layerLoadingBack->setAttribute("url","%SWFPATH%/plugins/textfield.swf");
    $layerLoadingBack->setAttribute("keep","true");
    $layerLoadingBack->setAttribute("devices","mobile");
    $layerLoadingBack->setAttribute("width","25");
    $layerLoadingBack->setAttribute("height","25");
    $layerLoadingBack->setAttribute("background","false");
    $layerLoadingBack->setAttribute("align","lefttop");
    $layerLoadingBack->setAttribute("x","5%");
    $layerLoadingBack->setAttribute("y","5%");
    //追加至外层的Container区域
    $layerContainer->appendChild($layerLoadingBack);
    //退出icon
    $loadingBackIcon = $vtourDocXml->createElement("layer");
    $loadingBackIcon->setAttribute("name","loading_back_icon");
    $loadingBackIcon->setAttribute("url","../../../static/imgs/loading-left.png");
    $loadingBackIcon->setAttribute("scale","0.22");
    $loadingBackIcon->setAttribute("zorder","12");
    $loadingBackIcon->setAttribute("style","skin_base|skin_glow");
    $loadingBackIcon->setAttribute("keep","true");
    $loadingBackIcon->setAttribute("onclick","");
    //追加至loading_back_icon
    $layerLoadingBack->appendChild($loadingBackIcon);

    //Logo
    $layerLogo = $vtourDocXml->createElement("layer");
    $layerLogo->setAttribute("name", "skin_title_logo3");
    $layerLogo->setAttribute("zorder", "100");
    $layerLogo->setAttribute("keep", "true");
    $layerLogo->setAttribute("x", "0");
    $layerLogo->setAttribute("y", "32%");
    $layerLogo->setAttribute("align", "top");
    $layerLogo->setAttribute("width", "410px");
    $layerLogo->setAttribute("height", "110px");
    $layerLogo->setAttribute("url", "../../../static/imgs/logo.png");
    $layerLogo->setAttribute("scale", "0.4");
    $layerLogo->setAttribute("onloaded", "tween(alpha,1.0);tween(layer[skin_title_pr].alpha,1.0);");
    $layerLogo->setAttribute("enabled", "false");



    //进度条bg层
    $layerloadingbar = $vtourDocXml->createElement("layer");
    $layerloadingbar->setAttribute("name", 'loadingbar_bg');
    $layerloadingbar->setAttribute("type", 'container');
    $layerloadingbar->setAttribute("bgcolor", '0x0068696B');
    $layerloadingbar->setAttribute("bgalpha", '0.3');
    $layerloadingbar->setAttribute("align", 'top');
    $layerloadingbar->setAttribute("y", '46%');
    $layerloadingbar->setAttribute("width", '40%');
    $layerloadingbar->setAttribute("height", '7');
    $layerloadingbar->setAttribute("enabled", 'false');
    $layerloadingbar->setAttribute("visible", 'false');

    //进度条space层
    $loadingbarSpace = $vtourDocXml->createElement("layer");
    $loadingbarSpace->setAttribute("name", "loadingbar_space");
    $loadingbarSpace->setAttribute("type", "container");
    $loadingbarSpace->setAttribute("align", "left");
    $loadingbarSpace->setAttribute("x", "2");
    $loadingbarSpace->setAttribute("y", "1");
    $loadingbarSpace->setAttribute("width", "-3");
    $loadingbarSpace->setAttribute("height", "7");


    //进度条fill层
    $loadingbarFill = $vtourDocXml->createElement("layer");
    $loadingbarFill->setAttribute("name", "loadingbar_fill");
    $loadingbarFill->setAttribute("type", "container");
    $loadingbarFill->setAttribute("bgcolor", "0x0064C506");
    $loadingbarFill->setAttribute("bgalpha", "0.8");
    $loadingbarFill->setAttribute("align", "lefttop");
    $loadingbarFill->setAttribute("width", "0");
    $loadingbarFill->setAttribute("height", "4");


    $layerloadingbar->appendChild($loadingbarSpace);
    $loadingbarSpace->appendChild($loadingbarFill);

    //百分比进度条
    $layerPercent = $vtourDocXml->createElement("layer");
    $layerPercent->setAttribute("name", "loadingpercent_text");
    $layerPercent->setAttribute("url", "%SWFPATH%/plugins/textfield.swf");
    $layerPercent->setAttribute("align", "top");
    $layerPercent->setAttribute("y", "48%");
    $layerPercent->setAttribute("background", "false");
    $layerPercent->setAttribute("css", "color:#FFFFFF; font-family:Arial; font-weight:bold; font-size:12px; font-style:italic;");
    $layerPercent->setAttribute("textshadow", "2");
    $layerPercent->setAttribute("html", "");


    //Title
    $layerTitle = $vtourDocXml->createElement("layer");
    $layerTitle->setAttribute("name", "skin_title_pr");
    $layerTitle->setAttribute("background", "false");
    $layerTitle->setAttribute("visible", "true");
    $layerTitle->setAttribute("keep", "true");
    $layerTitle->setAttribute("align", "top");
    $layerTitle->setAttribute("zorder", "100");
    $layerTitle->setAttribute("url", "%SWFPATH%/plugins/textfield.swf");
    $layerTitle->setAttribute("html", "全景模式   弹指可触");
    $layerTitle->setAttribute("css", "text-align:center; color:#F2F2F2; font-family:SimHei; font-weight:bold; font-size:14px;");
    $layerTitle->setAttribute("x", "0");
    $layerTitle->setAttribute("y", "38.5%");
    $layerTitle->setAttribute("enabled", "false");

    //logo  title  进度条 百分比进度条  追加至外层的Container区域
    $layerContainer->appendChild($layerLogo);
    $layerContainer->appendChild($layerloadingbar);
    $layerContainer->appendChild($layerPercent);
    $layerContainer->appendChild($layerTitle);


    /* events 事件区块 */
    $eventTag = $vtourDocXml->createElement("events");
    $eventTag->setAttribute("name", "startlogoevents");
    $eventTag->setAttribute("keep", "true");
    $eventTag->setAttribute("onxmlcomplete", "loadingbar_startloading();");
    $eventTag->setAttribute("onloadcomplete", "delayedcall(0.1,tween(layer[startpic_container].ox,-2500,1));delayedcall(0.5, loadingbar_stoploading() );");


    /* 进度条start 事件区块 */
    $actionStartLoading = $vtourDocXml->createElement("action");
    $actionStartLoading->setAttribute("name", "loadingbar_startloading");
    $actionValue = "set(loadingbar_loading, true);set(layer[loadingbar_bg].visible, true);set(layer[loadingpercent_text].visible, true);asyncloop(loadingbar_loading,mul(pv, progress.progress, 100);roundval(pv,0);txtadd(pv, '%');txtadd(layer[loadingpercent_text].html, '', get(pv));copy(layer[loadingbar_fill].width, pv););";
    $actionStartLoading->nodeValue = $actionValue;

    /* 进度条 container stop 事件区块 */
    $actionStopLoading = $vtourDocXml->createElement("action");
    $actionStopLoading->setAttribute("name", "loadingbar_stoploading");
    $actionContent = "set(loadingbar_loading, false);set(layer[loadingbar_bg].visible, false);set(layer[loadingpercent_text].visible, false);";
    $actionStopLoading->nodeValue = $actionContent;

    //追加至根节点下
    $krpanoNode->appendChild($layerContainer);
    $krpanoNode->appendChild($eventTag);
    $krpanoNode->appendChild($actionStartLoading);
    $krpanoNode->appendChild($actionStopLoading);
    $vtourDocXml->save($xmlFile);

}


/**
 * 漫游场景中嵌入房源信息层
 * @param null $xmlFile
 */
function addIframeFy($xmlFile = null, $route = '',$content = '')
{
    $vtourDocXml = new \DOMDocument();
    $vtourDocXml->load($xmlFile);
    //根节点
    $krpanoNode = $vtourDocXml->getElementsByTagName("krpano")->item(0);
    //control标签
    $controlNode = $vtourDocXml->createElement("control");
    $controlNode->setAttribute("mousetype", "drag2d");
    //preview标签
    $previewNode = $vtourDocXml->createElement("preview");
    $previewNode->setAttribute("type", "grid();");
    //iframelayer 块
    $layerIframe = $vtourDocXml->createElement("layer");
    $layerIframe->setAttribute("name", "iframelayer");
    $layerIframe->setAttribute("type", "container");
    $layerIframe->setAttribute("align", "center");
    $layerIframe->setAttribute("width", "100%");
    $layerIframe->setAttribute("height", "100%");
    $layerIframe->setAttribute("zorder", "10");
    $layerIframe->setAttribute("keep", "true");
    $layerIframe->setAttribute("visible", "false");
    // button_3 按钮
    $layerButton = $vtourDocXml->createElement("layer");
    $layerButton->setAttribute("name", "button_3");
    $layerButton->setAttribute("style", "buttonstyle");
    $layerButton->setAttribute("x", "");
    $layerButton->setAttribute("background", "true");
    $layerButton->setAttribute("html", $content);
    $layerButton->setAttribute("onclick", "if(layer[iframelayer].visible,
			   		set(layer[icon].url, '../../static/imgs/down.png');
			   		set(layer[top_button_layer].visible, false);
			   		set(layer['button_3'].background,true);
			   		set(layer['left_content'].background,true);
			   		set(layer['right_share'].background,true);
			   		set(layer['right_screen'].background,true);
			   		remove_iframe();
			   		set(layer[iframelayer].visible,false),
					set(layer[icon].url, '../../static/imgs/up.png');
					set(layer['button_3'].background,false);
					set(layer['left_content'].background,false);
					set(layer['right_share'].background,false);
					set(layer['right_screen'].background,false);
					set(layer[top_button_layer].visible, true);
					call_iframe(/pano/krpano/fr););");

    //icon 上下标签图标
    $layerIcon = $vtourDocXml->createElement("layer");
    $layerIcon->setAttribute("name","icon");
    $layerIcon->setAttribute("url","../../../static/imgs/down.png");
    $layerIcon->setAttribute("zorder","10");
    $layerIcon->setAttribute("scale","1.3");
    $layerIcon->setAttribute("keep","true");
    $layerIcon->setAttribute("align","right");
    $layerIcon->setAttribute("x","5");
    //追加至button_3
    $layerButton->appendChild($layerIcon);

    //top_button_layer 顶部层
    $layerTopButton = $vtourDocXml->createElement('layer');
    $layerTopButton->setAttribute("name",'top_button_layer');
    $layerTopButton->setAttribute("keep",'true');
    $layerTopButton->setAttribute("type",'container');
    $layerTopButton->setAttribute("align",'top');
    $layerTopButton->setAttribute("width",'108%');
    $layerTopButton->setAttribute("height",'60');
    $layerTopButton->setAttribute("visible",'false');
    $layerTopButton->setAttribute("x",'10');
    $layerTopButton->setAttribute("y",'0');
    $layerTopButton->setAttribute("bgalpha",'0.24');
    $layerTopButton->setAttribute("zorder",'10');
    //追加至根节点
    $krpanoNode->appendChild($layerTopButton);

    //手机显示的 顶部返回按钮 left_content、
    $layerLeftContent =  $vtourDocXml->createElement("layer");
    $layerLeftContent->setAttribute("name","left_content");
    $layerLeftContent->setAttribute("type","container");
    $layerLeftContent->setAttribute("url","%SWFPATH%/plugins/textfield.swf");
    $layerLeftContent->setAttribute("zorder","10");
    $layerLeftContent->setAttribute("keep","true");
    $layerLeftContent->setAttribute("devices","mobile");
    $layerLeftContent->setAttribute("roundedge","20");
    $layerLeftContent->setAttribute("width","25");
    $layerLeftContent->setAttribute("height","25");
    $layerLeftContent->setAttribute("height","25");
    $layerLeftContent->setAttribute("backgroundcolor","0x000000");
    $layerLeftContent->setAttribute("background","true");
    $layerLeftContent->setAttribute("align","lefttop");
    $layerLeftContent->setAttribute("x","25");
    $layerLeftContent->setAttribute("y","20");
    //追加至根节点
    $krpanoNode->appendChild($layerLeftContent);
    //手机显示的 顶部返回按钮 left_icon、
    $layerLeftIcon = $vtourDocXml->createElement("layer");
    $layerLeftIcon->setAttribute("name","left_icon");
    $layerLeftIcon->setAttribute("url","../../../static/imgs/left.png");
    $layerLeftIcon->setAttribute("scale","1.7");
    $layerLeftIcon->setAttribute("zorder","12");
    $layerLeftIcon->setAttribute("align","center");
    $layerLeftIcon->setAttribute("style","skin_base|skin_glow");
    $layerLeftIcon->setAttribute("keep","true");
    //追加至left_content
    $layerLeftContent->appendChild($layerLeftIcon);

    //手机显示 顶部分享 right_share、
    $layerRightShare = $vtourDocXml->createElement("layer");
    $layerRightShare->setAttribute("name","right_share");
    $layerRightShare->setAttribute("type","container");
    $layerRightShare->setAttribute("url","%SWFPATH%/plugins/textfield.swf");
    $layerRightShare->setAttribute("zorder","10");
    $layerRightShare->setAttribute("keep","true");
    $layerRightShare->setAttribute("devices","mobile");
    $layerRightShare->setAttribute("roundedge","20");
    $layerRightShare->setAttribute("width","25");
    $layerRightShare->setAttribute("height","25");
    $layerRightShare->setAttribute("backgroundcolor","0x000000");
    $layerRightShare->setAttribute("background","true");
    $layerRightShare->setAttribute("align","righttop");
    $layerRightShare->setAttribute("x","25");
    $layerRightShare->setAttribute("y","20");
    //追加至根节点
    $krpanoNode->appendChild($layerRightShare);
    //手机显示的 顶部返回按钮 right_link、
    $layerRightLink = $vtourDocXml->createElement("layer");
    $layerRightLink->setAttribute("name","right_link");
    $layerRightLink->setAttribute("url","../../../static/imgs/link.png");
    $layerRightLink->setAttribute("zorder","12");
    $layerRightLink->setAttribute("scale","0.15");
    $layerRightLink->setAttribute("align","center");
    $layerRightLink->setAttribute("style","skin_base|skin_glow");
    $layerRightLink->setAttribute("keep","true");
    //追加至 right_share
    $layerRightShare->appendChild($layerRightLink);

    //right_screen
    $layerRightScreen = $vtourDocXml->createElement("layer");
    $layerRightScreen->setAttribute("name","right_screen");
    $layerRightScreen->setAttribute("type","container");
    $layerRightScreen->setAttribute("url","%SWFPATH%/plugins/textfield.swf");
    $layerRightScreen->setAttribute("zorder","10");
    $layerRightScreen->setAttribute("keep","true");
    $layerRightScreen->setAttribute("devices","!mobile+fullscreensupport");
    $layerRightScreen->setAttribute("roundedge","20");
    $layerRightScreen->setAttribute("width","25");
    $layerRightScreen->setAttribute("height","25");
    $layerRightScreen->setAttribute("backgroundcolor","0x000000");
    $layerRightScreen->setAttribute("background","true");
    $layerRightScreen->setAttribute("align","righttop");
    $layerRightScreen->setAttribute("x","25");
    $layerRightScreen->setAttribute("y","20");
    //追加至根节点
    $krpanoNode->appendChild($layerRightScreen);
    //right_fullscreen
    $layerRightFullScreen = $vtourDocXml->createElement("layer");
    $layerRightFullScreen->setAttribute("name","right_fullscreen");
    $layerRightFullScreen->setAttribute("url","../../../static/imgs/fullscreen.png");
    $layerRightFullScreen->setAttribute("zorder","12");
    $layerRightFullScreen->setAttribute("style","skin_base|skin_glow");
    $layerRightFullScreen->setAttribute("scale","0.15");
    $layerRightFullScreen->setAttribute("align","center");
    $layerRightFullScreen->setAttribute("keep","true");
    $layerRightFullScreen->setAttribute("onclick","switch(fullscreen);");
    // 追加至 right_screen
    $layerRightScreen->appendChild($layerRightFullScreen);

    //right_vr
    $layerRightVr =  $vtourDocXml->createElement('layer');
    $layerRightVr->setAttribute("name","right_vr");
    $layerRightVr->setAttribute("type","container");
    $layerRightVr->setAttribute("url","%SWFPATH%/plugins/textfield.swf");
    $layerRightVr->setAttribute("zorder","10");
    $layerRightVr->setAttribute("keep","true");
    $layerRightVr->setAttribute("roundedge","20");
    $layerRightVr->setAttribute("width","25");
    $layerRightVr->setAttribute("height","25");
    $layerRightVr->setAttribute("backgroundcolor","0x000000");
    $layerRightVr->setAttribute("background","true");
    $layerRightVr->setAttribute("align","righttop");
    $layerRightVr->setAttribute("x","25");
    $layerRightVr->setAttribute("y","11%");
    //追加至根节点
    $krpanoNode->appendChild($layerRightVr);
    //skin_vr
    $layerSkinVr = $vtourDocXml->createElement("layer");
    $layerSkinVr->setAttribute("name","skin_vr");
    $layerSkinVr->setAttribute("url","%SWFPATH%/plugins/textfield.swf");
    $layerSkinVr->setAttribute("css","color:#FFFFFF; font-family:Arial;font-size:14px;");
    $layerSkinVr->setAttribute("enabled","true");
    $layerSkinVr->setAttribute("bg","false");
    $layerSkinVr->setAttribute("html","VR");
    $layerSkinVr->setAttribute("x","-1");
    $layerSkinVr->setAttribute("y","-2");
    $layerSkinVr->setAttribute("style","skin_base|skin_glow");
    $layerSkinVr->setAttribute("zorder","12");
    $layerSkinVr->setAttribute("scale","0.9");
    $layerSkinVr->setAttribute("align","center");
    $layerSkinVr->setAttribute("keep","true");
    $layerSkinVr->setAttribute("onclick","webvr.enterVR();");
    //追加至 right_vr
    $layerRightVr->appendChild($layerSkinVr);

    //right_setting
    $layerRightSetting =  $vtourDocXml->createElement("layer");
    $layerRightSetting->setAttribute("name","right_setting");
    $layerRightSetting->setAttribute("type","container");
    $layerRightSetting->setAttribute("url","%SWFPATH%/plugins/textfield.swf");
    $layerRightSetting->setAttribute("zorder","10");
    $layerRightSetting->setAttribute("keep","true");
    $layerRightSetting->setAttribute("roundedge","20");
    $layerRightSetting->setAttribute("width","25");
    $layerRightSetting->setAttribute("height","25");
    $layerRightSetting->setAttribute("backgroundcolor","0x000000");
    $layerRightSetting->setAttribute("background","true");
    $layerRightSetting->setAttribute("align","righttop");
    $layerRightSetting->setAttribute("x","25");
    $layerRightSetting->setAttribute("y","18%");
    //追加至根节点
    $krpanoNode->appendChild($layerRightSetting);
    //right_config
    $layerRightConfig = $vtourDocXml->createElement("layer");
    $layerRightConfig->setAttribute("name","right_config");
    $layerRightConfig->setAttribute("url","../../../static/imgs/setting.png");
    $layerRightConfig->setAttribute("zorder","12");
    $layerRightConfig->setAttribute("scale","0.14");
    $layerRightConfig->setAttribute("align","center");
    $layerRightConfig->setAttribute("keep","true");
    $layerRightConfig->setAttribute("style","skin_base|skin_glow");
    $layerRightConfig->setAttribute("onclick","");
    //追加至 right_setting
    $layerRightSetting->appendChild($layerRightConfig);

    //style  [buttonstyle样式]
    $styleButton = $vtourDocXml->createElement("style");
    $styleButton->setAttribute("name", "buttonstyle");
    $styleButton->setAttribute("keep", "true");
    $styleButton->setAttribute("url", "%SWFPATH%/plugins/textfield.swf");
    $styleButton->setAttribute("children", "false");
    $styleButton->setAttribute("enabled", "true");
    $styleButton->setAttribute("align", "topcenter");
    $styleButton->setAttribute("y", "15");
    $styleButton->setAttribute("width", "150");
    $styleButton->setAttribute("height", "32");
    $styleButton->setAttribute("vcenter", "true");
    $styleButton->setAttribute("border", "false");
    $styleButton->setAttribute("background", "true");
    $styleButton->setAttribute("backgroundcolor", "0x000000");
    $styleButton->setAttribute("backgroundalpha", "0.6");
    $styleButton->setAttribute("roundedge", "15");
    $styleButton->setAttribute("css", "text-align:center;color:#FFFFFF;font-family:Arial;font-weight:bold;font-size:14px;margin-left:-12px;");
    $styleButton->setAttribute("zorder", "12");
    $styleButton->setAttribute("onout", "set(shadow,0);if(layer[bar].state != name,stoptween(layer[bar].x);tween(layer[bar].x,get(layer[get(layer[bar].state)].x),0.5))");
    //action  [call_iframe]
    $actionCall = $vtourDocXml->createElement("action");
    $actionCall->setAttribute("name", "call_iframe");
    $actionCall->nodeValue = "callwith(layer[iframelayer],add_iframe(%1, 100%, 100%);set(visible,true));";
    //action  [add_iframe]
    $actionAdd = $vtourDocXml->createElement("action");
    $actionAdd->setAttribute("name", "add_iframe");
    $actionAdd->setAttribute("type", "Javascript");
    $addJs = 'var iframe = document.createElement("iframe");
				iframe.style.position = "fixed";
				iframe.style.left = 0;
				iframe.style.top = 0;
				iframe.style.width = "100%";
				iframe.style.height = "100%";
				iframe.style.border = 0;
				iframe.src = args[1];
			    iframe.setAttribute(\'id\',resolve(caller.name));
				caller.registercontentsize(args[2], args[3]);
				caller.sprite.appendChild(iframe);
				caller.sprite.style.webkitOverflowScrolling = "touch";
		        caller.sprite.style.overflowY = "auto";
		        caller.sprite.style.overflowX = "auto";';
    $actionAdd->nodeValue = $addJs;
    //action  [remove_iframe]
    $actionRemove = $vtourDocXml->createElement("action");
    $actionRemove->setAttribute("name", "remove_iframe");
    $actionRemove->setAttribute("type", "Javascript");
    $removeJs = "var lastIframe = document.getElementById('iframelayer');
		 lastIframe.parentNode.removeChild(lastIframe);";
    $actionRemove->nodeValue = $removeJs;
    //追加至根节点
    $krpanoNode->appendChild($controlNode);
    $krpanoNode->appendChild($previewNode);
    $krpanoNode->appendChild($layerIframe);
    $krpanoNode->appendChild($layerButton);
    $krpanoNode->appendChild($styleButton);
    $krpanoNode->appendChild($actionCall);
    $krpanoNode->appendChild($actionAdd);
    $krpanoNode->appendChild($actionRemove);
    $vtourDocXml->save($xmlFile);


}

/**
 * 添加热点
 * @param $tourSceneArr
 */
function addHotSpot($tourSceneArr)
{
    $sceneCount = count($tourSceneArr);
    for ($i = 0; $i < $sceneCount; $i++) {
        $tourSceneArr[$i]->addChild("hotspot");
        $tourSceneArr[$i]->hotspot->addAttribute("name", "spot1");
        $tourSceneArr[$i]->hotspot->addAttribute("style", "skin_hotspotstyle");
        $tourSceneArr[$i]->hotspot->addAttribute("ath", "0.000");
        $tourSceneArr[$i]->hotspot->addAttribute("atv", "0.000");
        $next = $i + 1;
        if ($next == $sceneCount) {
            $next = 0;
        }
        $name = $tourSceneArr[$next]->attributes()->name;
        $tourSceneArr[$i]->hotspot->addAttribute("linkedscene", "$name");
    }

}

/**
 * 操作tour.xml文件数据
 * @param null $xmlFile
 * @param null $mb_name
 * @return false|string
 */
function changTourXml($xmlFile = null, $mb_name = null,$content)
{
    //项目启动画面
    startpic($xmlFile);

    //漫游场景中嵌入房源信息层
    addIframeFy($xmlFile, '/pano/krpano/fr',$content);


    //修改tour.xml配置
    $tourXmlStr = file_get_contents($xmlFile);
    $tourXmlObj = new \SimpleXMLElement($tourXmlStr);


    //去掉根节点的title
    $tourXmlObj->krpano['title'] = "";

    /*********************
     * 自动旋转设置
     *********************/
    $tourXmlObj->addChild("autorotate");
    $tourXmlObj->autorotate->addAttribute("enabled", "true");
    $tourXmlObj->autorotate->addAttribute("waittime", "1.0");
    $tourXmlObj->autorotate->addAttribute("speed", "10.0");

    /*************************
     * 更改skin_settings配置
     *************************/
    $tourXmlObj->skin_settings['thumbs_opened'] = "true";           //是否在启动时弹出缩略图一栏
    $tourXmlObj->skin_settings['thumbs_text'] = "true";             //是否在缩略图上显示名字
    $tourXmlObj->skin_settings['title'] = "true";                   //是否左下角显示title
    $tourXmlObj->skin_settings['loadingtext'] = "加载中...";        //在全景图载入中时显示的文字
    $tourXmlObj->skin_settings['thumbs_width'] = "100";             //缩略图宽
    $tourXmlObj->skin_settings['thumbs_height'] = "75";             //缩略图高

    /****************
     * 皮肤设置
     ****************/
    $tourXmlObj->skin_settings['design_skin_images'] = "vtourskin_light.png";           //皮肤所用的源图片
    $tourXmlObj->skin_settings['design_bgalpha'] = "0.5";                               //皮肤的透明度 [控制条皮肤]
    $tourXmlObj->skin_settings['design_thumbborder_bgborder'] = "2 0x0000FFFF 1.0";     //皮肤的缩略图边框

    /*****************
     * 小行星动画设置
     *****************/
    $tourXmlObj->skin_settings['littleplanetintro'] = "true";       //打开小行星动画效果

    /**************
     * 导航条容器设置
     **************/
    $tourXmlObj->skin_settings['layout_width'] = "102%";              //导航条容器相对屏幕宽度的百分比
    $tourXmlObj->skin_settings['layout_maxwidth'] = "102%";           //导航条容器的最大宽度像素
    $tourXmlObj->skin_settings['controlbar_offset_closed'] = "-40";   //导航条隐藏状态时与屏幕底部的距离

    $tourXmlObj->skin_settings['controlbar_offset'] = "110";          //导航条背景与屏幕底部的距离   [控制条皮肤]
    $tourXmlObj->skin_settings['controlbar_overlap.no-fractionalscaling'] = "50";       //   [控制条皮肤]
    $tourXmlObj->skin_settings['controlbar_overlap.fractionalscaling'] = "50";          //   [控制条皮肤]



    $tourSceneArr = $tourXmlObj->xpath('scene');
    //添加热点 [hotspot]
    addHotSpot($tourSceneArr);

    /**********************
     * 循环更改xml场景title
     **********************/
    keepArrKey($mb_name, $tourSceneArr, 'title');
    //渲染xml
    file_put_contents($xmlFile, $tourXmlObj->asXML());

}
