<?php

namespace App\Http\Controllers\Krpano;

use App\Common\Err\ApiErrDesc;
use App\Common\Krpano\KrpanoContants;
use App\Model\Pano;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

class UploadController extends Controller
{
    //多图片上传页
    public function indexs(Request $request)
    {
        return view('krpano.uploads');
    }


    public function getPanoUri(Request $request)
    {
        $houseCode = $request->hc;
        $agentCode = $request->ac;
        $gid = $request->gid;
        $CityID = $request->cs;
        $flagType = $request->ft;
        $sourceType = $request->st;
        $houseType = $request->ht;  //2:出售  | 3:出租 (备注：区分出售vr和出租vr)
        $panoUri = $request->getUri();

        //判断tour_pro.xml 和tour.xml 配置文件存在的逻辑
        $name = "tour_pro";
        $xml = storage_path("panos") . '\\' . $gid . '\\' . 'vtour' . '\\';
        $xmlFile = $xml . $name . ".xml";
        if (file_exists($xmlFile)) {
            $vtourskinFile = $vtourskinXml = storage_path("panos") . "\\" . $gid . "\\vtour\\skin\\vtourskin_new.xml";
            $tourFile = $xml . "tour_pro" . ".xml";
        } else {
            $vtourskinFile = storage_path("panos") . "\\" . $gid . "\\vtour\\skin\\vtourskin.xml";
            $tourFile = $xml . "tour" . ".xml";
        }

        //调客户端的方法解决客户端启动VR的空白页
        //editTourStartlogoevents($tourFile);

        //vtourskin.xml 更改plugin name="WebVR"  的设置
        //editVskinWebVR($vtourskinFile);

        if ($houseType == "") {
            $houseType = 2;
        }


        if ($sourceType == 2 || $sourceType == 3) {      //  0:pc, 1:wap, 2:android, 3:ios, 4:wechart
            //editTourShare($tourFile, true);
            //editTourBar($tourFile,true);
        } else {
            //editTourShare($tourFile, false);
            //editTourBar($tourFile,false);
            $isPhone = isMobile();
            if ($isPhone) {
                $sourceType = 1;
            } else {
                $sourceType = 0;
            }
        }

        /*if ($sourceType == 0){
            editTourStart($tourFile,"pc");       //全景加载页进度条PC 和 移动端
        }else{
            editTourStart($tourFile,"wap");
        }*/

        if ($houseCode == "{0}" && $agentCode == "{1}" && $CityID == "{2}") {
            $title = "";
            $thumb = "";
            $agentID = "";
            $agentName = "";
            $agentPhone = "";
            $houseID = "";
            //更改tour.xml 里的Title为空
            //editTourTitle($tourFile, "");
            //更改vtourskin.xml 里的ImageUrl 和Mobile 为空
            //editVskinImageurlMobile($vtourskinFile, "", "", "");

            return view("krpano.new", ["gid" => $gid, "title" => $title, "agentID" => $agentID, "agentPhone" => $agentPhone, "agentName" => $agentName, "thumb" => $thumb,
                "houseID" => $houseID, "houseCode" => $houseCode, "agentCode" => $agentCode, "CityID" => $CityID]);
        }

        //不传 房源/经纪人/城市ID 的情况
        if ($houseCode == "" && $agentCode == "" && $CityID == "") {
            $title = "";
            $thumb = "";
            $agentID = "";
            $agentName = "";
            $agentPhone = "";
            $houseID = "";
            //更改tour.xml 里的Title为空
            //editTourTitle($tourFile, "");
            //更改vtourskin.xml 里的ImageUrl 和Mobile 为空
            //editVskinImageurlMobile($vtourskinFile, "", "", "");
            return view("krpano.new", ["gid" => $gid, "title" => $title, "agentID" => $agentID, "agentPhone" => $agentPhone, "agentName" => $agentName, "thumb" => $thumb,
                "houseID" => $houseID, "houseCode" => $houseCode, "agentCode" => $agentCode, "CityID" => $CityID]);
        }

        if ($flagType == "") {       //0:C端  | 1:B端
            $flagType = 0;
        }

        $houseApi = file_get_contents("http://120.76.210.152:8099/api/HouseAPI/GetShareHouseDetailByCode?HouseSysCode=" . $houseCode . "&flagType=" . $flagType . "&CityID=" . $CityID . "&houseType=" . $houseType);
        $agentApi = file_get_contents("http://120.76.210.152:8099/api/Agent/GetAgentInfoByCodeVr?id=" . $agentCode . "&sourceType=" . $sourceType . "&cityID=" . $CityID);

        $houseData = json_decode($houseApi);
        $agentData = json_decode($agentApi);

        if (!is_dir(storage_path('files'))) {
            mkdir(storage_path('files'), 0777, true);
        }

        //房源信息
        if ($houseData->Code == 2000 && $houseData->Data) {
            $title = $houseData->Data->Title;
            $title = mb_substr($title, 0, 15, 'utf-8') . "...";

            $fileName = $gid . ".txt";
            $filePath = storage_path("files\\") . $fileName;
            if (!file_exists($filePath)) {
                file_put_contents($filePath, json_encode($houseData->Data));
            }

            editTourTitle($tourFile, $title);
            //Cache::forever("houseInfo" . "_" . $gid, $houseData->Data);
            $charTitle = "全景看房 | " . $houseData->Data->BuildingName . " " . $houseData->Data->CountF . "室" . $houseData->Data->CountT . "厅";
            $houseID = $houseData->Data->ID;                    //房源自增长ID
            $houseNum = $houseData->Data->CertificationID;      //房源编号

            DB::update("update panos set houseCode='" . $houseCode . "',cityName='" . $CityID . "',panoUrl='" . $panoUri . "',title='" . $title . "',pano_id='" . $houseID . "',houseNum='" . $houseNum . "' where gid=?", [$gid]);

        } else {
            $title = "";
            Cache::forever("houseInfo" . "_" . $gid, $houseData->Data);
            editTourTitle($tourFile, $title);
            $charTitle = "";
            $houseID = "";
            $houseNum = "";

            DB::update("update panos set houseCode='" . $houseCode . "',cityName='" . $CityID . "',panoUrl='" . $panoUri . "',title='" . $title . "',pano_id='" . $houseID . "',houseNum='" . $houseNum . "' where gid=?", [$gid]);
        }


        //经纪人信息
        if ($agentData->Code == 2000 && $agentData->Data) {
            $agentId = $agentData->Data->AgentID;   //int  房源id
            // 扫码拨号 agentid 从上面接口获取
            $chatCode = "http://jjwechatapi.jjw.com/api/SmallProgram/GetHouseAgentImg?houseid=0&agentid=" . $agentId . "&phonePosition=73";
            Cache::forever("chatCode" . "_" . $gid, $chatCode);
            Cache::forever("agentInfo" . "_" . $gid, $agentData->Data);

            $storeName = $agentData->Data->StoreName;   //所属门店
            $agentName = $agentData->Data->AgentName;   //发布人
            $agentImgUrl = $agentData->Data->ImageUrl;
            if ($agentImgUrl == "") {
                $agentImgUrl = "/pano/storage/static/images/manager.png";
            }
            $agentPhone = $agentData->Data->Mobile;
            $agentID = $agentData->Data->AgentID;

            //更改vtourskin.xml 里的ImageUrl 和Mobile
            editVskinImageurlMobile($vtourskinFile, $agentImgUrl, $agentPhone, true);

            $kf = file_get_contents("http://120.76.210.152:8099/api/Home/GetCityList");
            $kfData = json_decode($kf)->Data;
            foreach ($kfData as $cityVal) {
                if ($CityID == $cityVal->CityID) {
                    $cityName = $cityVal->CityName;
                }
            }

            DB::update("update panos set agentCode='" . $agentCode . "',user_id='" . $agentID . "',storeName='" . $storeName . "',agentName='" . $agentName . "' where gid=?", [$gid]);


        } else {  //如果经纪人数据为空 则获取400电话
            //拿到房源ID 下的城市ID用来获取该客服400电话
            $storeName = "";                        //所属门店
            $agentName = "";                        //发布人
            $agentImgUrl = "";
            Cache::forever("agentInfo" . "_" . $gid, $agentData->Data);
            $kf = file_get_contents("http://120.76.210.152:8099/api/Home/GetCityList");
            $kfData = json_decode($kf)->Data;
            $CustomerService400 = "";

            foreach ($kfData as $kfVal) {
                if ($CityID == $kfVal->CityID) {
                    $CustomerService400 = $kfVal->CustomerService400;    //400电话
                    $cityName = $kfVal->CityName;
                }
            }
            $agentPhone = $CustomerService400;
            $agentID = "";
            //更改vtourskin.xml 里的ImageUrl 和Mobile
            editVskinImageurlMobile($vtourskinFile, $agentImgUrl, $agentPhone, false);

            DB::update("update panos set agentCode='" . $agentCode . "',user_id='" . $agentID . "',storeName='" . $storeName . "',agentName='" . $agentName . "' where gid=?", [$gid]);
        }

        //缩略图
        $result = DB::select('select thumb from imgs where gid=?', [$gid]);
        if ($result) {
            $thumb = $result[0]->thumb;
        } else {
            $thumb = "";
        }

        //$result = DB::select('select imgData from uploads  where gid=?', [$gid]);
        //$zh_name = json_decode($result[0]->imgData)->zh_name;
        //$res_panos = DB::select('select houseCode,agentCode from panos  where gid=?', [$gid]);
        return view("krpano.new", ["gid" => $gid, "title" => $charTitle, "agentID" => $agentID, "agentPhone" => $agentPhone, "agentName" => $agentName, "thumb" => $thumb,
            "houseID" => $houseID, "houseCode" => $houseCode, "agentCode" => $agentCode, "CityID" => $CityID]);

    }


