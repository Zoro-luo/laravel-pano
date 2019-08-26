<?php

namespace App\Http\Controllers\Admin;

use App\Model\Hotspot;
use App\Model\Pano;
use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\DB;

class PanoController extends Controller
{
    public function index()
    {
        $panos = Pano::get();
        return view('admin.pano.index', ['panos' => $panos]);
    }

    public function indexHotspot(Request $request)
    {
        $pano_id = $request->pano_id;
        return view("admin.pano.indhs", ["panoId" => $pano_id]);
    }

    //缓存热点的下拉选择的NAME
    public function changeHotspot(Request $request)
    {
        $changeName = $request->get("changeName");
        Cache::put("hostName", $changeName, 5);
    }

    //ajax 保持热点坐标
    public function keepHotspot(Request $request)
    {
        $panoId = $request->get("panoId");
        $ath = $request->get("h");
        $atv = $request->get("v");
        $hostName = $request->get("hostName");
        $sceneName = $request->get("sceneName");
        $sceneIndex = (int)$request->get("sceneIndex");


        $selectName = $request->get("selectName");
        $resChangeName = Cache::pull("hostName");
        if ($resChangeName) {
            $linkedscene = $resChangeName;
        } else {
            $linkedscene = $selectName;
        }

        if ($selectName) {
            // 同pano_id 下的同热点名 则update 覆盖
            $resHotSpot = DB::select('select pano_id,hotsName from hotspots where pano_id=? and hotsName=?', [$panoId, $hostName]);
            if ($resHotSpot) {
                $pano_id = $resHotSpot[0]->pano_id;
                $hotsName = $resHotSpot[0]->hotsName;
                $updated_at = date('Y-m-d H:i:s', time());
                $affected = DB::update("update hotspots set linkedscene='" . $linkedscene . "' ath='" . $ath . "',atv='" . $atv . "',updated_at='" . $updated_at . "' where pano_id=? and hotsName=?", [$pano_id, $hotsName]);
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
                $hotspot->linkedscene = $linkedscene;
                $hotspot->created_at = date('Y-m-d H:i:s', time());
                $hotspot->save();
            }
        }

        //没有则xml添加热点
        $xmlFile = storage_path("panos") . "\\" . $panoId . "\\vtour\\tour.xml";
        $vtourXmlStr = file_get_contents($xmlFile);
        $vtourXmlObj = new \SimpleXMLElement($vtourXmlStr);
        $currScene = $vtourXmlObj->scene[$sceneIndex];
        $currHotSpot = $currScene->addChild('hotspot ');
        $currHotSpot->addAttribute("name", $hostName);
        $currHotSpot->addAttribute("style", "skin_hotspotstyle");
        $currHotSpot->addAttribute("ath", $ath);
        $currHotSpot->addAttribute("atv", $atv);
        $currHotSpot->addAttribute("scale", "0.45");
        $currHotSpot->addAttribute("zoom", "true");
        $currHotSpot->addAttribute("linkedscene", $linkedscene);
        file_put_contents($xmlFile, $vtourXmlObj->asXML());

        $ress['xmlPath'] = $xmlFile;
        $ress['sceneName'] = $sceneName;
        return $ress;

    }

    //删除热点
    public function deleteHotspot(Request $request)
    {
        //删除数据库热点数据
        $hsName = $request->get("hsName");
        $panoId = $request->get("panoId");
        $sceneIndex = $request->get("sceneIndex");


        $resource = DB::select("select pano_id,hotsName from hotspots where hotsName=?", [$hsName]);
        if ($resource) {
            $res = DB::update("update hotspots set visible= 'false' where hotsName=?", [$hsName]);
        }
        //删除xml热点数据
        $xmlFile = storage_path("panos") . "\\" . $panoId . "\\vtour\\tour.xml";
        $vtourXmlStr = file_get_contents($xmlFile);
        $vtourXmlObj = new \SimpleXMLElement($vtourXmlStr);
        $vtourSceneArr = $vtourXmlObj->xpath('scene');
        $hotspots = $vtourSceneArr[$sceneIndex]->xpath("hotspot");


        foreach ($hotspots as $hs) {

            if ($hs['name'] == $hsName) {

                if (!$hs['visible']) {
                    $hs->addAttribute("visible", "false");
                } else {
                    $hs['visible'] = 'false';
                }
            }
        }

        file_put_contents($xmlFile, $vtourXmlObj->asXML());
        exit('Delete Ok!');

    }

