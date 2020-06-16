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
    exit;
}

/**
 * @param null $path
 * 删除目录以及该目录下的所有子目录文件
 */
function clearDir($path = null)
{
    if(is_dir($path)){
        $p = scandir($path);
        foreach($p as $val){
            if($val !="." && $val !=".."){
                if(is_dir($path.$val)){         //如果是目录则递归子目录，继续操作
                    clearDir($path.$val.'/');      //子目录中操作删除文件夹和文件
                    @rmdir($path.$val.'/');     //目录清空后删除空文件夹
                }else{
                    unlink($path.$val);         //文件则直接删除
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
 * 两个同key数组值整合成一个数组 key保持
 * @param $arr1
 * @param $arr2
 * @param string $key
 */
function keepArrKey($arr1, $arr2, $key = '')
{
    $count = count($arr1);
    for ($j = 0; $j < $count;) {
        for ($i = 0; $i < $count; $i++) {
            $arr2[$j][$key] = $arr1[$i];
            $j++;
        }
    }
}


/**
 * 索引数组key替换成大写字母
 * @param $arr
 * @param $temp
 * @return array|false
 */
function numAbc($arr,$temp){
    //$temp = array("A","B","C","D","E","G","H","I");
    $diff = count($temp)-count($arr);
    if ($diff >= 0){
        $newTemp = array_slice($temp,0,count($arr));
        $newArr = array_combine($newTemp,$arr);
        return $newArr;
    }

}


function houseApi($propertyCode,$CityID,$VrUrl,$VRTitleUrl,$PlatForm){

    //$url = "http://192.168.1.79:51314/SProperty/HouseApi/PropertyExploration/VRPropertyExplorationCallback";
    $url = "http://wf.t.jjw.com:8889/Api/PropertyExploration/VRPropertyExplorationCallback";
    //请求头
    //$headers[]  =  "Content-Type:application/x-www-form-urlencoded";
    $headers[]  =  "Content-Type:application/json";
    $headers[]  =  "ERPSignSecret:E10ADC3949BA59ABBE56E057F20F883E";
    $headers[]  =  "ERPVersion: JJW";
    $headers[]  =  "PlatForm:1";
    $headers[]  =  "Client-Mac:Accept-Encoding:gzip";
    //$headers[]  =  "Cookie:ERP=AF5071EE81E387E47076D8E693C2FFB465A70730B1D453CA3C74B5A29AB68ED94B84C3566B0ED1DFBCFE5CE8435348C16B4CD77B1D88D606973A21DBA8242A1D478D3E8A91A900E8CC120D6EEA671E79C0759FA911165D5FB5B65DA0958BC2AC4C98B94A570EAE095331898B764848050BF24B5741160FE6CC1BFED3335C2A244ABC4398811F6379B74ABCC0C1B834E49F2F1743728116991626A50EA777FC0762B9920D50ACEE672B62C401101EB499C222107846A7D8EBC9467BF656059924; ERPUSERINFO=b2a7e1d225ce705d2ab4ce058e0f138cae98c2894dad7a3586399c6fa5e91b05101359132d0b165f43f5f8ca19ecd41943ecf3605ae943e839f4ee77ba284fe3e2bc03fa70633739edc7f94a68bf646b682673db642fd82ce27e7b9c035d6f5071e575af6457178074c45391f36abb86208ae8668db7b105a29f17f5049c0b443963151388710fc34885b2302bab8a595c1cff7e945d17c5761c4fb32089e1880bc54ed09e4d40696ff1a5e208920717a952d3626169d520b393a0b441511591fb384f5529db34414d6738996e5731537bfe193d475c700b940739b6667687308928fc1fefefc774c0656e6cb3040237ee84be9f130a5d183b9063320f7997cc71099d8fc6f157fafbe8ba8bcc2262b5e2ac75d1d707bf5bd680394955436f423d702197841bd538f701968d8f044458247fac7aecf5724a407a97dcefaf067535ee0b3db69f2b7ff490b6ffae9b38fdcf32a29b15062f3b92d963770fa557532a882661fbb7569b1107bf59eea338541447cd2d5707a10366b31809d2d9c28c36a2d51ff2bf563ea1440b180ae1e5c138a179012241251fdff5fb63f22feea30c199c57fbadccd5eb25fa2469a25b15";
    $headers[]  =  "Cookie:ERP-Test=BD429CD317369A5C0F1D8B8730E5491BAA198F75E061BDC0D13045F823BB98B7069D55546F5ED99ED5E725F80F6821300008AB8EC7F01D81F592665FB0B8402957A352CA400DA30F2EC6695344496AD9E9458E66F7FC018F5498A7D23FEC2E3C2FAFFE694A235AF30A2194E816B56CB50E953FEC7882EDE8B7638278C0CFE721347C477BC8743E829FB92DE30DB84714C0D2AE32E12A064E74BD4C23BD8BA879DC29C4A38E5214BB891AF26E28AB55B44036F42DEA286212091E2FF8B31E33BC";

    //初始化
    $curl = curl_init();
    //设置抓取的url
    curl_setopt($curl, CURLOPT_URL, $url);
    //设置头文件的信息作为数据流输出
    curl_setopt($curl, CURLOPT_HEADER,0);
    //设置请求头
    curl_setopt($curl, CURLOPT_HTTPHEADER,$headers);
    //设置获取的信息以文件流的形式返回，而不是直接输出。
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
    //设置post方式提交
    curl_setopt($curl, CURLOPT_POST, 1);
    //设置头文件的信息作为数据流输出
    //curl_setopt($curl, CURLOPT_HEADER, 1);
    $post_data = array(
        "CityID" => $CityID,
        'PropertyCode' => $propertyCode,
        'VrUrl'=>$VrUrl,
        'VRTitleUrl' =>$VRTitleUrl,
        'PlatForm'=>$PlatForm,
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
function editTourTitle($gid,$title){
    $tourXml = storage_path("panos") . "\\" . $gid . "\\vtour\\tour.xml";
    $tourXmlStr = file_get_contents($tourXml);
    $tourXmlObj = new \SimpleXMLElement($tourXmlStr);
    $button_title = $tourXmlObj->layer[16];
    $button_title["html"] = $title;
    file_put_contents($tourXml, $tourXmlObj->asXML());
}

//更改vtourskin.xml 里的ImageUrl 和Mobile
function editVskinImageurlMobile ($gid ,$userUrl,$phoneHtml,$flag){
    $vtourskinXml = storage_path("panos") . "\\" . $gid . "\\vtour\\skin\\vtourskin.xml";
    $vtourskinStr = file_get_contents($vtourskinXml);
    $vtourskinObj = new \SimpleXMLElement($vtourskinStr);
    //更改PC端经纪人头像和手机号
    $father_control_bar_pc = $vtourskinObj->layer[2]->layer[0]->layer[0]->layer[3];
    $user_icon_pc = $father_control_bar_pc->layer[2]->layer[0]->layer[0];
    $user_icon_pc["url"] = $userUrl;
    $phone_value_pc =  $father_control_bar_pc->layer[2]->layer[1];
    $phone_value_pc["html"] = $phoneHtml;
    //更改移动端经纪人图形
    $father_control_bar = $vtourskinObj->layer[2]->layer[0]->layer[0]->layer[4];
    $skin_user = $father_control_bar->layer[1]->layer[0];
    $skin_user["url"] = $userUrl;
    $skin_talk = $father_control_bar->layer[2];
    $skin_phone = $father_control_bar->layer[3];
    //如果维护人为空 则隐藏再线聊
    if ($flag == false){
        $skin_talk["visible"] = "false";
        $skin_talk["onclick"] = "";
        $skin_phone["onclick"] = "openurl('tel:".$phoneHtml."')";
    }else{
        $skin_talk["visible"] = "true";
        $skin_talk["onclick"] = "jscall(LineConsult())";
        $skin_phone["onclick"] = "openurl('tel:".$phoneHtml."')";
    }

    file_put_contents($vtourskinXml, $vtourskinObj->asXML());
}










