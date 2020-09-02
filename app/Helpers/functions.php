<?php

/**
 * 打印调试
 * @param $data
 */
function d($data)
{
    echo '<pre>';
    var_dump($data);
    echo '</pre>';
}

/**
 * @param null $path
 * 删除目录以及该目录下的所有子目录文件
 */
function clearDir($path = null)
{
    if (is_dir($path)) {
        $p = scandir($path);
        foreach ($p as $val) {
            if ($val != "." && $val != "..") {
                if (is_dir($path . $val)) {         //如果是目录则递归子目录，继续操作
                    clearDir($path . $val . '/');      //子目录中操作删除文件夹和文件
                    @rmdir($path . $val . '/');     //目录清空后删除空文件夹
                } else {
                    unlink($path . $val);         //文件则直接删除
                }
            }
        }
    }
}

/**
 * 一维数组过滤 保持key
 * @param $arr
 * @return array
 */
function filterArr($arr)
{
    $newArr = array();
    foreach ($arr as $val) {
        if ($val) {
            $newArr[] = $val;
        }
    }
    return $newArr;
}



/**
 * 索引数组key替换成大写字母
 * @param $arr
 * @param $temp
 * @return array|false
 */
function numAbc($arr, $temp)
{
    //$temp = array("A","B","C","D","E","G","H","I");
    $diff = count($temp) - count($arr);
    if ($diff >= 0) {
        $newTemp = array_slice($temp, 0, count($arr));
        $newArr = array_combine($newTemp, $arr);
        return $newArr;
    }

}


function houseApi($propertyCode, $CityID, $VrUrl, $VRTitleUrl, $PlatForm)
{
    $url = "http://wf.t.jjw.com:8889/Api/PropertyExploration/VRPropertyExplorationCallback";
    //请求头
    //$headers[]  =  "Content-Type:application/x-www-form-urlencoded";
    $headers[] = "Content-Type:application/json";
    $headers[] = "ERPSignSecret:E10ADC3949BA59ABBE56E057F20F883E";
    $headers[] = "ERPVersion: JJW";
    $headers[] = "PlatForm:1";
    $headers[] = "Client-Mac:Accept-Encoding:gzip";
    $headers[] = "Cookie:ERP-Test=BD429CD317369A5C0F1D8B8730E5491BAA198F75E061BDC0D13045F823BB98B7069D55546F5ED99ED5E725F80F6821300008AB8EC7F01D81F592665FB0B8402957A352CA400DA30F2EC6695344496AD9E9458E66F7FC018F5498A7D23FEC2E3C2FAFFE694A235AF30A2194E816B56CB50E953FEC7882EDE8B7638278C0CFE721347C477BC8743E829FB92DE30DB84714C0D2AE32E12A064E74BD4C23BD8BA879DC29C4A38E5214BB891AF26E28AB55B44036F42DEA286212091E2FF8B31E33BC";

    //初始化
    $curl = curl_init();
    //设置抓取的url
    curl_setopt($curl, CURLOPT_URL, $url);
    //设置头文件的信息作为数据流输出
    curl_setopt($curl, CURLOPT_HEADER, 0);
    //设置请求头
    curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);
    //设置获取的信息以文件流的形式返回，而不是直接输出。
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
    //设置post方式提交
    curl_setopt($curl, CURLOPT_POST, 1);
    //设置头文件的信息作为数据流输出
    //curl_setopt($curl, CURLOPT_HEADER, 1);
    $post_data = array(
        "CityID" => $CityID,
        'PropertyCode' => $propertyCode,
        'VrUrl' => $VrUrl,
        'VRTitleUrl' => $VRTitleUrl,
        'PlatForm' => $PlatForm,
    );
    $post_data = json_encode($post_data);
    curl_setopt($curl, CURLOPT_POSTFIELDS, $post_data);
    //执行命令
    $data = curl_exec($curl);
    //关闭URL请求
    curl_close($curl);
    //显示获得的数据
    print_r($data);
}

