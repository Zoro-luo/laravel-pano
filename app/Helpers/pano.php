<?php

/**
 * 操作vtourskin.xml文件数据
 * @param $vtourXmlUrl
 */
function changVtourskinXml($vtourXmlUrl,$panoId,$agentImgUrl,$agentPhone)
{

    $vtourDocXml = new \DOMDocument();
    $vtourDocXml->load($vtourXmlUrl);
    $actionDom = $vtourDocXml->getElementsByTagName("action");

    //icon_title_down 层的切换小icon
    $actionDom->item(26)->nodeValue="if(show == null,if(layer[skin_thumbs].state == 'closed',set(show,true),set(show,false)););
        if(show,
        set(layer[skin_thumbs].state, 'opened');
        tween(layer[skin_thumbs].alpha, 1.0, 0.25);
        tween(layer[skin_scroll_layer].y, calc(-area.pixelheight + layer[skin_thumbs].height +
        layer[skin_scroll_layer].y_offset), 0.5, easeOutQuint);
        set(layer[skin_thumbs_container].visible, true);
        tween(layer[skin_thumbs_container].alpha, 1.0, 0.25);
        tween(layer[skin_map].alpha, 0.0, 0.25, default, set(layer[skin_map].visible,false));
        ,
        set(layer[skin_thumbs].state, 'closed');
        tween(layer[skin_thumbs].alpha, 0.0, 0.25, easeOutQuint);
        tween(layer[skin_scroll_layer].y, calc(-area.pixelheight + layer[skin_scroll_layer].y_offset), 0.5,
        easeOutQuint, set(layer[skin_thumbs_container].visible, false););
        );
        if(layer[skin_thumbs].state == 'closed',
        set(layer[icon_title_down].url, '../../static/images/arrow-down-fill.png'),
        set(layer[icon_title_down].url, '../../static/images/arrow-up-fill.png');
        );";

    //a.只显示小行星而不显示任何皮肤,只待小行星结束后才显示皮肤
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
    $skin_scroll_container['y'] = "2";

    /**
     * [skin_scroll_layer]
     * 添加 头像 在线聊 打电话 三个层
     */
    $skin_scroll_layer = $skin_scroll_window->layer[0];
    $skin_scroll_layer['width'] = 'get:skin_settings.layout_maxwidth';
    $skin_scroll_layer['y_offset'] = '0';

    //VR 移动端退出交互
    $pluginDOC = $vtourXmlObj->plugin[0];
    $pluginDOC['onexitvr'] = "webvr_onexitvr(); skin_webvr_setup(); skin_reloadscene_webvr();set(layer[vr_all_screen].visible, 'false');";

    // PC 父级导航
    $fatherControlBarPc = $skin_scroll_layer->addChild("layer");
    $fatherControlBarPc->addAttribute("name","father_control_bar_pc");
    $fatherControlBarPc->addAttribute("type","text");
    $fatherControlBarPc->addAttribute("align","top");
    $fatherControlBarPc->addAttribute("background","false");
    $fatherControlBarPc->addAttribute("zorder","12");
    $fatherControlBarPc->addAttribute("width","90%");
    $fatherControlBarPc->addAttribute("height","60");
    $fatherControlBarPc->addAttribute("y","-75");
    $fatherControlBarPc->addAttribute("devices","html5+!touchdevice");
    $fatherControlBarPc->addAttribute("keep","true");
    $fatherControlBarPc->addAttribute("url","%SWFPATH%/plugins/textfield.swf");

    // 添加 bohao_saoma 层
    $bohaoSaoma = $fatherControlBarPc->addChild("layer");
    $bohaoSaoma->addAttribute("name","bohao_saoma");
    $bohaoSaoma->addAttribute("url","../../../../static/images/back-code.png");
    $bohaoSaoma->addAttribute("width","180");
    $bohaoSaoma->addAttribute("height","230");
    $bohaoSaoma->addAttribute("zorder","-100");
    $bohaoSaoma->addAttribute("keep","true");
    $bohaoSaoma->addAttribute("visible","false");
    $bohaoSaoma->addAttribute("background","false");
    $bohaoSaoma->addAttribute("backgroundalpha","0.1");
    $bohaoSaoma->addAttribute("align","topleft");
    $bohaoSaoma->addAttribute("x","290");
    $bohaoSaoma->addAttribute("y","-290");

    // 添加 share_saoma 层
    $shareSaoma = $fatherControlBarPc->addChild("layer");
    $shareSaoma->addAttribute("name","share_saoma");
    $shareSaoma->addAttribute("url","../../../../static/images/back-code.png");
    $shareSaoma->addAttribute("width","174");
    $shareSaoma->addAttribute("height","202");
    $shareSaoma->addAttribute("zorder","-100");
    $shareSaoma->addAttribute("keep","true");
    $shareSaoma->addAttribute("visible","false");
    $shareSaoma->addAttribute("background","false");
    $shareSaoma->addAttribute("backgroundalpha","0.1");
    $shareSaoma->addAttribute("align","topright");
    $shareSaoma->addAttribute("x","-32");
    $shareSaoma->addAttribute("y","-230");

    // 添加 back_phone_pc 层
    $backPhonePc  = $fatherControlBarPc->addChild("layer");
    $backPhonePc->addAttribute("name","back_phone_pc");
    $backPhonePc->addAttribute("url","../../../../static/images/back-phone-pc.png");
    $backPhonePc->addAttribute("align","lefttop");
    $backPhonePc->addAttribute("zorder","14");
    $backPhonePc->addAttribute("x","20");
    $backPhonePc->addAttribute("y","-50");
    $backPhonePc->addAttribute("keep","true");
    $backPhonePc->addAttribute("scale","0.9");


    // 内嵌 user_icon_pc 层
    $userIconPc = $backPhonePc->addChild("layer");
    $userIconPc->addAttribute("name","user_icon_pc");
    $userIconPc->addAttribute("url",$agentImgUrl?$agentImgUrl:"../../../../static/images/manager.png");
    $userIconPc->addAttribute("zorder","25");
    $userIconPc->addAttribute("style","skin_base|skin_glow");
    $userIconPc->addAttribute("scale","0.35");
    $userIconPc->addAttribute("align","leftcenter");
    $userIconPc->addAttribute("keep","true");
    $userIconPc->addAttribute("x","35");
    $userIconPc->addAttribute("onclick","if(layer[iframelayer_new].visible,remove_iframe(iframelayer_new);
                                    set(layer[iframelayer_new].visible,false),set(layer[iframelayer].visible,false);set(layer[set_alert_table].visible,false);call_iframe(iframelayer_new,/pano/krpano/vr/".$panoId."););");
    // 添加 phone_value_pc
    $phoneValuePc = $backPhonePc->addChild("layer");
    $phoneValuePc->addAttribute("name","phone_value_pc");
    $phoneValuePc->addAttribute("background","false");
    $phoneValuePc->addAttribute("visible","true");
    $phoneValuePc->addAttribute("keep","true");
    $phoneValuePc->addAttribute("align","leftcenter");
    $phoneValuePc->addAttribute("zorder","14");
    $phoneValuePc->addAttribute("url","%SWFPATH%/plugins/textfield.swf");
    $phoneValuePc->addAttribute("html",$agentPhone?$agentPhone:"NULL");
    $phoneValuePc->addAttribute("css","color:#FFFFFF;font-family:Arial;font-size:18px;font-weight:bold;");
    $phoneValuePc->addAttribute("x","140");
    // 添加 sweep_code_pc
    $sweepCodePc = $backPhonePc->addChild("layer");
    $sweepCodePc->addAttribute("name","sweep_code_pc");
    $sweepCodePc->addAttribute("url","../../../../static/images/sweep-code.png");
    $sweepCodePc->addAttribute("align","leftcenter");
    $sweepCodePc->addAttribute("zorder","14");
    $sweepCodePc->addAttribute("scale","0.9");
    $sweepCodePc->addAttribute("keep","true");
    $sweepCodePc->addAttribute("x","300");
    $sweepCodePc->addAttribute("onover","if(layer[bohao_saoma].visible,remove_iframe(bohao_saoma);
                                   set(layer[bohao_saoma].visible,false),call_iframe(bohao_saoma,/pano/krpano/code/".$panoId."););");
    $sweepCodePc->addAttribute("onout","if(layer[bohao_saoma].visible,remove_iframe(bohao_saoma);
                                   set(layer[bohao_saoma].visible,false),call_iframe(bohao_saoma,/pano/krpano/code/".$panoId."););");

    // 添加 code_share_text
    $codeShareText = $fatherControlBarPc->addChild("layer");
    $codeShareText->addAttribute("name","code_share_text");
    $codeShareText->addAttribute("background","false");
    $codeShareText->addAttribute("visible","true");
    $codeShareText->addAttribute("keep","true");
    $codeShareText->addAttribute("align","righttop");
    $codeShareText->addAttribute("zorder","14");
    $codeShareText->addAttribute("url","%SWFPATH%/plugins/textfield.swf");
    $codeShareText->addAttribute("html","扫码分享");
    $codeShareText->addAttribute("x","42");
    $codeShareText->addAttribute("y","-15");
    $codeShareText->addAttribute("css","color:#FFFFFF;font-size:14px;width:20px;");
    //  添加 sweep_code_share_pc
    $sweepCodeSharePc = $fatherControlBarPc->addChild("layer");
    $sweepCodeSharePc->addAttribute("name","sweep_code_share_pc");
    $sweepCodeSharePc->addAttribute("url","../../../../static/images/sweepcode-share-pc.png");
    $sweepCodeSharePc->addAttribute("align","righttop");
    $sweepCodeSharePc->addAttribute("zorder","14");
    $sweepCodeSharePc->addAttribute("scale","0.95");
    $sweepCodeSharePc->addAttribute("keep","true");
    $sweepCodeSharePc->addAttribute("x","10");
    $sweepCodeSharePc->addAttribute("y","5");
    $sweepCodeSharePc->addAttribute("width","54");
    $sweepCodeSharePc->addAttribute("height","54");
    $sweepCodeSharePc->addAttribute("style","skin_base|skin_glow");
    $sweepCodeSharePc->addAttribute("onover","if(layer[share_saoma].visible,remove_iframe(share_saoma);set(layer[share_saoma].visible,false),call_iframe(share_saoma,/pano/krpano/share/".$panoId."););");
    $sweepCodeSharePc->addAttribute("onout","if(layer[share_saoma].visible,remove_iframe(share_saoma);set(layer[share_saoma].visible,false),call_iframe(share_saoma,/pano/krpano/share/".$panoId."););");

    // 添加 select_room_text
    $selectRoomText = $fatherControlBarPc->addChild("layer");
    $selectRoomText->addAttribute("name","select_room_text");
    $selectRoomText->addAttribute("background","false");
    $selectRoomText->addAttribute("visible","true");
    $selectRoomText->addAttribute("keep","true");
    $selectRoomText->addAttribute("align","righttop");
    $selectRoomText->addAttribute("zorder","14");
    $selectRoomText->addAttribute("url","%SWFPATH%/plugins/textfield.swf");
    $selectRoomText->addAttribute("html","房间选择");
    $selectRoomText->addAttribute("x","122");
    $selectRoomText->addAttribute("y","-15");
    $selectRoomText->addAttribute("css","color:#FFFFFF;font-size:14px;width:20px;");
    // 添加 select_room_pc
    $selectRoomPc = $fatherControlBarPc->addChild("layer");
    $selectRoomPc->addAttribute("name","select_room_pc");
    $selectRoomPc->addAttribute("url","../../../../static/images/selectroom-pc.png");
    $selectRoomPc->addAttribute("align","righttop");
    $selectRoomPc->addAttribute("zorder","14");
    $selectRoomPc->addAttribute("scale","0.95");
    $selectRoomPc->addAttribute("keep","true");
    $selectRoomPc->addAttribute("x","90");
    $selectRoomPc->addAttribute("y","5");
    $selectRoomPc->addAttribute("width","54");
    $selectRoomPc->addAttribute("height","54");
    $selectRoomPc->addAttribute("ondown2","skin_showmap(false); skin_showthumbs();");
    $selectRoomPc->addAttribute("style","skin_base|skin_glow");

    //父级导航
    $fatherControlBar =  $skin_scroll_layer->addChild("layer");
    $fatherControlBar->addAttribute("name","father_control_bar");
    $fatherControlBar->addAttribute("url","../../../../static/images/back_bar.png");
    $fatherControlBar->addAttribute("align","top");
    $fatherControlBar->addAttribute("width","90%");
    $fatherControlBar->addAttribute("height","60");
    $fatherControlBar->addAttribute("y","-75");
    $fatherControlBar->addAttribute("alpha","1");
    $fatherControlBar->addAttribute("devices","touchdevice");

    //添加 skin_title 层
    $skin_title = $fatherControlBar->addChild("layer");
    $skin_title->addAttribute("name","skin_title");
    $skin_title->addAttribute("type","text");
    $skin_title->addAttribute("align","righttop");
    $skin_title->addAttribute("edge","centerbuttom");
    $skin_title->addAttribute("x","14%");
    $skin_title->addAttribute("y","30");
    $skin_title->addAttribute("zorder","4");
    $skin_title->addAttribute("enabled","true");
    $skin_title->addAttribute("bg","false");
    $skin_title->addAttribute("css","calc:skin_settings.design_text_css + 'font-size:15px;text-align:center;line-height:30px'");
    $skin_title->addAttribute("textshadow","get:skin_settings.design_text_shadow");
    $skin_title->addAttribute("visible","true");
    $skin_title->addAttribute("onautosized","skin_video_updateseekbarwidth();");
    $skin_title->addAttribute("style","skin_base|skin_glow");
    $skin_title->addAttribute("width","250");
    $skin_title->addAttribute("ondown2","skin_showmap(false); skin_showthumbs();");
    //添加icon_title_down
    $iconTitleDown = $skin_title->addChild("layer");
    $iconTitleDown->addAttribute("name","icon_title_down");
    $iconTitleDown->addAttribute("url","../../../../static/images/arrow-down-fill.png");
    $iconTitleDown->addAttribute("align","leftcenter");
    $iconTitleDown->addAttribute("x","56%");
    $iconTitleDown->addAttribute("scale","0.25");


    //添加 skin_user 层
    $skin_user = $fatherControlBar->addChild("layer");
    $skin_user->addAttribute("name","skin_user");
    $skin_user->addAttribute("url",$agentImgUrl?$agentImgUrl:"../../../../static/images/manager.png");
    $skin_user->addAttribute("zorder","24");
    $skin_user->addAttribute("keep","true");
    $skin_user->addAttribute("align","lefttop");
    $skin_user->addAttribute("edge","leftbottom");
    $skin_user->addAttribute("width","40");
    $skin_user->addAttribute("height","42");
    $skin_user->addAttribute("bg","true");
    $skin_user->addAttribute("x","4%");
    $skin_user->addAttribute("y","52");
    $skin_user->addAttribute("onclick","if(layer[iframelayer_new].visible,
                                    remove_iframe(iframelayer_new);
                                    set(layer[iframelayer_new].visible,false),
                                    set(layer[set_alert_table].visible,false);
                                    call_iframe(iframelayer_new,/pano/krpano/vr/".$panoId."););");



    //添加 skin_talk 层
    $skin_talk = $fatherControlBar->addChild("layer");
    $skin_talk->addAttribute("name","skin_talk");
    $skin_talk->addAttribute("url","../../../../static/images/online.png");
    $skin_talk->addAttribute("zorder","24");
    $skin_talk->addAttribute("keep","true");
    $skin_talk->addAttribute("align","lefttop");
    $skin_talk->addAttribute("style","skin_base|skin_glow");
    $skin_talk->addAttribute("width","160");
    $skin_talk->addAttribute("height","64");
    $skin_talk->addAttribute("bg","false");
    $skin_talk->addAttribute("x","20%");
    $skin_talk->addAttribute("y","15");
    $skin_talk->addAttribute("scale","0.5");


    //添加 skin_phone 层
    $skin_phone = $fatherControlBar->addChild("layer");
    $skin_phone->addAttribute("name","skin_phone");
    $skin_phone->addAttribute("url","../../../../static/images/phone.png");
    $skin_phone->addAttribute("zorder","24");
    $skin_phone->addAttribute("keep","true");
    $skin_phone->addAttribute("align","lefttop");
    $skin_phone->addAttribute("style","skin_base|skin_glow");
    $skin_phone->addAttribute("width","160");
    $skin_phone->addAttribute("height","64");
    $skin_phone->addAttribute("bg","false");
    $skin_phone->addAttribute("x","48%");
    $skin_phone->addAttribute("y","15");
    $skin_phone->addAttribute("scale","0.5");
    $skin_phone->addAttribute("onclick","openurl('tel:123456789')");


    //隐藏整个 [skin_control_bar] 层 为了自定义这个层 todo
    $skin_control_bar = $vtourXmlObj->layer[2]->layer[2];
    $skin_control_bar['y'] = "-9999999";

    //废弃皮肤自带的title 层
    $skin_title_old = $skin_scroll_window->layer[0]->layer[0];
    $skin_title_old['name'] = "skin_title_old";


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
    $layerContainer->setAttribute("url", "../../../static/images/background.jpg");
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
    $layerLoadingBack->setAttribute("devices","touchdevice");
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
    $loadingBackIcon->setAttribute("url","../../../static/images/loading-left.png");
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
    $layerLogo->setAttribute("url", "../../../static/images/logo.png");
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
 * 分享弹窗
 * @param null $xmlFile
 */

function alertShare($xmlFile = null)
{
    $vtourDocXml = new \DOMDocument();
    $vtourDocXml->load($xmlFile);
    //根节点
    $krpanoNode = $vtourDocXml->getElementsByTagName("krpano")->item(0);

    //share_alert_table
    $shareAlertTable = $vtourDocXml->createElement("layer");
    $shareAlertTable->setAttribute("name","share_alert_table");
    $shareAlertTable->setAttribute("width","102%");
    $shareAlertTable->setAttribute("height","102%");
    $shareAlertTable->setAttribute("keep","true");
    $shareAlertTable->setAttribute("visible","false");
    $shareAlertTable->setAttribute("zorder","20");
    $shareAlertTable->setAttribute("devices","touchdevice");
    $shareAlertTable->setAttribute("backgroundcolor","0x000000");
    $shareAlertTable->setAttribute("backgroundalpha","0.5");
    $shareAlertTable->setAttribute("url","%SWFPATH%/plugins/textfield.swf");
    //share_buttom_table
    $shareButtomTable = $vtourDocXml->createElement("layer");
    $shareButtomTable->setAttribute("name","share_buttom_table");
    $shareButtomTable->setAttribute("width","102%");
    $shareButtomTable->setAttribute("height","30%");
    $shareButtomTable->setAttribute("keep","true");
    $shareButtomTable->setAttribute("zorder","22");
    $shareButtomTable->setAttribute("align","centerbuttom");
    $shareButtomTable->setAttribute("y","40%");
    $shareButtomTable->setAttribute("backgroundcolor","0x00FFFFFF");
    $shareButtomTable->setAttribute("url","%SWFPATH%/plugins/textfield.swf");
    $shareAlertTable->appendChild($shareButtomTable);
    //share_buttom_line
    $shareButtomLine = $vtourDocXml->createElement("layer");
    $shareButtomLine->setAttribute("name","share_buttom_line");
    $shareButtomLine->setAttribute("width","102%");
    $shareButtomLine->setAttribute("height","1");
    $shareButtomLine->setAttribute("keep","true");
    $shareButtomLine->setAttribute("zorder","25");
    $shareButtomLine->setAttribute("align","centerbuttom");
    $shareButtomLine->setAttribute("backgroundcolor","0x00666666");
    $shareButtomLine->setAttribute("backgroundalpha","0.2");
    $shareButtomLine->setAttribute("url","%SWFPATH%/plugins/textfield.swf");
    $shareButtomTable->appendChild($shareButtomLine);
    //share_weixin
    $shareWeixin = $vtourDocXml->createElement("layer");
    $shareWeixin->setAttribute("name","share_weixin");
    $shareWeixin->setAttribute("url","../../../static/images/weixin.png");
    $shareWeixin->setAttribute("scale","0.5");
    $shareWeixin->setAttribute("zorder","25");
    $shareWeixin->setAttribute("align","leftbuttom");
    $shareWeixin->setAttribute("keep","true");
    $shareWeixin->setAttribute("x","10%");
    $shareWeixin->setAttribute("y","-56");
    $shareButtomTable->appendChild($shareWeixin);
    //share_weixin_text
    $shareWeixinText = $vtourDocXml->createElement("layer");
    $shareWeixinText->setAttribute("name","share_weixin_text");
    $shareWeixinText->setAttribute("background","false");
    $shareWeixinText->setAttribute("keep","true");
    $shareWeixinText->setAttribute("align","leftbuttom");
    $shareWeixinText->setAttribute("zorder","25");
    $shareWeixinText->setAttribute("url","%SWFPATH%/plugins/textfield.swf");
    $shareWeixinText->setAttribute("html","微信好友");
    $shareWeixinText->setAttribute("x","10%");
    $shareWeixinText->setAttribute("y","-20");
    $shareWeixinText->setAttribute("css","color:#ccc;font-size:13px;font-family:Arial;");
    $shareButtomTable->appendChild($shareWeixinText);
    //share_friend
    $shareFriend = $vtourDocXml->createElement("layer");
    $shareFriend->setAttribute("name","share_friend");
    $shareFriend->setAttribute("url","../../../static/images/friends.png");
    $shareFriend->setAttribute("scale","0.5");
    $shareFriend->setAttribute("zorder","25");
    $shareFriend->setAttribute("align","leftbuttom");
    $shareFriend->setAttribute("keep","true");
    $shareFriend->setAttribute("x","43%");
    $shareFriend->setAttribute("y","-56");
    $shareButtomTable->appendChild($shareFriend);
    //  share_friend_text
    $shareFriendText = $vtourDocXml->createElement("layer");
    $shareFriendText->setAttribute("name","share_friend_text");
    $shareFriendText->setAttribute("background","false");
    $shareFriendText->setAttribute("keep","true");
    $shareFriendText->setAttribute("align","leftbuttom");
    $shareFriendText->setAttribute("zorder","25");
    $shareFriendText->setAttribute("url","%SWFPATH%/plugins/textfield.swf");
    $shareFriendText->setAttribute("html","朋友圈");
    $shareFriendText->setAttribute("x","44%");
    $shareFriendText->setAttribute("y","-20");
    $shareFriendText->setAttribute("css","color:#ccc;font-size:13px;font-family:Arial;");
    $shareButtomTable->appendChild($shareFriendText);
    //share_copy_link
    $shareCopyLink =  $vtourDocXml->createElement("layer");
    $shareCopyLink->setAttribute("name","share_copy_link");
    $shareCopyLink->setAttribute("url","../../../static/images/copylink.png");
    $shareCopyLink->setAttribute("scale","0.5");
    $shareCopyLink->setAttribute("zorder","25");
    $shareCopyLink->setAttribute("align","leftbuttom");
    $shareCopyLink->setAttribute("keep","true");
    $shareCopyLink->setAttribute("x","76%");
    $shareCopyLink->setAttribute("y","-56");
    $shareButtomTable->appendChild($shareCopyLink);
    // share_copylink_text
    $shareCopylinkText = $vtourDocXml->createElement("layer");
    $shareCopylinkText->setAttribute("name","share_copylink_text");
    $shareCopylinkText->setAttribute("background","false");
    $shareCopylinkText->setAttribute("keep","true");
    $shareCopylinkText->setAttribute("align","leftbuttom");
    $shareCopylinkText->setAttribute("zorder","25");
    $shareCopylinkText->setAttribute("url","%SWFPATH%/plugins/textfield.swf");
    $shareCopylinkText->setAttribute("html","复制链接");
    $shareCopylinkText->setAttribute("x","76%");
    $shareCopylinkText->setAttribute("y","-20");
    $shareCopylinkText->setAttribute("css","color:#ccc;font-size:13px;font-family:Arial;");
    $shareButtomTable->appendChild($shareCopylinkText);
    // share_cancel
    $shareCancel = $vtourDocXml->createElement("layer");
    $shareCancel->setAttribute("name","share_cancel");
    $shareCancel->setAttribute("background","false");
    $shareCancel->setAttribute("keep","true");
    $shareCancel->setAttribute("align","centerbuttom");
    $shareCancel->setAttribute("zorder","32");
    $shareCancel->setAttribute("url","%SWFPATH%/plugins/textfield.swf");
    $shareCancel->setAttribute("html","取消");
    $shareCancel->setAttribute("y","30");
    $shareCancel->setAttribute("backgroundcolor","0x002C1F18");
    $shareCancel->setAttribute("css","font-size:15px;font-family:Arial;font-weight:bold;");
    $shareCancel->setAttribute("onclick","if(layer[share_alert_table].visible,
                    set(layer[share_alert_table].visible,false),set(layer[share_alert_table].visible,true);)");

    $shareButtomTable->appendChild($shareCancel);

    $krpanoNode->appendChild($shareAlertTable);
    $vtourDocXml->save($xmlFile);
}


/**
 * 设置弹窗
 * @param null $xmlFile
 */
function addSetting($xmlFile = null)
{
    $vtourDocXml = new \DOMDocument();
    $vtourDocXml->load($xmlFile);
    //根节点
    $krpanoNode = $vtourDocXml->getElementsByTagName("krpano")->item(0);

    // set_alert_table
    $setAlertTable = $vtourDocXml->createElement("layer");
    $setAlertTable->setAttribute("name","set_alert_table");
    $setAlertTable->setAttribute("url","../../../static/images/set-alert.png");
    $setAlertTable->setAttribute("scale","0.7");
    $setAlertTable->setAttribute("zorder","20");
    $setAlertTable->setAttribute("align","topcenter");
    $setAlertTable->setAttribute("keep","true");
    $setAlertTable->setAttribute("x","0");
    $setAlertTable->setAttribute("y","30%");
    $setAlertTable->setAttribute("visible","false");
    $setAlertTable->setAttribute("devices","all");

    // set_title_text
    $setTitleText = $vtourDocXml->createElement("layer");
    $setTitleText->setAttribute("name","set_title_text");
    $setTitleText->setAttribute("background","false");
    $setTitleText->setAttribute("url","%SWFPATH%/plugins/textfield.swf");
    $setTitleText->setAttribute("align","topcenter");
    $setTitleText->setAttribute("y","18%");
    $setTitleText->setAttribute("zorder","32");
    $setTitleText->setAttribute("html","设置");
    $setTitleText->setAttribute("devices","all");
    $setTitleText->setAttribute("css","text-align:center;color:#FFFFFF;font-family:Arial;font-weight:bold;font-size:16px;");

    // gyro_control_text
    $gyroControlText = $vtourDocXml->createElement("layer");
    $gyroControlText->setAttribute("name","gyro_control_text");
    $gyroControlText->setAttribute("background","false");
    $gyroControlText->setAttribute("url","%SWFPATH%/plugins/textfield.swf");
    $gyroControlText->setAttribute("align","lefttop");
    $gyroControlText->setAttribute("x","14%");
    $gyroControlText->setAttribute("y","38%");
    $gyroControlText->setAttribute("zorder","32");
    $gyroControlText->setAttribute("html","陀螺仪");
    $gyroControlText->setAttribute("devices","all");
    $gyroControlText->setAttribute("css","text-align:center;color:#FFFFFF;font-family:Arial;font-weight:bold;font-size:12px;");

    // gyro_fill_space
    $gyroFillSpace = $vtourDocXml->createElement("layer");
    $gyroFillSpace->setAttribute("name","gyro_fill_space");
    $gyroFillSpace->setAttribute("backgroundcolor","0x00FFFFFF");
    $gyroFillSpace->setAttribute("roundedge","25");
    $gyroFillSpace->setAttribute("align","righttop");
    $gyroFillSpace->setAttribute("x","14%");
    $gyroFillSpace->setAttribute("y","40%");
    $gyroFillSpace->setAttribute("width","30");
    $gyroFillSpace->setAttribute("height","15");
    $gyroFillSpace->setAttribute("devices","all");
    $gyroFillSpace->setAttribute("url","%SWFPATH%/plugins/textfield.swf");
    $gyroFillSpace->setAttribute("onclick","if(layer[gyro_on].visible,
               set(skin_settings.gyro,false);set(layer[gyro_on].visible,false);set(layer[gyro_off].visible,true),
               set(skin_settings.gyro,true);set(layer[gyro_off].visible,false);set(layer[gyro_on].visible,true););");
    // gyro_off
    $gyroOff = $vtourDocXml->createElement("layer");
    $gyroOff->setAttribute("name","gyro_off");
    $gyroOff->setAttribute("backgroundcolor","0x007F7F7F");
    $gyroOff->setAttribute("roundedge","50");
    $gyroOff->setAttribute("visible","false");
    $gyroOff->setAttribute("align","leftcenter");
    $gyroOff->setAttribute("x","4%");
    $gyroOff->setAttribute("width","14");
    $gyroOff->setAttribute("height","13");
    $gyroOff->setAttribute("devices","all");
    $gyroOff->setAttribute("url","%SWFPATH%/plugins/textfield.swf");
    // gyro_on
    $gyroOn = $vtourDocXml->createElement("layer");
    $gyroOn->setAttribute("name","gyro_on");
    $gyroOn->setAttribute("backgroundcolor","0x006CDA00");
    $gyroOn->setAttribute("roundedge","50");
    $gyroOn->setAttribute("visible","true");
    $gyroOn->setAttribute("align","rightcenter");
    $gyroOn->setAttribute("x","4%");
    $gyroOn->setAttribute("width","14");
    $gyroOn->setAttribute("height","13");
    $gyroOn->setAttribute("devices","all");
    $gyroOn->setAttribute("url","%SWFPATH%/plugins/textfield.swf");
    $gyroFillSpace->appendChild($gyroOff);
    $gyroFillSpace->appendChild($gyroOn);

    // rotate_control_text
    $rotateControlText = $vtourDocXml->createElement("layer");
    $rotateControlText->setAttribute("name","rotate_control_text");
    $rotateControlText->setAttribute("background","false");
    $rotateControlText->setAttribute("url","%SWFPATH%/plugins/textfield.swf");
    $rotateControlText->setAttribute("align","lefttop");
    $rotateControlText->setAttribute("x","14%");
    $rotateControlText->setAttribute("y","62%");
    $rotateControlText->setAttribute("zorder","32");
    $rotateControlText->setAttribute("html","自动旋转");
    $rotateControlText->setAttribute("devices","all");
    $rotateControlText->setAttribute("css","text-align:center;color:#FFFFFF;font-family:Arial;font-weight:bold;font-size:12px;");
    //rotate_fill_space
    $rotateFillSpace = $vtourDocXml->createElement("layer");
    $rotateFillSpace->setAttribute("name","rotate_fill_space");
    $rotateFillSpace->setAttribute("backgroundcolor","0x00FFFFFF");
    $rotateFillSpace->setAttribute("roundedge","25");
    $rotateFillSpace->setAttribute("align","righttop");
    $rotateFillSpace->setAttribute("x","14%");
    $rotateFillSpace->setAttribute("y","62%");
    $rotateFillSpace->setAttribute("width","30");
    $rotateFillSpace->setAttribute("height","15");
    $rotateFillSpace->setAttribute("devices","all");
    $rotateFillSpace->setAttribute("url","%SWFPATH%/plugins/textfield.swf");
    $rotateFillSpace->setAttribute("onclick","if(layer[rotate_on].visible,set(autorotate.enabled,false);
               set(layer[rotate_on].visible,false);set(layer[rotate_off].visible,true),set(autorotate.enabled,true);
                set(layer[rotate_off].visible,false);set(layer[rotate_on].visible,true););");
    //rotate_off
    $rotateOff = $vtourDocXml->createElement("layer");
    $rotateOff->setAttribute("name","rotate_off");
    $rotateOff->setAttribute("backgroundcolor","0x007F7F7F");
    $rotateOff->setAttribute("roundedge","50");
    $rotateOff->setAttribute("visible","false");
    $rotateOff->setAttribute("align","leftcenter");
    $rotateOff->setAttribute("x","4%");
    $rotateOff->setAttribute("width","14");
    $rotateOff->setAttribute("height","13");
    $rotateOff->setAttribute("devices","all");
    $rotateOff->setAttribute("url","%SWFPATH%/plugins/textfield.swf");
    // rotate_on
    $rotateOn = $vtourDocXml->createElement("layer");
    $rotateOn->setAttribute("name","rotate_on");
    $rotateOn->setAttribute("backgroundcolor","0x006CDA00");
    $rotateOn->setAttribute("roundedge","50");
    $rotateOn->setAttribute("visible","true");
    $rotateOn->setAttribute("align","rightcenter");
    $rotateOn->setAttribute("x","4%");
    $rotateOn->setAttribute("width","14");
    $rotateOn->setAttribute("height","13");
    $rotateOn->setAttribute("devices","all");
    $rotateOn->setAttribute("url","%SWFPATH%/plugins/textfield.swf");
    $rotateFillSpace->appendChild($rotateOff);
    $rotateFillSpace->appendChild($rotateOn);

    $setAlertTable->appendChild($setTitleText);
    $setAlertTable->appendChild($gyroControlText);
    $setAlertTable->appendChild($gyroFillSpace);
    $setAlertTable->appendChild($rotateControlText);
    $setAlertTable->appendChild($rotateFillSpace);

    $krpanoNode->appendChild($setAlertTable);
    $vtourDocXml->save($xmlFile);
}

/**
 * VR 相关设置
 * @param null $xmlFile
 */
function addVr($xmlFile = null)
{
    $vtourDocXml = new \DOMDocument();
    $vtourDocXml->load($xmlFile);
    //根节点
    $krpanoNode = $vtourDocXml->getElementsByTagName("krpano")->item(0);

    /**
     * VR模式
     */
    // vr_all_screen
    $vrAllScreen = $vtourDocXml->createElement("layer");
    $vrAllScreen->setAttribute("name","vr_all_screen");
    $vrAllScreen->setAttribute("width","100%");
    $vrAllScreen->setAttribute("height","100%");
    $vrAllScreen->setAttribute("keep","true");
    $vrAllScreen->setAttribute("visible","false");
    $vrAllScreen->setAttribute("zorder","30");
    $vrAllScreen->setAttribute("devices","touchdevice");
    $vrAllScreen->setAttribute("backgroundcolor","0x000000");
    $vrAllScreen->setAttribute("backgroundalpha","0.5");
    $vrAllScreen->setAttribute("url","%SWFPATH%/plugins/textfield.swf");
    // vr_title_master
    $vrTitleMaster = $vtourDocXml->createElement("layer");
    $vrTitleMaster->setAttribute("name","vr_title_master");
    $vrTitleMaster->setAttribute("background","false");
    $vrTitleMaster->setAttribute("keep","true");
    $vrTitleMaster->setAttribute("url","%SWFPATH%/plugins/textfield.swf");
    $vrTitleMaster->setAttribute("align","lefttop");
    $vrTitleMaster->setAttribute("x","25%");
    $vrTitleMaster->setAttribute("y","25%");
    $vrTitleMaster->setAttribute("zorder","32");
    $vrTitleMaster->setAttribute("html","VR虚拟现实体验模式");
    $vrTitleMaster->setAttribute("css","text-align:center;color:#FFFFFF;font-family:Arial;font-weight:bold;font-size:20px;");
    // vr_title_one
    $vrTitleOne = $vtourDocXml->createElement("layer");
    $vrTitleOne->setAttribute("name","vr_title_one");
    $vrTitleOne->setAttribute("background","false");
    $vrTitleOne->setAttribute("keep","true");
    $vrTitleOne->setAttribute("url","%SWFPATH%/plugins/textfield.swf");
    $vrTitleOne->setAttribute("align","lefttop");
    $vrTitleOne->setAttribute("x","25%");
    $vrTitleOne->setAttribute("y","35%");
    $vrTitleOne->setAttribute("zorder","32");
    $vrTitleOne->setAttribute("html","1、解除手机自带旋转锁定");
    $vrTitleOne->setAttribute("alpha","0.9");
    $vrTitleOne->setAttribute("css","text-align:center;color:#F3F3F3;font-family:Arial;font-size:14px;");
    // vr_title_two
    $vrTitleTwo = $vtourDocXml->createElement("layer");
    $vrTitleTwo->setAttribute("name","vr_title_two");
    $vrTitleTwo->setAttribute("background","false");
    $vrTitleTwo->setAttribute("keep","true");
    $vrTitleTwo->setAttribute("url","%SWFPATH%/plugins/textfield.swf");
    $vrTitleTwo->setAttribute("align","lefttop");
    $vrTitleTwo->setAttribute("x","25%");
    $vrTitleTwo->setAttribute("y","40.5%");
    $vrTitleTwo->setAttribute("zorder","32");
    $vrTitleTwo->setAttribute("html","2、旋转手机至横屏模式");
    $vrTitleTwo->setAttribute("alpha","0.9");
    $vrTitleTwo->setAttribute("css","text-align:center;color:#F3F3F3;font-family:Arial;font-size:14px;");
    // vr_title_three
    $vrTitleThree = $vtourDocXml->createElement("layer");
    $vrTitleThree->setAttribute("name","vr_title_three");
    $vrTitleThree->setAttribute("background","false");
    $vrTitleThree->setAttribute("keep","true");
    $vrTitleThree->setAttribute("url","%SWFPATH%/plugins/textfield.swf");
    $vrTitleThree->setAttribute("align","lefttop");
    $vrTitleThree->setAttribute("x","25%");
    $vrTitleThree->setAttribute("y","46%");
    $vrTitleThree->setAttribute("zorder","32");
    $vrTitleThree->setAttribute("html","3、佩戴VR眼镜,感受沉浸式体验");
    $vrTitleThree->setAttribute("alpha","0.9");
    $vrTitleThree->setAttribute("css","text-align:center;color:#F3F3F3;font-family:Arial;font-size:14px;");
    // vr_tiyan_button
    $vrTiyanButton = $vtourDocXml->createElement("layer");
    $vrTiyanButton->setAttribute("name","vr_tiyan_button");
    $vrTiyanButton->setAttribute("url","../../../static/images/tiyan-back.png");
    $vrTiyanButton->setAttribute("scale","0.45");
    $vrTiyanButton->setAttribute("zorder","32");
    $vrTiyanButton->setAttribute("align","lefttop");
    $vrTiyanButton->setAttribute("x","25%");
    $vrTiyanButton->setAttribute("y","56%");
    $vrTiyanButton->setAttribute("width","100%");
    $vrTiyanButton->setAttribute("height","");
    $vrTiyanButton->setAttribute("keep","true");
    $vrTiyanButton->setAttribute("onclick","webvr.enterVR();");
    // vr_tiyan_title
    $vrTiyanTitle = $vtourDocXml->createElement("layer");
    $vrTiyanTitle->setAttribute("name","vr_tiyan_title");
    $vrTiyanTitle->setAttribute("background","false");
    $vrTiyanTitle->setAttribute("keep","true");
    $vrTiyanTitle->setAttribute("url","%SWFPATH%/plugins/textfield.swf");
    $vrTiyanTitle->setAttribute("align","center");
    $vrTiyanTitle->setAttribute("x","");
    $vrTiyanTitle->setAttribute("y","-1");
    $vrTiyanTitle->setAttribute("zorder","30");
    $vrTiyanTitle->setAttribute("html","立即体验");
    $vrTiyanTitle->setAttribute("onclick","webvr.enterVR();");
    $vrTiyanTitle->setAttribute("css","text-align:center;color:#F3F3F3;font-family:Arial;font-size:13px;");

    $vrAllScreen->appendChild($vrTitleMaster);
    $vrAllScreen->appendChild($vrTitleOne);
    $vrAllScreen->appendChild($vrTitleTwo);
    $vrAllScreen->appendChild($vrTitleThree);
    $vrAllScreen->appendChild($vrTiyanButton);
    $vrTiyanButton->appendChild($vrTiyanTitle);

    $krpanoNode->appendChild($vrAllScreen);
    $vtourDocXml->save($xmlFile);
}


/**
 * 漫游场景中嵌入房源信息层
 * @param null $xmlFile
 */
function addIframeFy($xmlFile = null, $content = '',$paonId)
{
    $vtourDocXml = new \DOMDocument();
    $vtourDocXml->load($xmlFile);
    //根节点
    $krpanoNode = $vtourDocXml->getElementsByTagName("krpano")->item(0);
    //control标签
    $controlNode = $vtourDocXml->createElement("control");
    $controlNode->setAttribute("mousetype", "drag2d");

    //combobox.xml
    $combobox = $vtourDocXml->createElement("include");
    $combobox->setAttribute("url","%SWFPATH%/plugins/combobox.xml");

    //preview标签
    $previewNode = $vtourDocXml->createElement("preview");
    $previewNode->setAttribute("type", "grid();");
    //iframelayer 块
    $layerIframe = $vtourDocXml->createElement("layer");
    $layerIframe->setAttribute("name", "iframelayer");
    $layerIframe->setAttribute("type", "text");
    $layerIframe->setAttribute("align", "center");
    $layerIframe->setAttribute("width", "100%");
    $layerIframe->setAttribute("height", "100%");
    $layerIframe->setAttribute("zorder", "-100");
    $layerIframe->setAttribute("keep", "true");
    $layerIframe->setAttribute("visible", "false");
    $layerIframe->setAttribute("background", "false");
    $layerIframe->setAttribute("backgroundalpha", "0.1");

    //iframelayer_new 快
    $layerIframeNew = $vtourDocXml->createElement("layer");
    $layerIframeNew->setAttribute("name", "iframelayer_new");
    $layerIframeNew->setAttribute("type", "text");
    $layerIframeNew->setAttribute("align", "center");
    $layerIframeNew->setAttribute("width", "100%");
    $layerIframeNew->setAttribute("height", "100%");
    $layerIframeNew->setAttribute("zorder", "-100");
    $layerIframeNew->setAttribute("keep", "true");
    $layerIframeNew->setAttribute("visible", "false");
    $layerIframeNew->setAttribute("background", "false");
    $layerIframeNew->setAttribute("backgroundalpha", "0.1");


    // button_3 按钮
    $layerButton = $vtourDocXml->createElement("layer");
    $layerButton->setAttribute("name", "button_3");
    $layerButton->setAttribute("background", "false");
    $layerButton->setAttribute("keep", "true");
    $layerButton->setAttribute("url", "%SWFPATH%/plugins/textfield.swf");
    $layerButton->setAttribute("enabled", "true");
    $layerButton->setAttribute("align", "topcenter");
    $layerButton->setAttribute("y", "15");
    $layerButton->setAttribute("width", "220");
    $layerButton->setAttribute("height", "32");
    $layerButton->setAttribute("css", "text-align:center;color:#FFFFFF;font-family:Arial;font-weight:bold;font-size:14px;");
    $layerButton->setAttribute("zorder", "15");
    $layerButton->setAttribute("html", $content);
    $layerButton->setAttribute("onclick", "if(layer[iframelayer].visible,set(layer[top_shade_layer].visible,false);
                set(layer[top_back_layer].visible,true);remove_iframe(iframelayer);set(layer[iframelayer].visible,false),
                set(layer[top_back_layer].visible,false);set(layer[top_shade_layer].visible,true);
                set(layer[top_shade_layer_pc].visible,true);set(layer[set_alert_table].visible,false);set(layer[iframelayer_new].visible,false);
                call_iframe(iframelayer,/pano/krpano/fr/".$paonId."););");

    //icon 上下标签图标
    $layerIcon = $vtourDocXml->createElement("layer");
    $layerIcon->setAttribute("name","icon");
    $layerIcon->setAttribute("url","../../../static/images/down.png");
    $layerIcon->setAttribute("zorder","10");
    $layerIcon->setAttribute("scale","1.3");
    $layerIcon->setAttribute("keep","true");
    $layerIcon->setAttribute("align","right");
    $layerIcon->setAttribute("y","-3");
    $layerIcon->setAttribute("x","1");
    //追加至button_3
    $layerButton->appendChild($layerIcon);

    // top_back_layer
    $topBackLayer = $vtourDocXml->createElement("layer");
    $topBackLayer->setAttribute("name","top_back_layer");
    $topBackLayer->setAttribute("keep","true");
    $topBackLayer->setAttribute("url","../../../static/images/top-back.png");
    $topBackLayer->setAttribute("align","top");
    $topBackLayer->setAttribute("width","101%");
    $topBackLayer->setAttribute("height","50");
    $topBackLayer->setAttribute("visible","true");
    $topBackLayer->setAttribute("x","");
    $topBackLayer->setAttribute("y","1");
    $topBackLayer->setAttribute("bgalpha","0.24");
    $topBackLayer->setAttribute("zorder","10");
    $topBackLayer->setAttribute("scale","1");
    $topBackLayer->setAttribute("devices","touchdevice");

    //left_icon
    $leftIcon =  $vtourDocXml->createElement("layer");
    $leftIcon->setAttribute("name","left_icon");
    $leftIcon->setAttribute("url","../../../static/images/loading-left.png");
    $leftIcon->setAttribute("scale","0.18");
    $leftIcon->setAttribute("zorder","12");
    $leftIcon->setAttribute("align","lefttop");
    $leftIcon->setAttribute("style","skin_base|skin_glow");
    $leftIcon->setAttribute("keep","true");
    $leftIcon->setAttribute("x","5%");
    $leftIcon->setAttribute("y","15");
    $leftIcon->setAttribute("devices","touchdevice");

    //right_share
    $rightShare = $vtourDocXml->createElement("layer");
    $rightShare->setAttribute("name","right_share");
    $rightShare->setAttribute("url","../../../static/images/link.png");
    $rightShare->setAttribute("scale","0.16");
    $rightShare->setAttribute("zorder","12");
    $rightShare->setAttribute("align","righttop");
    $rightShare->setAttribute("style","skin_base|skin_glow");
    $rightShare->setAttribute("keep","true");
    $rightShare->setAttribute("x","22");
    $rightShare->setAttribute("y","20");
    $rightShare->setAttribute("devices","touchdevice");
    $rightShare->setAttribute("onclick","if(layer[share_alert_table].visible,set(layer[share_alert_table].visible,false),
                    set(layer[share_alert_table].visible,true);set(layer[set_alert_table].visible,false);)");


    //right_set_pc
    $rightSetPc = $vtourDocXml->createElement("layer");
    $rightSetPc->setAttribute("name","right_set_pc");
    $rightSetPc->setAttribute("url","../../../static/images/set_1.png");
    $rightSetPc->setAttribute("scale","0.95");
    $rightSetPc->setAttribute("width","54");
    $rightSetPc->setAttribute("height","54");
    $rightSetPc->setAttribute("zorder","12");
    $rightSetPc->setAttribute("align","righttop");
    $rightSetPc->setAttribute("style","skin_base|skin_glow");
    $rightSetPc->setAttribute("keep","true");
    $rightSetPc->setAttribute("x","42");
    $rightSetPc->setAttribute("y","190");
    $rightSetPc->setAttribute("visible","true");
    $rightSetPc->setAttribute("devices","html5+!touchdevice");
    $rightSetPc->setAttribute("onclick","if(layer[set_alert_table].visible,
           set(layer[set_alert_table].visible, 'false'),set(layer[iframelayer_new].visible,false);set(layer[iframelayer].visible,false);set(layer[set_alert_table].visible, 'true'););");


    //right_set
    $rightSet = $vtourDocXml->createElement("layer");
    $rightSet->setAttribute("name","right_set");
    $rightSet->setAttribute("url","../../../static/images/set_1.png");
    $rightSet->setAttribute("scale","0.7");
    $rightSet->setAttribute("zorder","12");
    $rightSet->setAttribute("align","righttop");
    $rightSet->setAttribute("style","skin_base|skin_glow");
    $rightSet->setAttribute("keep","true");
    $rightSet->setAttribute("x","25");
    $rightSet->setAttribute("y","150");
    $rightSet->setAttribute("visible","true");
    $rightSet->setAttribute("devices","touchdevice");
    $rightSet->setAttribute("onclick","if(layer[set_alert_table].visible,
           set(layer[set_alert_table].visible, 'false'),set(layer[iframelayer_new].visible,false);set(layer[iframelayer].visible,false);set(layer[set_alert_table].visible, 'true'););");



    //top_shade_layer
    $topShadeLayer = $vtourDocXml->createElement("layer");
    $topShadeLayer->setAttribute("name","top_shade_layer");
    $topShadeLayer->setAttribute("width","65%");
    $topShadeLayer->setAttribute("zorder","12");
    $topShadeLayer->setAttribute("align","top");
    $topShadeLayer->setAttribute("visible","false");
    $topShadeLayer->setAttribute("backgroundalpha","0.2");
    $topShadeLayer->setAttribute("keep","true");
    $topShadeLayer->setAttribute("x","10");
    $topShadeLayer->setAttribute("y","5");
    $topShadeLayer->setAttribute("devices","touchdevice");
    $topShadeLayer->setAttribute("backgroundcolor","0x000000");
    $topShadeLayer->setAttribute("height","45");
    $topShadeLayer->setAttribute("background","true");
    $topShadeLayer->setAttribute("roundedge","25");
    $topShadeLayer->setAttribute("url","%SWFPATH%/plugins/textfield.swf");

    //top_shade_layer_pc
    $topShadeLayerPc = $vtourDocXml->createElement("layer");
    $topShadeLayerPc->setAttribute("name","top_shade_layer_pc");
    $topShadeLayerPc->setAttribute("width","18%");
    $topShadeLayerPc->setAttribute("zorder","12");
    $topShadeLayerPc->setAttribute("align","topcenter");
    $topShadeLayerPc->setAttribute("visible","true");
    $topShadeLayerPc->setAttribute("backgroundalpha","0.4");
    $topShadeLayerPc->setAttribute("keep","true");
    $topShadeLayerPc->setAttribute("y","5");
    $topShadeLayerPc->setAttribute("devices","html5+!touchdevice");
    $topShadeLayerPc->setAttribute("backgroundcolor","0x000000");
    $topShadeLayerPc->setAttribute("height","45");
    $topShadeLayerPc->setAttribute("background","true");
    $topShadeLayerPc->setAttribute("roundedge","25");
    $topShadeLayerPc->setAttribute("url","%SWFPATH%/plugins/textfield.swf");

    //top_screen_pc
    $topScreenPc = $vtourDocXml->createElement("layer");
    $topScreenPc->setAttribute("name","top_screen_pc");
    $topScreenPc->setAttribute("url","../../../static/images/screen-pc.png");
    $topScreenPc->setAttribute("scale","0.95");
    $topScreenPc->setAttribute("width","54");
    $topScreenPc->setAttribute("height","54");
    $topScreenPc->setAttribute("zorder","12");
    $topScreenPc->setAttribute("align","righttop");
    $topScreenPc->setAttribute("style","skin_base|skin_glow");
    $topScreenPc->setAttribute("keep","true");
    $topScreenPc->setAttribute("x","42");
    $topScreenPc->setAttribute("y","50");
    $topScreenPc->setAttribute("onclick","switch(fullscreen);");
    $topScreenPc->setAttribute("devices","html5+!touchdevice");

    //right_vr_pc
    $rightVrPc = $vtourDocXml->createElement("layer");
    $rightVrPc->setAttribute("name","right_vr_pc");
    $rightVrPc->setAttribute("url","../../../static/images/vr_1.png");
    $rightVrPc->setAttribute("scale","0.95");
    $rightVrPc->setAttribute("width","54");
    $rightVrPc->setAttribute("height","54");
    $rightVrPc->setAttribute("zorder","12");
    $rightVrPc->setAttribute("align","righttop");
    $rightVrPc->setAttribute("style","skin_base|skin_glow");
    $rightVrPc->setAttribute("keep","true");
    $rightVrPc->setAttribute("x","42");
    $rightVrPc->setAttribute("y","120");
    $rightVrPc->setAttribute("visible","true");
    $rightVrPc->setAttribute("devices","html5+!touchdevice");
    $rightVrPc->setAttribute("onclick","webvr.enterVR();");

    //right_vr
    $rightVr = $vtourDocXml->createElement("layer");
    $rightVr->setAttribute("name","right_vr");
    $rightVr->setAttribute("url","../../../static/images/vr_1.png");
    $rightVr->setAttribute("scale","0.7");
    $rightVr->setAttribute("zorder","12");
    $rightVr->setAttribute("align","righttop");
    $rightVr->setAttribute("style","skin_base|skin_glow");
    $rightVr->setAttribute("keep","true");
    $rightVr->setAttribute("x","25");
    $rightVr->setAttribute("y","90");
    $rightVr->setAttribute("visible","true");
    $rightVr->setAttribute("devices","touchdevice");
    $rightVr->setAttribute("onclick","if(layer[vr_all_screen].visible,set(layer[vr_all_screen].visible, 'false'),
           set(layer[set_alert_table].visible, 'false');set(layer[vr_all_screen].visible, 'true'););");

    //action  [call_iframe]
    $actionCall = $vtourDocXml->createElement("action");
    $actionCall->setAttribute("name", "call_iframe");
    $actionCall->nodeValue = "callwith(layer[%1],add_iframe(%2, 100%, 100%);set(visible,true));";
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
    $removeJs = "var lastIframe = document.getElementById(args[1]);
		 lastIframe.parentNode.removeChild(lastIframe);";
    $actionRemove->nodeValue = $removeJs;

    //action [draghotspot]
    $actionDraghotspot = $vtourDocXml->createElement("action");
    $actionDraghotspot->setAttribute("name","draghotspot");
    $draghotspotNode = "spheretoscreen(ath, atv, hotspotcenterx, hotspotcentery, 'l');sub(drag_adjustx, mouse.stagex, hotspotcenterx);
    sub(drag_adjusty, mouse.stagey, hotspotcentery);asyncloop(pressed,sub(dx, mouse.stagex, drag_adjustx);sub(dy, mouse.stagey, drag_adjusty);
    screentosphere(dx, dy, ath, atv););";
    $actionDraghotspot->nodeValue = $draghotspotNode;

    //追加至根节点
    $krpanoNode->appendChild($topBackLayer);
    $krpanoNode->appendChild($leftIcon);
    $krpanoNode->appendChild($rightShare);
    $krpanoNode->appendChild($topShadeLayer);
    $krpanoNode->appendChild($topShadeLayerPc);
    $krpanoNode->appendChild($topScreenPc);
    $krpanoNode->appendChild($rightVrPc);
    $krpanoNode->appendChild($rightVr);
    $krpanoNode->appendChild($rightSetPc);
    $krpanoNode->appendChild($rightSet);
    /*$krpanoNode->appendChild($vrAllScreen);*/

    $krpanoNode->appendChild($combobox);
    $krpanoNode->appendChild($controlNode);
    $krpanoNode->appendChild($previewNode);
    $krpanoNode->appendChild($layerIframeNew);
    $krpanoNode->appendChild($layerIframe);
    $krpanoNode->appendChild($layerButton);



    $krpanoNode->appendChild($actionCall);
    $krpanoNode->appendChild($actionAdd);
    $krpanoNode->appendChild($actionRemove);
    $krpanoNode->appendChild($actionDraghotspot);
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
        $tourSceneArr[$i]->hotspot->addAttribute("visible", "false");
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
function changTourXml($xmlFile = null, $mb_name = null,$content,$paonId)
{
    //热点动态操作[后台]
    makeSelectAction($xmlFile);
    //设置的弹窗
    addSetting($xmlFile);
    //分享弹窗
    alertShare($xmlFile);
    //VR的相关操作
    addVr($xmlFile);
    //项目启动画面
    startpic($xmlFile);

    //漫游场景中嵌入房源信息层
    addIframeFy($xmlFile, $content,$paonId);

    //修改tour.xml配置
    $tourXmlStr = file_get_contents($xmlFile);
    $tourXmlObj = new \SimpleXMLElement($tourXmlStr);


    //去掉根节点的title
    $tourXmlObj->krpano['title'] = "";
    $tourXmlObj->krpano->addAttribute("idletime","0.0001");

    /*********************
     * 自动旋转设置
     *********************/
    $tourXmlObj->addChild("autorotate");
    $tourXmlObj->autorotate->addAttribute("enabled", "true");
    $tourXmlObj->autorotate->addAttribute("waittime", "0.5");
    $tourXmlObj->autorotate->addAttribute("speed", "20.0");

    /*************************
     * 更改skin_settings配置
     *************************/
    $tourXmlObj->skin_settings['thumbs_opened'] = "false";           //是否在启动时弹出缩略图一栏
    $tourXmlObj->skin_settings['thumbs_text'] = "true";             //是否在缩略图上显示名字
    $tourXmlObj->skin_settings['title'] = "true";                   //是否左下角显示title
    $tourXmlObj->skin_settings['loadingtext'] = "";                 //在全景图载入中时显示的文字
    $tourXmlObj->skin_settings['thumbs_width'] = "100";             //缩略图宽
    $tourXmlObj->skin_settings['thumbs_height'] = "75";             //缩略图高

    $tourXmlObj->skin_settings['deeplinking'] = "false";             //打开带场景ID 的 url

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
    $tourXmlObj->skin_settings['layout_width'] = "100%";              //导航条容器相对屏幕宽度的百分比
    $tourXmlObj->skin_settings['layout_maxwidth'] = "100%";           //导航条容器的最大宽度像素
    $tourXmlObj->skin_settings['controlbar_offset_closed'] = "-40";   //导航条隐藏状态时与屏幕底部的距离

    $tourXmlObj->skin_settings['controlbar_offset'] = "110";          //导航条背景与屏幕底部的距离   [控制条皮肤]
    $tourXmlObj->skin_settings['controlbar_overlap.no-fractionalscaling'] = "50";       //   [控制条皮肤]
    $tourXmlObj->skin_settings['controlbar_overlap.fractionalscaling'] = "50";          //   [控制条皮肤]



    $tourSceneArr = $tourXmlObj->xpath('scene');
    //添加热点 [hotspot]
    //addHotSpot($tourSceneArr);

    /**********************
     * 循环更改xml场景title
     **********************/
    keepArrKey($mb_name, $tourSceneArr, 'title');
    //渲染xml
    file_put_contents($xmlFile, $tourXmlObj->asXML());

}
