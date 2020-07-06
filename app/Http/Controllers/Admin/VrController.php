<?php

namespace App\Http\Controllers\Admin;

use App\Model\Hotspot;
use App\Model\Pano;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;

class VrController extends Controller
{
    //vr 列表页
    public function index(Request $request)
    {
        $kf = file_get_contents("http://120.76.210.152:8099/api/Home/GetCityList");
        $kfData = json_decode($kf)->Data;
        foreach ($kfData as $kfVal) {
            $CityName[] = $kfVal->CityName;
        }

        $perPage = 5;
        $cityName = $request->cityName;
        $status = $request->status;
        if ($status == "已上线"){
            $status_at = 1;
        }else{
            $status_at = 2;
        }
        $keywords = $request->keywords;
        $createtTime = $request->createtTime;
        $where = new Pano();
        if ($cityName){
            if ($cityName == "全部"){
                $where = $where;
            }else{
                $where = $where->where("cityName","=",$cityName);
            }
        }
        if ($status){
            if ($status == "全部"){
                $where = $where;
            }else{
                $where = $where->where("status","=",$status_at);
            }
        }
        $panos = $where->paginate($perPage);

        $panos->cityName = $cityName;
        $panos->status = $status;
        //$panos = Pano::where('cityName','=','武汉')->paginate($perPage);
        $count = $panos->total();
        return view('admin.vr.list', ['panos' => $panos, 'count' => $count, 'perPage' => $perPage, 'cityName' => $CityName]);
    }

    //编辑页的预览视图
    public function preview(Request $request)
    {
        $panoId = $request->pano_id;
        return view("admin.vr.preview", ["panoId" => $panoId]);
    }

    //列表页的预览视图
    public function lookto(Request $request)
    {
        $panoId = $request->pano_id;
        return view("admin.vr.lookto", ["panoId" => $panoId]);
    }

    //发布页的VR视图
    public function online(Request $request)
    {
        $panoId = $request->pano_id;
        return view("admin.vr.online", ["panoId" => $panoId]);
    }

    //列表页上下线操作
    public function turnup(Request $request)
    {
        $panoId = $request->panoId;
        $panoData = DB::select('select status from panos where gid=?', [$panoId]);
        if ($panoData[0]->status == "2") {   //下线
            $affected = DB::update("update panos set status= '1' where gid=?", [$panoId]);
            file_get_contents("http://120.76.210.152:8034/api/PanoRamaAPI/UpdateLine?SysCode=C025FAD876904306AAE5216982E2E8EC&state=1&time=" . time());
        } elseif ($panoData[0]->status == "1") {   //上线
            $affected = DB::update("update panos set status= '2' where gid=?", [$panoId]);
            file_get_contents("http://120.76.210.152:8034/api/PanoRamaAPI/UpdateLine?SysCode=C025FAD876904306AAE5216982E2E8EC&state=0&time=" . time());
        }
        if ($affected) {
            $panoData = DB::select('select * from panos where gid=?', [$panoId]);
            return $panoData;
        }
    }

    //列表页预览
    public function listPreview(Request $request)
    {
        $panoId = $request->panoId;
        $xmlFile = storage_path("panos") . "\\" . $panoId . "\\vtour\\tour.xml";
        $xmlPreFile = storage_path("panos") . "\\" . $panoId . "\\vtour\\tour_pre.xml";
        $skinXmlFile = storage_path("panos") . "\\" . $panoId . "\\vtour\\skin\\vtourskin.xml";
        $skinXmlNewFile = storage_path("panos") . "\\" . $panoId . "\\vtour\\skin\\vtourskin_new.xml";

        //1. copy vtourskin.xml 文件为vtourskin_new.xml
        if (!file_exists($skinXmlNewFile)) {
            $vtourSkinXmlStr = file_get_contents($skinXmlFile);
            file_put_contents($skinXmlNewFile, $vtourSkinXmlStr);
        }

        //2. copy tour.xml 文件为tour_pre.xml
        $vtourXmlStr = file_get_contents($xmlFile);
        $res = file_put_contents($xmlPreFile, $vtourXmlStr);

        //3. tour_pre.xml 的include 嵌入skin/vtourskin_new.xml 文件
        if ($res) {
            $tourXmlStr = file_get_contents($xmlPreFile);
            $tourXmlObj = new \SimpleXMLElement($tourXmlStr);
            $vtourIncludeArr = $tourXmlObj->xpath("include");
            if ($vtourIncludeArr[0]["url"] == "skin/vtourskin.xml") {    // <include url="skin/vtourskin.xml"/>
                $vtourIncludeArr[0]["url"] = "skin/vtourskin_new.xml";
            }
            file_put_contents($xmlPreFile, $tourXmlObj->asXML());
        }
        $panoData = DB::select('select status from panos where gid=?', [$panoId]);
        if ($panoData[0]->status == "1") {   //已上线
            return "online";
        } else {
            return "outline";
        }
    }