//tour.xml 里的Title
function editTourTitle($tourXml, $title)
{
        //$tourXml = storage_path("panos") . "\\" . $gid . "\\vtour\\tour.xml";
        //$tourXml = storage_path("panos") . "\\" . $gid . "\\vtour\\tour_pro.xml";
        $tourXmlStr = file_get_contents($tourXml);
        $tourXmlObj = new \SimpleXMLElement($tourXmlStr);
        $button_title = $tourXmlObj->layer[16];
        $button_title["html"] = $title;
        file_put_contents($tourXml, $tourXmlObj->asXML());
}


//全景启动页进度条PC 和 移动端
function editTourStart($tourXml,$flag){
    //$tourXml = storage_path("panos") . "\\" . $gid . "\\vtour\\tour.xml";
    //$tourXml = storage_path("panos") . "\\" . $gid . "\\vtour\\tour_pro.xml";
    $tourXmlStr = file_get_contents($tourXml);
    $tourXmlObj = new \SimpleXMLElement($tourXmlStr);
    $startpic_container = $tourXmlObj->layer[3];
    $skin_title_logo3 = $startpic_container->layer[0];
    $loadingbar_bg = $startpic_container->layer[1];
    $loadingpercent_text = $startpic_container->layer[2];

    if ($flag == "pc"){
        $startpic_container["devices"] = "html5+!touchdevice";
        $skin_title_logo3["scale"] = "1";
        $loadingbar_bg["x"] = "-10";
        $loadingbar_bg["y"] = "540";
        $loadingbar_bg["width"] = "25%";
        $loadingpercent_text["x"] = "13%";
        $loadingpercent_text["y"] = "535";
    }else if ($flag == "wap"){
        $startpic_container["devices"] = "touchdevice";
        $skin_title_logo3["scale"] = "0.6";
        $loadingbar_bg["x"] = "-15";
        $loadingbar_bg["y"] = "380";
        $loadingbar_bg["width"] = "45%";
        $loadingpercent_text["x"] = "93";
        $loadingpercent_text["y"] = "375";
    }
    file_put_contents($tourXml, $tourXmlObj->asXML());
}

//vtourskin.xml 更改plugin name="WebVR"  的设置
function editVskinWebVR($vtourskinXml)
{
    //$vtourskinXml = storage_path("panos") . "\\" . $gid . "\\vtour\\skin\\vtourskin.xml";
    //$vtourskinXml = storage_path("panos") . "\\" . $gid . "\\vtour\\skin\\vtourskin_new.xml";
    $vtourskinStr = file_get_contents($vtourskinXml);
    $vtourskinObj = new \SimpleXMLElement($vtourskinStr);
    $pluginDOC = $vtourskinObj->plugin[0];
    $pluginDOC['multireslock.mobile.or.tablet'] = "true";
    $pluginDOC['mobilevr_screensize'] = "auto";
    $pluginDOC['devices'] = "all";
    $pluginDOC['mobilevr_wakelock'] = "false";
    $pluginDOC['mobilevr_fake_support'] = "true";
    file_put_contents($vtourskinXml, $vtourskinObj->asXML());
}

//u+app
function editTourShare($tourXml, $flag)
{
    //$tourXml = storage_path("panos") . "\\" . $gid . "\\vtour\\tour.xml";
    //$tourXml = storage_path("panos") . "\\" . $gid . "\\vtour\\tour_pro.xml";
    $tourXmlStr = file_get_contents($tourXml);
    $tourXmlObj = new \SimpleXMLElement($tourXmlStr);
    $left_icon = $tourXmlObj->layer[5];
    $right_share = $tourXmlObj->layer[6];
    if ($flag) {
        $left_icon["visible"] = "true";
        $left_icon["y"]= "6%";
        $left_icon["onclick"] = "jscall(Back())";
        $right_share["visible"] = "true";
        $right_share["y"] = "6%";
        $right_share["onclick"] = "jscall(Share_vr())";
    } else {
        $left_icon["visible"] = "false";
        $left_icon["y"]= "15";
        $left_icon["onclick"] = "jscall(Back())";
        $right_share["visible"] = "false";
        $right_share["y"] = "20";
        $right_share["onclick"] = "";
    }
    file_put_contents($tourXml, $tourXmlObj->asXML());
}