    //VR房勘检查状态
    public function checkRule(Request $request)
    {
        $houseCode = $request->houseCode;
        $check_at = $request->check_at;
        if ($check_at == 2) {
            $affected = DB::update("update panos set check_at=  '" . $check_at . "' where houseCode=?", [$houseCode]);
            $c['code'] = 2000;
            $c['check_at'] = $check_at;
            $c['msg'] = "房堪检测合规";
        } else if ($check_at == 3) {
            $affected = DB::update("update panos set check_at=  '" . $check_at . "' where houseCode=?", [$houseCode]);
            $c['code'] = 2000;
            $c['check_at'] = $check_at;
            $c['msg'] = "房堪检测不合规";
        } else if ($check_at == 1) {
            $affected = DB::update("update panos set check_at=  '" . $check_at . "' where houseCode=?", [$houseCode]);
            $c['code'] = 2000;
            $c['check_at'] = $check_at;
            $c['msg'] = "房堪未检查";
        } else {
            $c['code'] = 2001;
            $c['check_at'] = $check_at;
            $c['msg'] = "传参错误";
        }
        return json_encode($c);
    }


    // TEST 添加VR房堪创建日志流程接口
    public function makeHouseApi()
    {
        $propertyCode = "7803";
        $vrStepId = "195F01914D114E2CA61CB7B72E853614";
        $VrStatus = 3;
        //$VrUrl = "http://localhost/pano/vr/uri/486187/1912111727175A792253BBA34D0A8A47/17122815560039EE2E6DAB1A47ABAD62/1/-1/-1";
        $VrUrl = "http://120.76.210.152:8088/pano/vr/uri/FD5BEFFA6606D91975D51F4A55EDA92B?hc={0}&ac={1}&cs={2}";
        $CityID = 51;
        $Creator = "101610";
        $CreatorDC = "50504";
        $VRTitleUrl = "http：//localhost//pano/storage/panos/123456/thumb/a2.jpg";
        $PlatForm = "2";

        houseApi($propertyCode, $CityID, $VrUrl, $VRTitleUrl, $PlatForm);
    }