    // 发布操作
    public function produce(Request $request)
    {
        $panoId = $request->panoId;
        $xmlNewFile = storage_path("panos") . "\\" . $panoId . "\\vtour\\tour_new.xml";
        $xmlProFile = storage_path("panos") . "\\" . $panoId . "\\vtour\\tour_pro.xml";
        //1. 发布 复制一份 tour_new.xml为tour_pro.xml
        $vtourXmlStr = file_get_contents($xmlNewFile);
        $res = file_put_contents($xmlProFile, $vtourXmlStr);
        //2. 更改VR 状态为已上线
        DB::update("update panos set status= '1' where pano_id=?", [$panoId]);

        return '200';
    }

    //预览后copy xml
    public function copyUrl(Request $request)
    {
        $panoId = $request->panoId;
        $xmlFile = storage_path("panos") . "\\" . $panoId . "\\vtour\\tour.xml";
        $xmlEditFile = storage_path("panos") . "\\" . $panoId . "\\vtour\\tour_edit.xml";
        $xmlNewFile = storage_path("panos") . "\\" . $panoId . "\\vtour\\tour_new.xml";
        $skinXmlFile = storage_path("panos") . "\\" . $panoId . "\\vtour\\skin\\vtourskin.xml";
        $skinXmlNewFile = storage_path("panos") . "\\" . $panoId . "\\vtour\\skin\\vtourskin_new.xml";

        //1. copy tour_edit.xml 文件为tour_new.xml
        //$vtourXmlStr = file_get_contents($xmlFile);
        $vtourXmlStr = file_get_contents($xmlEditFile);
        $res = file_put_contents($xmlNewFile, $vtourXmlStr);
        //2. copy vtourskin.xml 文件为vtourskin_new.xml
        $vtourSkinXmlStr = file_get_contents($skinXmlFile);
        $ress = file_put_contents($skinXmlNewFile, $vtourSkinXmlStr);

        //3. 修改vtourskin_new.xml配置显示经纪人等设置信息
        $tourSkinXmlStr = file_get_contents($skinXmlNewFile);
        $tourSkinXmlObj = new \SimpleXMLElement($tourSkinXmlStr);
        $vtourSkinLayerArr = $tourSkinXmlObj->xpath("layer");
        $skin_thumbs = $vtourSkinLayerArr[2]->xpath("layer")[0]->xpath("layer")[0]->xpath("layer")[2]
            ->xpath("layer")[0]->xpath("layer")[3];
        if ($skin_thumbs["state"] == "opened") {             // skin_thumbs
            $skin_thumbs["state"] = "closed";
        }
        $father_control_bar_pc = $vtourSkinLayerArr[2]->xpath("layer")[0]->xpath("layer")[0]->xpath("layer")[3];
        if ($father_control_bar_pc["visible"] == "false") {  //father_control_bar_pc
            $father_control_bar_pc["visible"] = "true";
        }
        file_put_contents($skinXmlNewFile, $tourSkinXmlObj->asXML());

        //4. 修改tour_new.xml配置显示经纪人等设置信息
        $tourXmlStr = file_get_contents($xmlNewFile);
        $tourXmlObj = new \SimpleXMLElement($tourXmlStr);
        $vtourIncludeArr = $tourXmlObj->xpath("include");
        if ($vtourIncludeArr[0]["url"] == "skin/vtourskin.xml") {    // <include url="skin/vtourskin.xml"/>
            $vtourIncludeArr[0]["url"] = "skin/vtourskin_new.xml";
        }
        $vtourLayerArr = $tourXmlObj->xpath("layer");
        if ($vtourLayerArr[9]["visible"] == "false") {       //显示 top_screen_pc
            $vtourLayerArr[9]["visible"] = "true";
        }
        if ($vtourLayerArr[10]["visible"] == "false") {      //显示 right_vr_pc
            $vtourLayerArr[10]["visible"] = "true";
        }
        if ($vtourLayerArr[12]["visible"] == "false") {      //显示 right_set_pc
            $vtourLayerArr[12]["visible"] = "true";
        }
//        if ($tourXmlObj->skin_settings['thumbs_opened'] == "true") {   //关闭 skin_settings['thumbs_opened']
//            $tourXmlObj->skin_settings['thumbs_opened'] = "false";
//        }
        if ($tourXmlObj->autorotate['enabled'] == "false") {  //打开 自动旋转
            $tourXmlObj->autorotate['enabled'] = "true";
        }
        if ($tourXmlObj->skin_settings['littleplanetintro'] == "false") {  //打开 小行星
            $tourXmlObj->skin_settings['littleplanetintro'] = "true";
        }
        file_put_contents($xmlNewFile, $tourXmlObj->asXML());

        if ($res) {
            return '200';
        }
    }


