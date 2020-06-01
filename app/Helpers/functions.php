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


function houseApi($propertyCode,$vrStepID,$VrStatus,$VrUrl,$CityID,$Creator,$CreatorDC){

    //$vrStepID = "1912111727175A792253BBA34D0A8888";
    //$propertyCode = 123456;
    //$VrStatus = 3;
    //$VrUrl = "http://localhost/pano/vr/uri/1912111727175A792253BBA34D0A8A47/17122815560039EE2E6DAB1A47ABAD62";
    //$VrUrl = "http://localhost/pano//storage/panos/37508/tour.html";

    //$url = "http://192.168.13.20:51314/SProperty/HouseApi/PropertyExploration/UpdatePropertyExplorationVrStep";
    $url = "http://192.168.13.7:8099/HouseApi/PropertyExploration/UpdatePropertyExplorationVrStep";
    //请求头
    //$headers[]  =  "Content-Type:application/x-www-form-urlencoded";
    $headers[]  =  "Content-Type:application/json";
    $headers[]  =  "ERPSignSecret:E10ADC3949BA59ABBE56E057F20F883E";
    $headers[]  =  "ERPVersion:V1";
    $headers[]  =  "PlatForm:1";
    $headers[]  =  "Client-Mac:Accept-Encoding:gzip";
    $headers[]  =  "Cookie:ERP=AF5071EE81E387E47076D8E693C2FFB465A70730B1D453CA3C74B5A29AB68ED94B84C3566B0ED1DFBCFE5CE8435348C16B4CD77B1D88D606973A21DBA8242A1D478D3E8A91A900E8CC120D6EEA671E79C0759FA911165D5FB5B65DA0958BC2AC4C98B94A570EAE095331898B764848050BF24B5741160FE6CC1BFED3335C2A244ABC4398811F6379B74ABCC0C1B834E49F2F1743728116991626A50EA777FC0762B9920D50ACEE672B62C401101EB499C222107846A7D8EBC9467BF656059924; ERPUSERINFO=b2a7e1d225ce705d2ab4ce058e0f138cae98c2894dad7a3586399c6fa5e91b05101359132d0b165f43f5f8ca19ecd41943ecf3605ae943e839f4ee77ba284fe3e2bc03fa70633739edc7f94a68bf646b682673db642fd82ce27e7b9c035d6f5071e575af6457178074c45391f36abb86208ae8668db7b105a29f17f5049c0b443963151388710fc34885b2302bab8a595c1cff7e945d17c5761c4fb32089e1880bc54ed09e4d40696ff1a5e208920717a952d3626169d520b393a0b441511591fb384f5529db34414d6738996e5731537bfe193d475c700b940739b6667687308928fc1fefefc774c0656e6cb3040237ee84be9f130a5d183b9063320f7997cc71099d8fc6f157fafbe8ba8bcc2262b5e2ac75d1d707bf5bd680394955436f423d702197841bd538f701968d8f044458247fac7aecf5724a407a97dcefaf067535ee0b3db69f2b7ff490b6ffae9b38fdcf32a29b15062f3b92d963770fa557532a882661fbb7569b1107bf59eea338541447cd2d5707a10366b31809d2d9c28c36a2d51ff2bf563ea1440b180ae1e5c138a179012241251fdff5fb63f22feea30c199c57fbadccd5eb25fa2469a25b15";
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
        'PropertyCode' => $propertyCode,
        'VrStepID' => $vrStepID,
        'VrStatus' =>$VrStatus,
        'VrUrl'=>$VrUrl,
        'CityID'=>$CityID,
        'Creator'=>$Creator,
        'CreatorDC'=>$CreatorDC,
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