//更改vtourskin.xml 里的ImageUrl 和Mobile
function editVskinImageurlMobile($vtourskinXml, $userUrl, $phoneHtml, $flag)
{
    //$vtourskinXml = storage_path("panos") . "\\" . $gid . "\\vtour\\skin\\vtourskin.xml";
    //$vtourskinXml = storage_path("panos") . "\\" . $gid . "\\vtour\\skin\\vtourskin_new.xml";
    $vtourskinStr = file_get_contents($vtourskinXml);
    $vtourskinObj = new \SimpleXMLElement($vtourskinStr);
    //更改PC端经纪人头像和手机号
    $father_control_bar_pc = $vtourskinObj->layer[2]->layer[0]->layer[0]->layer[3];
    $user_icon_pc = $father_control_bar_pc->layer[2]->layer[0]->layer[0];
    $user_icon_pc["url"] = $userUrl;
    $phone_value_pc = $father_control_bar_pc->layer[2]->layer[1];
    $phone_value_pc["html"] = $phoneHtml;
    //更改移动端经纪人图形
    $father_control_bar = $vtourskinObj->layer[2]->layer[0]->layer[0]->layer[4];
    $skin_user = $father_control_bar->layer[1]->layer[0];
    $skin_user["url"] = $userUrl;
    $skin_talk = $father_control_bar->layer[2];
    $skin_phone = $father_control_bar->layer[3];
    //没有维护人时候 只显示打电话
    if ($flag == false) {
        $skin_talk["visible"] = "false";
        $skin_talk["onclick"] = "";
        //$skin_phone["onclick"] = "openurl('tel:".$phoneHtml."')";
        $skin_phone["onclick"] = "jscall(Mobile())";
        $skin_phone["x"] = "10%";
    } else {
        $skin_talk["visible"] = "true";
        $skin_talk["onclick"] = "jscall(LineConsult())";
        //$skin_phone["onclick"] = "openurl('tel:".$phoneHtml."')";
        $skin_phone["onclick"] = "jscall(Mobile())";
        $skin_phone["x"] = "48%";
    }

    file_put_contents($vtourskinXml, $vtourskinObj->asXML());
}



//wap 小程序 无需IM按钮
function checkVskinMobile($vtourskinXml,$flag)
{
    $vtourskinStr = file_get_contents($vtourskinXml);
    $vtourskinObj = new \SimpleXMLElement($vtourskinStr);
    //更改移动端经纪人图形
    $father_control_bar = $vtourskinObj->layer[2]->layer[0]->layer[0]->layer[4];
    $skin_talk = $father_control_bar->layer[2];
    $skin_phone = $father_control_bar->layer[3];
    if ($flag){
        $skin_talk["visible"] = "true";
        $skin_talk["onclick"] = "jscall(LineConsult())";
        $skin_phone["onclick"] = "jscall(Mobile())";
        $skin_phone["devices"] = "mobile";
        $skin_phone["x"] = "48%";
    }else{
        $skin_talk["visible"] = "false";
        $skin_talk["onclick"] = "jscall(LineConsult())";
        $skin_phone["onclick"] = "jscall(wapMobile())";
        $skin_phone["devices"] = "mobile";
        $skin_phone["x"] = "20%";
    }
    file_put_contents($vtourskinXml, $vtourskinObj->asXML());
}