    //热点编辑页
    public function update(Request $request)
    {
        $pano_id = $request->pano_id;
        $xmlFile = storage_path("panos") . "\\" . $pano_id . "\\vtour\\tour.xml";
        $xmlEditFile = storage_path("panos") . "\\" . $pano_id . "\\vtour\\tour_edit.xml";
        $skinXmlFile = storage_path("panos") . "\\" . $pano_id . "\\vtour\\skin\\vtourskin.xml";
        //1. copy tour.xml 文件为tour_edit.xml
        $vtourXmlStr = file_get_contents($xmlFile);
        $res = file_put_contents($xmlEditFile, $vtourXmlStr);

        //1. 拿到当前场景scene id下的缩略图和title
        $vtourDocXml = new \DOMDocument();
        //$vtourDocXml->load($xmlFile);
        $vtourDocXml->load($xmlEditFile);
        $actionStr = $vtourDocXml->getElementsByTagName("action")->item(0)->nodeValue;
        /* if(startscene === null OR !scene[get(startscene)], copy(startscene,scene[0].name); );loadscene(get(startscene), null, MERGE);
            if(startactions !== null, startactions() );
        */
        $start = strpos($actionStr, 'name');
        $start = $start - 3;
        $targetId = substr($actionStr, $start, 1);   //获取到0
        // a. 根据拿到的场景index 获取场景的title 和缩略图
        //$vtourXmlStr = file_get_contents($xmlFile);
        $vtourXmlStr = file_get_contents($xmlEditFile);
        $vtourXmlObj = new \SimpleXMLElement($vtourXmlStr);
        $vtourSceneArr = $vtourXmlObj->xpath('scene');
        $goalScene = $vtourSceneArr[$targetId];
        $target[] = $goalScene['title'];
        $target[] = $goalScene['thumburl'];
        // b. SimpleXMLElement对象 转为数组取值
        $target = json_encode($target);
        $target = json_decode($target, true);
        $sceneTitle = $target[0][0];
        $thumburl = $target[1][0];
        $thumbArr = explode('/', $thumburl);
        $thumbName = $thumbArr[2];

        //2. 修改vtourskin.xml 隐藏 经纪人信息
        $tourSkinXmlStr = file_get_contents($skinXmlFile);
        $tourSkinXmlObj = new \SimpleXMLElement($tourSkinXmlStr);
        $vtourSkinLayerArr = $tourSkinXmlObj->xpath("layer");
        $skin_thumbs = $vtourSkinLayerArr[2]->xpath("layer")[0]->xpath("layer")[0]->xpath("layer")[2]
            ->xpath("layer")[0]->xpath("layer")[3];
        if ($skin_thumbs["state"] == "closed") {             // skin_thumbs
            $skin_thumbs["state"] = "opened";
        }
        $father_control_bar_pc = $vtourSkinLayerArr[2]->xpath("layer")[0]->xpath("layer")[0]->xpath("layer")[3];
        if ($father_control_bar_pc["visible"] == "true") {  //father_control_bar_pc
            $father_control_bar_pc["visible"] = "false";
        }
        file_put_contents($skinXmlFile, $tourSkinXmlObj->asXML());

        //3. 修改tour.xml配置显示经纪人等设置信息
        //$tourXmlStr = file_get_contents($xmlFile);
        $tourXmlStr = file_get_contents($xmlEditFile);
        $tourXmlObj = new \SimpleXMLElement($tourXmlStr);
        if ($tourXmlObj->skin_settings['thumbs_opened'] == "false") {   // 开启 skin_settings['thumbs_opened']
            $tourXmlObj->skin_settings['thumbs_opened'] = "true";
        }
        $vtourLayerArr = $tourXmlObj->xpath("layer");
        if ($vtourLayerArr[9]["visible"] == "true") {       // 隐藏 top_screen_pc
            $vtourLayerArr[9]["visible"] = "false";
        }
        if ($vtourLayerArr[10]["visible"] == "true") {      // 隐藏 right_vr_pc
            $vtourLayerArr[10]["visible"] = "false";
        }
        if ($vtourLayerArr[12]["visible"] == "true") {      // 隐藏 right_set_pc
            $vtourLayerArr[12]["visible"] = "false";
        }
        if ($tourXmlObj->autorotate['enabled'] == "true") {  //关闭 自动旋转
            $tourXmlObj->autorotate['enabled'] = "false";
        }
        if ($tourXmlObj->skin_settings['littleplanetintro'] == "true") {  //关闭 小行星
            $tourXmlObj->skin_settings['littleplanetintro'] = "false";
        }
        //file_put_contents($xmlFile, $tourXmlObj->asXML());
        file_put_contents($xmlEditFile, $tourXmlObj->asXML());

        //4. 返回视图热点数据

        DB::delete("delete from hotspots where pano_id=?", [$pano_id]);

        $panoData = DB::select('select pano_id,sceneName,hotsName,type,linkedscene from hotspots where pano_id=? and visible=?', [$pano_id, "true"]);
        $panoSum = DB::select('select count(1) as sum from hotspots where pano_id=? and visible=?', [$pano_id, "true"]);
        return view("admin.vr.detail", ["panoId" => $pano_id, "panoData" => $panoData, "thumbName" => $thumbName, "sceneTitle" => $sceneTitle, "count" => $panoSum[0]->sum]);
    }