    public function panos(Request $request)
    {
        if ($request->isMethod("POST")) {

            $this->http_host = config("app.url");
            $this->base_name = config("app.name");

            $houseCode = $request->get("houseCode");        //房源ID
            $agentCode = $request->get("agentCode");        //经纪人ID
            $CityID = $request->get("CityID");              //城市ID
            $PrivateKey = $request->get("PrivateKey");      //秘钥

            //临时默认值
            $houseCode = "5394C61B6BAB437E891F67E6501392F0";
            $agentCode = "200525100905B8C328A8B74144489C7D";
            //$agentCode = "2007301937177055EFEB72A14E84B88D";

            $gid = $request->get("gid");
            $gid = "EQWQWAA12";

            $houseNum = $request->get("houseNum");
            $houseName = $request->get("house_name");   //楼盘名称
            $houseUsed = $request->get("house_used");   //房源类型
            $houseType = $request->get("house_type");   //户型
            $houseArea = $request->get("house_area");   //面积
            $houseRemark = $request->get("remark");     //备注
            $fileNames = $request->file("filename");

            $panoimgPath = str_replace('\\', '/', storage_path() . '/panos/');
            if (!is_dir(storage_path('panos'))) {
                mkdir(storage_path('panos'), 0777, true);
            }


            $flagType = 0;          //  0:C端  | 1:B端
            $CityID = 51;
            $sourceType = 0;        //  0:pc, 1:wap, 2:android, 3:ios, 4:wechart


            $houseApi = file_get_contents("http://120.76.210.152:8099/api/HouseAPI/GetShareHouseDetailByCode?HouseSysCode=" . $houseCode . "&flagType=" . $flagType . "&CityID=" . $CityID . "&houseType=2");
            $agentApi = file_get_contents("http://120.76.210.152:8099/api/Agent/GetAgentInfoByCodeVr?id=" . $agentCode . "&sourceType=" . $sourceType . "&cityID=" . $CityID);
            $houseData = json_decode($houseApi);
            $agentData = json_decode($agentApi);

            if ($houseData->Code == 2000 && $houseData->Data) {
                $pano_id = $houseData->Data->ID;
                $houseNum = $houseData->Data->CertificationID;
                $title = $houseData->Data->Title;
            } else {
                $pano_id = "";
                $houseNum = "";
                $title = "";
            }
            if ($agentData->Code == 2000 && $agentData->Data) {
                $user_id = $agentData->Data->ID;
                $storeName = $agentData->Data->StoreName;   //所属门店
                $agentName = $agentData->Data->AgentName;   //发布人
            } else {
                $user_id = "";
                $storeName = "";
                $agentName = "";
            }

            //panos有记录则先删除再添加
            $resPanoData = DB::select('select houseCode from panos where gid=?', [$gid]);
            if ($resPanoData) {
                DB::delete('delete from panos where gid=?', [$gid]);
            }
            $pano = new Pano;
            $pano->gid = $gid;
            $pano->houseCode = $houseCode;
            $pano->agentCode = $agentCode;

            $pano->pano_id = $pano_id;
            $pano->title = $title;
            $pano->cityName = $CityID;
            $pano->user_id = $user_id;
            $pano->storeName = $storeName;
            $pano->agentName = $agentName;
            $pano->PrivateKey = $PrivateKey;

            $pano->houseNum = $houseNum;
            $pano->house_name = $houseName;
            $pano->house_used = $houseUsed;
            $pano->house_type = $houseType;
            $pano->house_area = $houseArea;
            $pano->remark = $houseRemark;
            $pano->created_at = date('Y-m-d H:i:s.000', time());
            $pano->save();

            //同一个上传者 查场景id 如果有 则先删除id下的数据 再批量插入
            $result = DB::select('select houseCode,agentCode from imgs where gid=?', [$gid]);
            if ($result) {
                DB::delete("delete from imgs where gid=?", [$gid]);
                $path = $panoimgPath . $gid . '/';
                clearDir($path);                    //删除目录以及子目录文件
            }

            //批量插入数据库
            $zh_name = array();
            $i = 0;
            foreach ($fileNames as $key => $val) {
                //索引数组key替换成大写字母
                $temp = array("A", "B", "C", "D", "E", "F", "G", "H", "I", "J", "K", "L", "M", "N");
                $newVal = numAbc($val, $temp);
                foreach ($newVal as $min_k => $min_v) {
                    $i++;
                    $key = trim($key, "'");
                    if (array_key_exists($key, ApiErrDesc::PANO_ARR_REPLACE)) {
                        if (count($newVal) == 1) {   //场景单图片不拼接大写字母
                            $zh_name[] = ApiErrDesc::PANO_ARR_REPLACE[$key];
                        } else {
                            $zh_name[] = ApiErrDesc::PANO_ARR_REPLACE[$key] . $min_k;
                        }
                    }
                    $imgName = $min_v->getClientOriginalName();
                    $imgSize = $min_v->getClientSize();
                    $imgCreated_at = date('Y-m-d H:i:s.000', $min_v->getaTime());
                    DB::table('imgs')->insert(array(
                        'sortId'=> $i-1,
                        'gid' => $gid,
                        'houseCode' => $houseCode,
                        'agentCode' => $houseCode,
                        'name' => $imgName,
                        'length' => $imgSize,
                        'created_at' => $imgCreated_at,
                        'updated_at' => $imgCreated_at
                    ));

                    $res = $min_v->move($panoimgPath . $gid, $imgName);

                    //上传成功后返回josn
                    if ($res) {
                        $r['code'] = ApiErrDesc::UPLOAD_SUCCESS[0];
                        $r['msg'] = ApiErrDesc::UPLOAD_SUCCESS[1];
                        $r['gid'] = $gid;

                        $r['houseCode'] = $houseCode;
                        $r['agentCode'] = $agentCode;

                        $r['house_name'] = $houseName;
                        $r['house_type'] = $houseType;
                        $r['imgRealDir'] = $res->getPath();
                        $r['zh_name'] = $zh_name;
                        $r['iamges'][] = [
                            'img_code' => ApiErrDesc::UPLOAD_IMG_SUCCESS[0],
                            'img_message' => ApiErrDesc::UPLOAD_IMG_SUCCESS[1],
                            'name' => $imgName,
                            'src' => $res->getRealPath(),
                            'ext' => $res->getExtension(),
                            'length' => $res->getSize(),
                        ];
                    } else {
                        $r['code'] = ApiErrDesc::UPLOAD_ERROR[0];
                        $r['msg'] = ApiErrDesc::UPLOAD_ERROR[1];
                        $r['iamges'][] = [];
                    }
                }
            }
        } else {
            $r['code'] = ApiErrDesc::NO_METHOD_POST[0];
            $r['msg'] = ApiErrDesc::NO_METHOD_POST[1];
            $r['iamges'][] = [];
        }

        if ($r["msg"] == "Success") {
            $result = DB::select('select imgData from uploads where gid=?', [$gid]);
            if ($result) {
                $delRes = DB::delete('delete from uploads where gid=?', [$gid]);
                if ($delRes) {
                    DB::table('uploads')->insert(array(
                        'imgData' => json_encode($r),
                        'gid' => $gid,
                        'created_at' => $imgCreated_at,
                        'updated_at' => $imgCreated_at
                    ));
                }
            } else {
                DB::table('uploads')->insert(array(
                    'imgData' => json_encode($r),
                    'gid' => $gid,
                    'created_at' => $imgCreated_at,
                    'updated_at' => $imgCreated_at
                ));
            }
        } else {
            $r['code'] = ApiErrDesc::UPLOAD_ERROR[0];
            $r['msg'] = ApiErrDesc::UPLOAD_ERROR[1];
            $r['iamges'][] = [];
        }
        return json_encode($r);
    }