    //编辑热点坐标
    public function editHs(Request $request)
    {
        $hostName = $request->get("hostName");
        $ath = $request->get("h");
        $atv = $request->get("v");
        $sceneName = $request->get("sceneName");
        $panoId = $request->get("panoId");
        $sceneIndex = $request->get("sceneIndex");
        $selectName = $request->get("selectName");

        $resChangeName = Cache::pull("hostName");
        if ($resChangeName) {
            $linkedscene = $resChangeName;
        } else {
            $linkedscene = $selectName;
        }

        //操作xml
        $xmlFile = storage_path("panos") . "\\" . $panoId . "\\vtour\\tour.xml";
        $vtourXmlStr = file_get_contents($xmlFile);
        $vtourXmlObj = new \SimpleXMLElement($vtourXmlStr);
        $vtourSceneArr = $vtourXmlObj->xpath('scene');
        $hotspots = $vtourSceneArr[$sceneIndex]->xpath("hotspot");
        foreach ($hotspots as $val) {
            if ($hostName == $val['name']) {
                $val['linkedscene'] = $linkedscene;
                $val['ath'] = $ath;
                $val['atv'] = $atv;
                unset($val['ondown']);
                unset($val['onclick']);
                //$val['ondown'] = "";
                //$val['onclick'] = "";
            }
        }
        file_put_contents($xmlFile, $vtourXmlObj->asXML());

        $ress['sceneName'] = $sceneName;
        $ress['hostName'] = $hostName;
        return $ress;
    }

    //编辑热点拖拽
    public function editHotspot(Request $request)
    {
        $sceneIndex = $request->get("sceneIndex");
        $panoId = $request->get("panoId");
        //$sceneName = $request->get("sceneName");

        $xmlFile = storage_path("panos") . "\\" . $panoId . "\\vtour\\tour.xml";
        $vtourXmlStr = file_get_contents($xmlFile);
        $vtourXmlObj = new \SimpleXMLElement($vtourXmlStr);
        $vtourSceneArr = $vtourXmlObj->xpath('scene');

        $hotspots = $vtourSceneArr[$sceneIndex]->xpath("hotspot");

        foreach ($hotspots as $hsVal) {

            if (!$hsVal['ondown'] || !$hsVal['onclick']) {
                $hsVal->addAttribute("ondown", "draghotspot();");
                $hsVal->addAttribute("onclick", "js(handleTackAction( " . $hsVal['name'] . " ))");
            } else {
                $hsVal['ondown'] = "draghotspot();";
                $hsVal['onclick'] = "js(handleTackAction( " . $hsVal['name'] . " ))";
            }
            $hsVal['linkedscene'] = "";
        }
        file_put_contents($xmlFile, $vtourXmlObj->asXML());
    }

    //设定启动视角
    public function startup(Request $request)
    {
        $sceneIndex = $request->get("sceneIndex");
        $panoId = $request->get("panoId");
        $hlookat = $request->get("hlookat");
        $vlookat = $request->get("vlookat");

        //操作xml
        $xmlFile = storage_path("panos") . "\\" . $panoId . "\\vtour\\tour.xml";
        $vtourXmlStr = file_get_contents($xmlFile);
        $vtourXmlObj = new \SimpleXMLElement($vtourXmlStr);
        $vtourSceneArr = $vtourXmlObj->xpath('scene');
        $views = $vtourSceneArr[$sceneIndex]->xpath("view");
        $views[0]['hlookat'] = $hlookat;
        $views[0]['vlookat'] = $vlookat;
        file_put_contents($xmlFile, $vtourXmlObj->asXML());

        $lookat = ['h' => $hlookat, 'v' => $vlookat];
        return $lookat;
    }
}