    //设置为封面
    public function setcover(Request $request)
    {
        $sceneIndex = $request->get("sceneIndex");
        $panoId = $request->get("panoId");
        $sceneTitle = $request->get("sceneTitle");

        $hlookat = $request->get("hlookat");
        $vlookat = $request->get("vlookat");

        if ($sceneIndex != null) {
            //操作xml
            $xmlFile = storage_path("panos") . "\\" . $panoId . "\\vtour\\tour.xml";
            $xmlEditFile = storage_path("panos") . "\\" . $panoId . "\\vtour\\tour_edit.xml";
            $vtourDocXml = new \DOMDocument();
            //$vtourDocXml->load($xmlFile);
            $vtourDocXml->load($xmlEditFile);
            $actionDom = $vtourDocXml->getElementsByTagName("action");
            $nodeVal = "if(startscene === null OR !scene[get(startscene)], copy(startscene,scene[" . $sceneIndex . "].name); );loadscene(get(startscene), null, MERGE);if(startactions !== null, startactions() );";
            $actionDom->item(0)->nodeValue = $nodeVal;
            //$vtourDocXml->save($xmlFile);
            $vtourDocXml->save($xmlEditFile);
            //设置场景视角
            //$vtourXmlStr = file_get_contents($xmlFile);
            $vtourXmlStr = file_get_contents($xmlEditFile);
            $vtourXmlObj = new \SimpleXMLElement($vtourXmlStr);
            $vtourSceneArr = $vtourXmlObj->xpath('scene');
            $views = $vtourSceneArr[$sceneIndex]->xpath("view");
            $views[0]['hlookat'] = $hlookat;
            $views[0]['vlookat'] = $vlookat;
            //file_put_contents($xmlFile, $vtourXmlObj->asXML());
            file_put_contents($xmlEditFile, $vtourXmlObj->asXML());

            $ress = ['h' => $hlookat, 'v' => $vlookat, 'title' => $sceneTitle];

            return $ress;
        }
    }

    //热点隐藏显示切换
    public function toggleHot(Request $request)
    {
        $sceneIndex = $request->get("sceneIndex");
        $panoId = $request->get("panoId");

        $hlookat = $request->get("hlookat");
        $vlookat = $request->get("vlookat");


        $xmlFile = storage_path("panos") . "\\" . $panoId . "\\vtour\\tour.xml";
        $xmlEditFile = storage_path("panos") . "\\" . $panoId . "\\vtour\\tour_edit.xml";
        //$vtourXmlStr = file_get_contents($xmlFile);
        $vtourXmlStr = file_get_contents($xmlEditFile);
        $vtourXmlObj = new \SimpleXMLElement($vtourXmlStr);
        $vtourSceneArr = $vtourXmlObj->xpath('scene');
        $hotspots = $vtourSceneArr[$sceneIndex]->xpath("hotspot");

        foreach ($hotspots as $hsVal) {
            if ($hsVal['visible'] == "true") {
                $hsVal['visible'] = "false";
                $status = "隐藏";
            } else {
                $hsVal['visible'] = "true";
                $status = "显示";
            }

        }

        //设置场景视角
        $views = $vtourSceneArr[$sceneIndex]->xpath("view");
        $views[0]['hlookat'] = $hlookat;
        $views[0]['vlookat'] = $vlookat;
        //file_put_contents($xmlFile, $vtourXmlObj->asXML());
        file_put_contents($xmlEditFile, $vtourXmlObj->asXML());
        $ress = ['h' => $hlookat, 'v' => $vlookat, 'status' => $status];
        return $ress;

    }