//调整APP端VR导航的高度位置
function editTourBar($tourXml,$flag){
    //$tourXml = storage_path("panos") . "\\" . $gid . "\\vtour\\tour.xml";
    //$tourXml = storage_path("panos") . "\\" . $gid . "\\vtour\\tour_pro.xml";
    $tourXmlStr = file_get_contents($tourXml);
    $tourXmlObj = new \SimpleXMLElement($tourXmlStr);
    $top_back_layer = $tourXmlObj->layer[4];
    $top_shade_layer = $tourXmlObj->layer[7];
    $button_3 = $tourXmlObj->layer[16];

    if ($flag){
        $top_back_layer["y"] = "4%";
        $top_shade_layer["y"] = "4.3%";
        $button_3["y"] = "5.8%";
    }else{
        $top_back_layer["y"] = "2";
        $top_shade_layer["y"] = "6";
        $button_3["y"] = "16";
    }
    file_put_contents($tourXml, $tourXmlObj->asXML());

}


//判断是否是手机端
function isMobile()
{
    // 如果有HTTP_X_WAP_PROFILE则一定是移动设备
    if (isset($_SERVER['HTTP_X_WAP_PROFILE'])) {
        return true;
    }
    // 如果via信息含有wap则一定是移动设备,部分服务商会屏蔽该信息
    if (isset($_SERVER['HTTP_VIA'])) {
        // 找不到为flase,否则为true
        return stristr($_SERVER['HTTP_VIA'], "wap") ? true : false;
    }
    // 脑残法，判断手机发送的客户端标志,兼容性有待提高。其中'MicroMessenger'是电脑微信
    if (isset($_SERVER['HTTP_USER_AGENT'])) {
        $clientkeywords = array('nokia', 'sony', 'ericsson', 'mot', 'samsung', 'htc', 'sgh', 'lg', 'sharp', 'sie-', 'philips', 'panasonic', 'alcatel', 'lenovo', 'iphone', 'ipod', 'blackberry', 'meizu', 'android', 'netfront', 'symbian', 'ucweb', 'windowsce', 'palm', 'operamini', 'operamobi', 'openwave', 'nexusone', 'cldc', 'midp', 'wap', 'mobile', 'MicroMessenger');
        // 从HTTP_USER_AGENT中查找手机浏览器的关键字
        if (preg_match("/(" . implode('|', $clientkeywords) . ")/i", strtolower($_SERVER['HTTP_USER_AGENT']))) {
            return true;
        }
    }
    // 协议法，因为有可能不准确，放到最后判断
    if (isset ($_SERVER['HTTP_ACCEPT'])) {
        // 如果只支持wml并且不支持html那一定是移动设备
        // 如果支持wml和html但是wml在html之前则是移动设备
        if ((strpos($_SERVER['HTTP_ACCEPT'], 'vnd.wap.wml') !== false) && (strpos($_SERVER['HTTP_ACCEPT'], 'text/html') === false || (strpos($_SERVER['HTTP_ACCEPT'], 'vnd.wap.wml') < strpos($_SERVER['HTTP_ACCEPT'], 'text/html')))) {
            return true;
        }
    }
    return false;
}


// tour.xml  startlogoevents
//调客户端的方法解决客户端启动VR的空白页
function editTourStartlogoevents($tourXml){
    //$tourXml = storage_path("panos") . "\\" . $gid . "\\vtour\\tour.xml";
    //$tourXml = storage_path("panos") . "\\" . $gid . "\\vtour\\tour_pro.xml";
    $tourXmlStr = file_get_contents($tourXml);
    $tourXmlObj = new \SimpleXMLElement($tourXmlStr);
    $startlogoevents = $tourXmlObj->events[0];
    $startlogoevents["onloadcomplete"]="jscall(hideHUD());delayedcall(0.1,tween(layer[startpic_container].ox,-2500,1));delayedcall(0.5, loadingbar_stoploading() );";
    file_put_contents($tourXml, $tourXmlObj->asXML());
}


/**
 * 将字符串参数变为数组
 * @param $query
 * @return array
 */
function convertUrlQuery($query)
{
    $queryParts = explode('&', $query);
    $params = array();
    foreach ($queryParts as $param) {
        $item = explode('=', $param);
        $params[$item[0]] = $item[1];
    }
    return $params;
}













