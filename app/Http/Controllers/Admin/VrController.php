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
    public function index()
    {
        $panos = Pano::get();
        return view('admin.vr.list', ['panos' => $panos]);
    }

    //热点编辑页
    public function update(Request $request)
    {
        $pano_id = $request->pano_id;
        $xmlFile = storage_path("panos") . "\\" . $pano_id . "\\vtour\\tour.xml";
        $vtourDocXml = new \DOMDocument();
        $vtourDocXml->load($xmlFile);
        $actionStr = $vtourDocXml->getElementsByTagName("action")->item(0)->nodeValue;
        /* if(startscene === null OR !scene[get(startscene)], copy(startscene,scene[0].name); );loadscene(get(startscene), null, MERGE);
            if(startactions !== null, startactions() );
        */
        $start = strpos($actionStr,'name');
        $start = $start-3;
        $targetId = substr($actionStr, $start, 1);   //获取到0
        //根据拿到的场景index 获取场景的title
        $vtourXmlStr = file_get_contents($xmlFile);
        $vtourXmlObj = new \SimpleXMLElement($vtourXmlStr);
        $vtourSceneArr = $vtourXmlObj->xpath('scene');
        $goalScene = $vtourSceneArr[$targetId]['title'];
        // SimpleXMLElement对象 转为数组取值
        $goalScene = json_encode($goalScene);
        $goalScene = json_decode($goalScene,true);
        $sceneTitle = $goalScene[0];

        $panoData = DB::select('select pano_id,sceneName,hotsName,type,linkedscene from hotspots where pano_id=? and visible=?', [$pano_id, "true"]);
        $panoSum = DB::select('select count(1) as sum from hotspots where pano_id=? and visible=?', [$pano_id, "true"]);
        return view("admin.vr.detail", ["panoId" => $pano_id, "panoData" => $panoData, "sceneTitle"=>$sceneTitle, "count" => $panoSum[0]->sum]);
    }

    //设置为封面
    public function setcover(Request $request)
    {
        $sceneIndex = $request->get("sceneIndex");
        $panoId = $request->get("panoId");
        $sceneTitle = $request->get("sceneTitle");

        if ($sceneIndex != null) {
            //操作xml
            $xmlFile = storage_path("panos") . "\\" . $panoId . "\\vtour\\tour.xml";
            $vtourDocXml = new \DOMDocument();
            $vtourDocXml->load($xmlFile);
            $actionDom = $vtourDocXml->getElementsByTagName("action");
            $nodeVal = "if(startscene === null OR !scene[get(startscene)], copy(startscene,scene[" . $sceneIndex . "].name); );loadscene(get(startscene), null, MERGE);if(startactions !== null, startactions() );";
            $actionDom->item(0)->nodeValue = $nodeVal;
            $vtourDocXml->save($xmlFile);

            return $sceneTitle;
        }
    }

    //热点隐藏显示切换
    public function toggleHot(Request $request)
    {
        $sceneIndex = $request->get("sceneIndex");
        $panoId = $request->get("panoId");


        $xmlFile = storage_path("panos") . "\\" . $panoId . "\\vtour\\tour.xml";
        $vtourXmlStr = file_get_contents($xmlFile);
        $vtourXmlObj = new \SimpleXMLElement($vtourXmlStr);
        $vtourSceneArr = $vtourXmlObj->xpath('scene');
        $hotspots = $vtourSceneArr[$sceneIndex]->xpath("hotspot");

        foreach ($hotspots as $hsVal) {
            if ($hsVal['visible'] == "true") {
                $hsVal['visible'] = "false";
            } else {
                $hsVal['visible'] = "true";
            }
        }
        file_put_contents($xmlFile, $vtourXmlObj->asXML());

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
        $vtourXmlStr = file_get_contents($xmlFile);
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
        file_put_contents($xmlFile, $vtourXmlObj->asXML());

        //无刷新动态更新热点管理列表
        $panoData = DB::select('select pano_id,sceneName,hotsName,type,linkedscene from hotspots where pano_id=? and visible=?', [$panoId, "true"]);
        $panoSum = DB::select('select count(1) as sum from hotspots where pano_id=? and visible=?', [$panoId, "true"]);
        //return view("admin.vr.detail", ["panoId" => $pano_id,"panoData"=>$panoData,"count"=>$panoSum[0]->sum]);

        $ress['panoData'] = $panoData;
        $ress['count'] = $panoSum[0]->sum;
        $ress['xmlPath'] = $xmlFile;
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
            $vtourXmlStr = file_get_contents($xmlFile);
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
            file_put_contents($xmlFile, $vtourXmlObj->asXML());

            //无刷新动态更新热点管理列表
            $panoData = DB::select('select pano_id,sceneName,hotsName,type,linkedscene from hotspots where pano_id=? and visible=?', [$panoId, "true"]);
            $panoSum = DB::select('select count(1) as sum from hotspots where pano_id=? and visible=?', [$panoId, "true"]);

            $ress['panoData'] = $panoData;
            $ress['count'] = $panoSum[0]->sum;
            $ress['xmlPath'] = $xmlFile;
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

            if ($result) {     // 向xml追加新的热点标签

                $xmlFile = storage_path("panos") . "\\" . $panoId . "\\vtour\\tour.xml";
                $vtourXmlStr = file_get_contents($xmlFile);
                $vtourXmlObj = new \SimpleXMLElement($vtourXmlStr);
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

                file_put_contents($xmlFile, $vtourXmlObj->asXML());
            }

        }

        //无刷新动态更新热点管理列表
        $panoData = DB::select('select pano_id,sceneName,hotsName,type,linkedscene from hotspots where pano_id=? and visible=?', [$panoId, "true"]);
        $panoSum = DB::select('select count(1) as sum from hotspots where pano_id=? and visible=?', [$panoId, "true"]);
        //return view("admin.vr.detail", ["panoId" => $pano_id,"panoData"=>$panoData,"count"=>$panoSum[0]->sum]);

        $ress['panoData'] = $panoData;
        $ress['count'] = $panoSum[0]->sum;
        $ress['xmlPath'] = $xmlFile;
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
            //插库成功后 想xml追加新的热点标签
            if ($result) {
                $xmlFile = storage_path("panos") . "\\" . $panoId . "\\vtour\\tour.xml";
                $vtourXmlStr = file_get_contents($xmlFile);
                $vtourXmlObj = new \SimpleXMLElement($vtourXmlStr);

                //删除旧的热点标签
                /*$vtourSceneArr = $vtourXmlObj->xpath('scene');
                $hotspots = $vtourSceneArr[$sceneIndex]->xpath("hotspot");
                var_dump($hotspots);*/

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
                file_put_contents($xmlFile, $vtourXmlObj->asXML());
            }
        }
        //无刷新动态更新热点管理列表
        $panoData = DB::select('select pano_id,sceneName,hotsName,type,linkedscene from hotspots where pano_id=? and visible=?', [$panoId, "true"]);
        $panoSum = DB::select('select count(1) as sum from hotspots where pano_id=? and visible=?', [$panoId, "true"]);

        $ress['panoData'] = $panoData;
        $ress['count'] = $panoSum[0]->sum;
        $ress['xmlPath'] = $xmlFile;
        $ress['sceneEname'] = $sceneName;
        return $ress;
    }

    //编辑弹出热点后点红X的操作
    public function checkRedErr(Request $request)
    {
        $sceneIndex = $request->get("sceneIndex");
        $panoId = $request->get("panoId");
        $hostName = $request->get("hostName");
        $sceneName = $request->get("sceneName");

        $xmlFile = storage_path("panos") . "\\" . $panoId . "\\vtour\\tour.xml";
        $vtourXmlStr = file_get_contents($xmlFile);
        $vtourXmlObj = new \SimpleXMLElement($vtourXmlStr);
        $vtourSceneArr = $vtourXmlObj->xpath('scene');
        $hotspots = $vtourSceneArr[$sceneIndex]->xpath("hotspot");
        foreach ($hotspots as $hsVal) {
            if ($hsVal["name"] == $hostName) {
                $hsVal['visible'] = "true";
            }
        }
        DB::update("update hotspots set visible= 'true' where hotsName=?", [$hostName]);
        file_put_contents($xmlFile, $vtourXmlObj->asXML());
        return $sceneName;
    }

    //点击编辑按钮操作
    public function editHotspot(Request $request)
    {
        $sceneIndex = $request->get("sceneIndex");
        $panoId = $request->get("panoId");
        $hostName = $request->get("hostName");
        $sceneName = $request->get("sceneName");

        $xmlFile = storage_path("panos") . "\\" . $panoId . "\\vtour\\tour.xml";
        $vtourXmlStr = file_get_contents($xmlFile);
        $vtourXmlObj = new \SimpleXMLElement($vtourXmlStr);
        $vtourSceneArr = $vtourXmlObj->xpath('scene');
        $hotspots = $vtourSceneArr[$sceneIndex]->xpath("hotspot");
        foreach ($hotspots as $hsVal) {
            if ($hsVal["name"] == $hostName) {
                $hsVal['visible'] = "false";
            }
        }
        file_put_contents($xmlFile, $vtourXmlObj->asXML());

        //sql返回该热点的坐标值
        $panoData = DB::select('select pano_id,hotsName,ath,atv,type from hotspots where pano_id=? and hotsName=?', [$panoId, $hostName]);
        $res["panoId"] = $panoData[0]->pano_id;
        $res["hotsName"] = $panoData[0]->hotsName;
        $res["type"] = $panoData[0]->type;
        $res["ath"] = $panoData[0]->ath;
        $res["atv"] = $panoData[0]->atv;
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
            $vtourXmlStr = file_get_contents($xmlFile);
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
            file_put_contents($xmlFile, $vtourXmlObj->asXML());
        }

        $panoData = DB::select('select pano_id,sceneName,hotsName,type,linkedscene from hotspots where pano_id=? and visible=?', [$panoId, "true"]);
        $panoSum = DB::select('select count(1) as sum from hotspots where pano_id=? and visible=?', [$panoId, "true"]);
        $ress['panoData'] = $panoData;
        $ress['count'] = $panoSum[0]->sum;
        $ress['xmlPath'] = $xmlFile;
        $ress['sceneEname'] = $sceneName;
        return $ress;
    }


}