    //ajax 添加热点坐标
    public function saveHotspot(Request $request)
    {
        $panoId = $request->get("panoId");
        $ath = $request->get("h");
        $atv = $request->get("v");
        $hostName = $request->get("hostName");
        $sceneName = $request->get("sceneName");
        $sceneEname = $request->get("sceneEname");
        $sceneIndex = (int)$request->get("sceneIndex");
        $linkedscene = $request->get("linkedscene");
        $linkedTitle = $request->get("linkedTitle");

        // 同pano_id 下的同热点名 则update 覆盖
        $resHotSpot = DB::select('select pano_id,hotsName from hotspots where pano_id=? and hotsName=?', [$panoId, $hostName]);
        if ($resHotSpot) {
            $pano_id = $resHotSpot[0]->pano_id;
            $hotsName = $resHotSpot[0]->hotsName;
            $updated_at = date('Y-m-d H:i:s', time());
            $affected = DB::update("update hotspots set linkedscene='" . $linkedTitle . "' ath='" . $ath . "',atv='" . $atv . "',updated_at='" . $updated_at . "' where pano_id=? and hotsName=?", [$pano_id, $hotsName]);
            if ($affected) {
                var_dump("update success!");
            }
        } else {
            $hotspot = new Hotspot;
            $hotspot->pano_id = $panoId;
            $hotspot->sceneName = $sceneName;
            $hotspot->hotsName = $hostName;
            $hotspot->ath = $ath;
            $hotspot->atv = $atv;
            $hotspot->linkedscene = $linkedTitle;
            $hotspot->created_at = date('Y-m-d H:i:s', time());
            $hotspot->save();
        }

        //没有则xml添加热点
        $xmlFile = storage_path("panos") . "\\" . $panoId . "\\vtour\\tour.xml";
        $xmlEditFile = storage_path("panos") . "\\" . $panoId . "\\vtour\\tour_edit.xml";
        //$vtourXmlStr = file_get_contents($xmlFile);
        $vtourXmlStr = file_get_contents($xmlEditFile);
        $vtourXmlObj = new \SimpleXMLElement($vtourXmlStr);
        $currScene = $vtourXmlObj->scene[$sceneIndex];
        $currHotSpot = $currScene->addChild('hotspot');
        $currHotSpot->addAttribute("name", $hostName);
        $currHotSpot->addAttribute("style", "hotspot_style_animated");
        $currHotSpot->addAttribute("tooltip", $linkedTitle);
        $currHotSpot->addAttribute("ath", $ath);
        $currHotSpot->addAttribute("atv", $atv);
        $currHotSpot->addAttribute("zoom", "true");
        $currHotSpot->addAttribute("linkedscene", $linkedscene);
        $currHotSpot->addAttribute("visible", "true");
        //file_put_contents($xmlFile, $vtourXmlObj->asXML());
        file_put_contents($xmlEditFile, $vtourXmlObj->asXML());

        //无刷新动态更新热点管理列表
        $panoData = DB::select('select pano_id,sceneName,hotsName,type,linkedscene from hotspots where pano_id=? and visible=?', [$panoId, "true"]);
        $panoSum = DB::select('select count(1) as sum from hotspots where pano_id=? and visible=?', [$panoId, "true"]);
        //return view("admin.vr.detail", ["panoId" => $pano_id,"panoData"=>$panoData,"count"=>$panoSum[0]->sum]);

        $ress['panoData'] = $panoData;
        $ress['count'] = $panoSum[0]->sum;
        //$ress['xmlPath'] = $xmlFile;
        $ress['xmlPath'] = $xmlEditFile;
        $ress['sceneEname'] = $sceneEname;
        return $ress;
    }

    //ajax 添加文本标签热点
    public function savePoint(Request $request)
    {
        $hostName = $request->get("hostName");
        $ath = $request->get("h");
        $atv = $request->get("v");
        $sceneName = $request->get("sceneName");
        $sceneEname = $request->get("sceneEname");
        $panoId = $request->get("panoId");
        $sceneIndex = $request->get("sceneIndex");
        $linkedscene = $request->get("linkedscene");

        //新的热点数据插入库
        $hotspot = new Hotspot;
        $hotspot->pano_id = $panoId;
        $hotspot->sceneName = $sceneName;
        $hotspot->hotsName = $hostName;
        $hotspot->ath = $ath;
        $hotspot->atv = $atv;
        $hotspot->type = "point";
        $hotspot->linkedscene = $linkedscene;
        $hotspot->created_at = date('Y-m-d H:i:s', time());
        $res = $hotspot->save();
        if ($res) {
            //xml 追加新的热点标签
            $xmlFile = storage_path("panos") . "\\" . $panoId . "\\vtour\\tour.xml";
            $xmlEditFile = storage_path("panos") . "\\" . $panoId . "\\vtour\\tour_edit.xml";
            //$vtourXmlStr = file_get_contents($xmlFile);
            $vtourXmlStr = file_get_contents($xmlEditFile);
            $vtourXmlObj = new \SimpleXMLElement($vtourXmlStr);
            $sceneIndex = intval($sceneIndex);
            $currScene = $vtourXmlObj->scene[$sceneIndex];
            $currHotSpot = $currScene->addChild('hotspot');
            $currHotSpot->addAttribute("name", $hostName);
            $currHotSpot->addAttribute("style", "point_style_animated");
            $currHotSpot->addAttribute("tooltip", $linkedscene);
            $currHotSpot->addAttribute("ath", $ath);
            $currHotSpot->addAttribute("atv", $atv);
            $currHotSpot->addAttribute("zoom", "true");
            $currHotSpot->addAttribute("visible", "true");
            //file_put_contents($xmlFile, $vtourXmlObj->asXML());
            file_put_contents($xmlEditFile, $vtourXmlObj->asXML());

            //无刷新动态更新热点管理列表
            $panoData = DB::select('select pano_id,sceneName,hotsName,type,linkedscene from hotspots where pano_id=? and visible=?', [$panoId, "true"]);
            $panoSum = DB::select('select count(1) as sum from hotspots where pano_id=? and visible=?', [$panoId, "true"]);

            $ress['panoData'] = $panoData;
            $ress['count'] = $panoSum[0]->sum;
            //$ress['xmlPath'] = $xmlFile;
            $ress['xmlPath'] = $xmlEditFile;
            $ress['sceneEname'] = $sceneEname;
            return $ress;
        }
    }