    /**
     * 多全景图生成漫游
     * @return string
     */
    public function panosExec(Request $request)
    {
        $this->http_host = config("app.url");
        $this->base_name = config("app.name");

        //调用上传全景图的API
        $getFilesData = $this->panos($request);
        $getFilesData = json_decode($getFilesData);

        //接收参数
        //$gid = $request->get("gid");
        $propertyCode = $request->get("PropertyCode");  //房源CODE
        $CityID = $request->get("CityID");              //当前登录人城市ID
        $propertyCode = "578651";

        /*$result = DB::select('select imgData from uploads where gid=?', [$gid]);
        $imgData = $result[0]->imgData;
        $getFilesData = json_decode($imgData); */
        $gid = $getFilesData->gid;
        $houseCode = $getFilesData->houseCode;


        //$title = $getFilesData->title;
        //$houseCode = $getFilesData->houseCode;
        //$agentCode = $getFilesData->agentCode;
        //$agentImgUrl = $getFilesData->agentImgUrl;
        //$agentPhone = $getFilesData->agentPhone;
        //$userId = $getFilesData->user_id;
        //$panoId = $getFilesData->pano_id;

        //拿到图片的绝对地址
        foreach ($getFilesData->iamges as $getFile) {
            $imgNameArr[] = $getFile->name;
            $imgRealDir = $getFile->src;
            $imgRealArr[] = $imgRealDir;
        }
        $imgRealStr = implode(' ', $imgRealArr);
        $zh_name = $getFilesData->zh_name;


        //全景图上传不全返回错误信息
        if (count($imgNameArr) != count($zh_name)) {
            $res['code'] = ApiErrDesc::ERR_IMG_UPLOAD[0];
            $res['msg'] = ApiErrDesc::ERR_IMG_UPLOAD[1];
            $res['url'] = '';
            return json_encode($res);
        }
        $keepNameArr = array_combine($imgNameArr, $zh_name);

        //全景图存放路径
        $imgRealDir = $getFilesData->imgRealDir . '/';
        $krpano = app_path() . '/Libs/krpano/krpanotools64.exe';

        //重复上传图片生成切片后 先如果有则先删除vtour文件夹和缩略图文件夹
        if (is_dir($imgRealDir . KrpanoContants::VTOUR_NAME)) {
            clearDir($imgRealDir . KrpanoContants::VTOUR_NAME);
        }
        if (is_dir($imgRealDir . KrpanoContants::THUMB_NAME)) {
            clearDir($imgRealDir . KrpanoContants::THUMB_NAME);
        }

        //注册激活
        //$register = "\"FXsqTqaGNSZER5dSETEm+VzQEh9sWSa5DZMFsSmMxYV9GcXs8W3R8A/mWXrGNUceXvrihmh28hfRF1ivrW0HMzEychPvNiD8B/4/ZzDaUE9Rh6Ig22aKJGDbja1/kYIqmc/VKfItRE2RTSOIbIroxOtsz626NIpxWksAAifwhpNwuPXqDQpz2sRUMBzoPqZktpkItoSenN2mKd8Klfx7pOuB6CIK3e1CDXgyndqOt2mWybLZcU/wfJVAecfxk15ghiqrzaDsbqrdABDowg==\"";
        //$exec =$krpano." register ".$register;
        //exec($exec,$output);

        //缩略图
        $result = DB::select('select thumb from imgs where gid=?', [$gid]);
        if ($result) {
            $thumb = $result[0]->thumb;
        } else {
            $thumb = "";
        }


        //执行切片
        exec($krpano . ' makepano -config=' . 'templates/vtour-multires.config' . " " . $imgRealStr, $opt, $r);

        if (!$r) {
            if (!is_dir($imgRealDir . KrpanoContants::VTOUR_NAME)) {
                $res['code'] = ApiErrDesc::ERR_KRPANO_ENV[0];
                $res['msg'] = ApiErrDesc::ERR_KRPANO_ENV[1];
                $res['url'] = '';
            } else {
                //changVtourskinXml($imgRealDir . 'vtour/skin/vtourskin.xml', $panoId, $agentCode, $CityID, $agentImgUrl, $agentPhone);
                //changVtourskinXml($imgRealDir . 'vtour/skin/vtourskin.xml', $gid, $agentCode, $CityID, $agentImgUrl, $agentPhone);
                //changTourXml($imgRealDir . 'vtour/tour.xml', $zh_name, $title, $houseCode, $CityID);

                $agentImgUrl = "/pano/storage/static/images/manager.png";
                $agentPhone = "";
                $title = "";

                changVtourskinXml($imgRealDir . 'vtour/skin/vtourskin.xml', $gid, $agentImgUrl, $agentPhone);
                changTourXml($imgRealDir . 'vtour/tour.xml', $zh_name, $title, $gid);

                DB::update("update panos set status=2 WHERE houseCode=? AND gid<>?", [$houseCode, $gid]);


                foreach ($keepNameArr as $k_thumb => $v_mbName) {
                    $thumb_path = $this->http_host . '/' . $this->base_name . '/storage/panos/' . $gid . '/thumb/' . $k_thumb;
                    DB::update("update imgs set thumb='" . $thumb_path . "',mb_name='" . $v_mbName . "' where name=?", [$k_thumb]);
                }

                $res['code'] = ApiErrDesc::SUCCESS_KRPANO[0];
                $res['msg'] = ApiErrDesc::SUCCESS_KRPANO[1];
                //$res['pano_id'] = $panoId;
                $res['gid'] = $gid;
                $res['url'] = $this->http_host . $this->base_name . '/' . 'vr/uri/' . $gid . '?hc={0}&ac={1}&cs={2}';

                $updated_at = date('Y-m-d', time());
                DB::update("update panos set panoUrl='" . $res['url'] . "',updated_at='" . $updated_at . "',PropertyCode='" . $propertyCode . "' where gid=?", [$gid]);
                houseApi($propertyCode, $CityID, $res['url'], $thumb, 2);
            }
        } else {
            $res['code'] = ApiErrDesc::ERR_KRPANO_PARAMS[0];
            $res['msg'] = ApiErrDesc::ERR_KRPANO_PARAMS[1];
            $res['url'] = '';
        }
        return $res;
    }
}