    //ajax 编辑后的添加热点坐标
    public function editSaveHotspot(Request $request)
    {
        $hsNewName = $request->get("hsNewName");
        $hsOldName = $request->get("hsOldName");
        $ath = $request->get("h");
        $atv = $request->get("v");
        $sceneTitle = $request->get("sceneTitle");
        $sceneName = $request->get("sceneName");
        $sceneCurrName = $request->get("sceneCurrName");
        $panoId = $request->get("panoId");
        $sceneIndex = $request->get("sceneIndex");
        $linkedTitle = $request->get("linkedTitle");

        //echo $hsNewName,"|",$hsOldName,"|",$ath,"|",$atv,"|",$sceneTitle,"|",$sceneName,"|",$panoId,"|",$sceneIndex,"|",$linkedTitle;

        //删除旧的数据
        $res = DB::delete("delete from hotspots where hotsName=?", [$hsOldName]);

        if ($res) {      //删除成功后插入新的热点数据
            $hotspot = new Hotspot;
            $hotspot->pano_id = $panoId;
            $hotspot->sceneName = $sceneTitle;
            $hotspot->hotsName = $hsNewName;
            $hotspot->ath = $ath;
            $hotspot->atv = $atv;
            $hotspot->type = "hotspot";
            $hotspot->linkedscene = $linkedTitle;
            $hotspot->created_at = date('Y-m-d H:i:s', time());
            $result = $hotspot->save();

            if ($result) {     // 向xml中去旧添新
                $xmlFile = storage_path("panos") . "\\" . $panoId . "\\vtour\\tour.xml";
                $xmlEditFile = storage_path("panos") . "\\" . $panoId . "\\vtour\\tour_edit.xml";
                //$vtourXmlStr = file_get_contents($xmlFile);
                $vtourXmlStr = file_get_contents($xmlEditFile);
                $vtourXmlObj = new \SimpleXMLElement($vtourXmlStr);

                //删除旧的热点标签
                $vtourSceneArr = $vtourXmlObj->xpath('scene');
                $hotspots = $vtourSceneArr[$sceneIndex]->xpath("hotspot");
                foreach ($hotspots as $hsVal) {    //
                    if ($hsVal["name"] == $hsOldName) {
                        $hsVal['name'] = "";
                        $hsVal['style'] = "";
                        $hsVal['tooltip'] = "";
                        $hsVal['ath'] = "";
                        $hsVal['atv'] = "";
                        $hsVal['zoom'] = "";
                        $hsVal['linkedscene'] = "";
                        $hsVal['visible'] = "";
                    }
                }
                // 追加新的热点标签
                $sceneIndex = intval($sceneIndex);
                $currScene = $vtourXmlObj->scene[$sceneIndex];
                $currHotSpot = $currScene->addChild('hotspot');
                $currHotSpot->addAttribute("name", $hsNewName);
                $currHotSpot->addAttribute("style", "hotspot_style_animated");
                $currHotSpot->addAttribute("tooltip", $linkedTitle);
                $currHotSpot->addAttribute("ath", $ath);
                $currHotSpot->addAttribute("atv", $atv);
                $currHotSpot->addAttribute("zoom", "true");
                $currHotSpot->addAttribute("linkedscene", $sceneName);
                $currHotSpot->addAttribute("visible", "true");
                //file_put_contents($xmlFile, $vtourXmlObj->asXML());
                file_put_contents($xmlEditFile, $vtourXmlObj->asXML());
            }

        }

        //无刷新动态更新热点管理列表
        $panoData = DB::select('select pano_id,sceneName,hotsName,type,linkedscene from hotspots where pano_id=? and visible=?', [$panoId, "true"]);
        $panoSum = DB::select('select count(1) as sum from hotspots where pano_id=? and visible=?', [$panoId, "true"]);
        //return view("admin.vr.detail", ["panoId" => $pano_id,"panoData"=>$panoData,"count"=>$panoSum[0]->sum]);

        $ress['panoData'] = $panoData;
        $ress['count'] = $panoSum[0]->sum;
        //$ress['xmlPath'] = $xmlFile;
        $ress['xmlPath'] = $xmlEditFile;
        $ress['sceneEname'] = $sceneCurrName;
        return $ress;

    }

    //ajax 编辑后的添加文本标签热点
    public function editSavePoint(Request $request)
    {
        $hsNewName = $request->get("hsNewName");
        $hsOldName = $request->get("hsOldName");
        $ath = $request->get("h");
        $atv = $request->get("v");
        $sceneTitle = $request->get("sceneTitle");
        $sceneName = $request->get("sceneName");
        $panoId = $request->get("panoId");
        $sceneIndex = $request->get("sceneIndex");
        $linkedTitle = $request->get("linkedTitle");
        //echo $hsNewName,"|",$hsOldName,"|",$ath,"|",$atv,"|",$sceneTitle,"|",$sceneName,"|",$panoId,"|",$sceneIndex,"|",$linkedTitle;
        //删除旧的数据
        $res = DB::delete("delete from hotspots where hotsName=?", [$hsOldName]);
        //删除成功后插入新的热点数据
        if ($res) {
            $hotspot = new Hotspot;
            $hotspot->pano_id = $panoId;
            $hotspot->sceneName = $sceneTitle;
            $hotspot->hotsName = $hsNewName;
            $hotspot->ath = $ath;
            $hotspot->atv = $atv;
            $hotspot->type = "point";
            $hotspot->linkedscene = $linkedTitle;
            $hotspot->created_at = date('Y-m-d H:i:s', time());
            $result = $hotspot->save();
            //插库成功后 向xml中去旧添新
            if ($result) {
                $xmlFile = storage_path("panos") . "\\" . $panoId . "\\vtour\\tour.xml";
                $xmlEditFile = storage_path("panos") . "\\" . $panoId . "\\vtour\\tour_edit.xml";
                //$vtourXmlStr = file_get_contents($xmlFile);
                $vtourXmlStr = file_get_contents($xmlEditFile);
                $vtourXmlObj = new \SimpleXMLElement($vtourXmlStr);

                //删除旧的热点标签
                $vtourSceneArr = $vtourXmlObj->xpath('scene');
                $hotspots = $vtourSceneArr[$sceneIndex]->xpath("hotspot");
                foreach ($hotspots as $hsVal) {    //
                    if ($hsVal["name"] == $hsOldName) {
                        $hsVal['name'] = "";
                        $hsVal['style'] = "";
                        $hsVal['tooltip'] = "";
                        $hsVal['ath'] = "";
                        $hsVal['atv'] = "";
                        $hsVal['zoom'] = "";
                        $hsVal['linkedscene'] = "";
                        $hsVal['visible'] = "";
                    }
                }

                //新增新的热点标签
                $sceneIndex = intval($sceneIndex);
                $currScene = $vtourXmlObj->scene[$sceneIndex];
                $currHotSpot = $currScene->addChild('hotspot');
                $currHotSpot->addAttribute("name", $hsNewName);
                $currHotSpot->addAttribute("style", "point_style_animated");
                $currHotSpot->addAttribute("tooltip", $linkedTitle);
                $currHotSpot->addAttribute("ath", $ath);
                $currHotSpot->addAttribute("atv", $atv);
                $currHotSpot->addAttribute("zoom", "true");
                $currHotSpot->addAttribute("visible", "true");
                //file_put_contents($xmlFile, $vtourXmlObj->asXML());
                file_put_contents($xmlEditFile, $vtourXmlObj->asXML());
            }
        }
        //无刷新动态更新热点管理列表
        $panoData = DB::select('select pano_id,sceneName,hotsName,type,linkedscene from hotspots where pano_id=? and visible=?', [$panoId, "true"]);
        $panoSum = DB::select('select count(1) as sum from hotspots where pano_id=? and visible=?', [$panoId, "true"]);

        $ress['panoData'] = $panoData;
        $ress['count'] = $panoSum[0]->sum;
        //$ress['xmlPath'] = $xmlFile;
        $ress['xmlPath'] = $xmlEditFile;
        $ress['sceneEname'] = $sceneName;
        return $ress;
    }

    //点击编辑按钮操作
    public function editHotspot(Request $request)
    {
        $sceneIndex = $request->get("sceneIndex");
        $panoId = $request->get("panoId");
        $hostName = $request->get("hostName");
        $sceneName = $request->get("sceneName");

        //sql返回该热点的坐标值
        $panoData = DB::select('select pano_id,hotsName,ath,atv,type,linkedscene from hotspots where pano_id=? and hotsName=?', [$panoId, $hostName]);
        $res["panoId"] = $panoData[0]->pano_id;
        $res["hotsName"] = $panoData[0]->hotsName;
        $res["type"] = $panoData[0]->type;
        $res["ath"] = $panoData[0]->ath;
        $res["atv"] = $panoData[0]->atv;
        $res["linkedscene"] = $panoData[0]->linkedscene;
        $res["sceneName"] = $sceneName;
        return $res;
    }

    //点击删除的弹窗操作
    public function delHotspot(Request $request)
    {
        $panoId = $request->get("panoId");
        $sceneIndex = $request->get("sceneIndex");
        $hostName = $request->get("hostName");
        $sceneName = $request->get("sceneName");
        //sql返回该热点的坐标值
        $panoData = DB::select('select pano_id,hotsName,linkedscene from hotspots where pano_id=? and hotsName=?', [$panoId, $hostName]);
        $res["panoId"] = $panoData[0]->pano_id;
        $res["hotsName"] = $panoData[0]->hotsName;
        $res["linkedscene"] = $panoData[0]->linkedscene;
        $res["sceneName"] = $sceneName;
        return $res;
    }

    //删除弹窗的确认操作
    public function delHs(Request $request)
    {
        $panoId = $request->get("panoId");
        $sceneIndex = $request->get("sceneIndex");
        $hostName = $request->get("hostName");
        $sceneName = $request->get("sceneName");
        $res = DB::delete("delete from hotspots where hotsName=?", [$hostName]);
        //删除成功后 向xml 删除xml里的热点标签
        if ($res) {
            $xmlFile = storage_path("panos") . "\\" . $panoId . "\\vtour\\tour.xml";
            $xmlEditFile = storage_path("panos") . "\\" . $panoId . "\\vtour\\tour_edit.xml";
            //$vtourXmlStr = file_get_contents($xmlFile);
            $vtourXmlStr = file_get_contents($xmlEditFile);
            $vtourXmlObj = new \SimpleXMLElement($vtourXmlStr);
            $vtourSceneArr = $vtourXmlObj->xpath('scene');
            $hotspots = $vtourSceneArr[$sceneIndex]->xpath("hotspot");
            foreach ($hotspots as $hsVal) {    //
                if ($hsVal["name"] == $hostName) {
                    $hsVal['name'] = "";
                    $hsVal['style'] = "";
                    $hsVal['tooltip'] = "";
                    $hsVal['ath'] = "";
                    $hsVal['atv'] = "";
                    $hsVal['zoom'] = "";
                    $hsVal['linkedscene'] = "";
                    $hsVal['visible'] = "";
                }
            }
            //file_put_contents($xmlFile, $vtourXmlObj->asXML());
            file_put_contents($xmlEditFile, $vtourXmlObj->asXML());
        }

        $panoData = DB::select('select pano_id,sceneName,hotsName,type,linkedscene from hotspots where pano_id=? and visible=?', [$panoId, "true"]);
        $panoSum = DB::select('select count(1) as sum from hotspots where pano_id=? and visible=?', [$panoId, "true"]);
        $ress['panoData'] = $panoData;
        $ress['count'] = $panoSum[0]->sum;
        //$ress['xmlPath'] = $xmlFile;
        $ress['xmlPath'] = $xmlEditFile;
        $ress['sceneEname'] = $sceneName;
        return $ress;
    }

    //热点管理场景下拉
    public function showLabel(Request $request)
    {
        $title = $request->get("title");
        $panoId = $request->get("panoId");
        //无刷新动态更新热点管理列表
        if ($title == "全部场景") {
            $panoData = DB::select('select pano_id,sceneName,hotsName,type,linkedscene from hotspots where pano_id=? and visible=?', [$panoId, "true"]);
            $panoSum = DB::select('select count(1) as sum from hotspots where pano_id=? and visible=?', [$panoId, "true"]);
        } else {
            $panoData = DB::select('select pano_id,sceneName,hotsName,type,linkedscene from hotspots where pano_id=? and sceneName=? and visible=?', [$panoId, $title, "true"]);
            $panoSum = DB::select('select count(1) as sum from hotspots where pano_id=? and sceneName=? and visible=?', [$panoId, $title, "true"]);
        }
        $ress['panoData'] = $panoData;
        $ress['count'] = $panoSum[0]->sum;
        return $ress;
    }


}
