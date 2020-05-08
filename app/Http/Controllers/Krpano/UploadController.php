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

    public function panos(Request $request)
    {
        $this->http_host = config("app.url");
        $this->base_name = config("app.name");

        //$panoId = $request->get('pano_id');

        if ($request->isMethod("POST")) {

            //$panoId = $request->get('pano_id');   //demo : 15477
            $userId = $request->get('user_id');
            $panoId = '29571';
            //$panoId = '2100';
            $frContent = file_get_contents("http://120.76.210.152:8077/api/Esf/ApiEsf720VRModel?id=".$panoId);
            $frApiData = json_decode($frContent);

            //$userId = $frApiData->Data->AgentInfo->ID;

            //房源信息和经纪人信息存入缓存
            if ($frApiData->Code ==200){
                Cache::forever("houseInfo"."_".$panoId,$frApiData->Data->EsfInfo);
                Cache::forever("agentInfo"."_".$panoId,$frApiData->Data->AgentInfo);
            }

            if ($frApiData->Code==200 && $frApiData->Data->EsfInfo) {
                $title = $frApiData->Data->EsfInfo->Title;
            }else{
                $title = "";
            }

            if($frApiData->Code==200 && $frApiData->Data->AgentInfo){
                $agentImgUrl = $frApiData->Data->AgentInfo->ImageUrl;
                $agentPhone = $frApiData->Data->AgentInfo->Mobile;
            }else{
                $agentImgUrl = "";
                $agentPhone = "";
            }

            $houseName = $request->get("house_name");   //楼盘名称
            $houseUsed = $request->get("house_used");   //房源类型
            $houseType = $request->get("house_type");   //户型
            $houseArea = $request->get("house_area");   //面积
            $houseRemark = $request->get("remark");     //备注

            $houseCustom = $request->get("custom");     //自定义名

            $fileNames = $request->file("filename");

            $panoimgPath = str_replace('\\', '/', storage_path() . '/panos/');

            if (!is_dir(storage_path('panos'))) {
                mkdir(storage_path('panos'), 0777, true);
            }

            //panos有记录则先删除再添加
            $resPanoData = DB::select('select pano_id,user_id from panos where pano_id=?', [$panoId]);
            if ($resPanoData) {
                DB::delete('delete from panos where pano_id=' . $panoId);
            }
            $pano = new Pano;
            $pano->pano_id = $panoId;
            $pano->user_id = $userId;
            $pano->house_name = $houseName;
            $pano->house_used = $houseUsed;
            $pano->house_type = $houseType;
            $pano->house_area = $houseArea;
            $pano->remark = $houseRemark;
            $pano->created_at = date('Y-m-d H:i:s', time());
            $pano->save();

            //同一个上传者 查场景id 如果有 则先删除id下的数据 再批量插入
            $result = DB::select('select pano_id,user_id from imgs where user_id=? and pano_id=?', [$userId, $panoId]);
            if ($result) {
                DB::delete('delete from imgs where user_id=' . $userId . ' and pano_id=' . $panoId);
                $path = $panoimgPath . $panoId . '/';
                clearDir($path);                    //删除目录以及子目录文件
            }

            //自定义全景字段合并
            foreach ($fileNames as $k1=>$v1){
                if ($k1 == "'custom'"){
                    $fileNames[$houseCustom] =  $fileNames["'custom'"];
                    unset($fileNames["'custom'"]);
                }
            }

            //批量插入数据库
            $zh_name = array();
            foreach ($fileNames as $key => $val) {
                foreach ($val as $min_k => $min_v) {
                    $key = trim($key,"'");

                    if (array_key_exists($key,ApiErrDesc::PANO_ARR_REPLACE)){
                        $zh_name[] = ApiErrDesc::PANO_ARR_REPLACE[$key];
                    }else{
                        $zh_name[] = $houseCustom;
                    }

                    $imgName = $min_v->getClientOriginalName();
                    $imgSize = $min_v->getClientSize();
                    $imgCreated_at = date('Y-m-d H:i:s', $min_v->getaTime());

                    DB::table('imgs')->insert(array(
                        'user_id' => $userId,
                        'pano_id' => $panoId,
                        'name' => $imgName,
                        'length' => $imgSize,
                        'created_at' => $imgCreated_at,
                        'updated_at' => $imgCreated_at
                    ));

                    $res = $min_v->move($panoimgPath . $panoId, $imgName);

                    //上传成功后返回josn
                    if ($res) {
                        $r['code'] = ApiErrDesc::UPLOAD_SUCCESS[0];
                        $r['msg'] = ApiErrDesc::UPLOAD_SUCCESS[1];
                        $r['user_id'] = $userId;
                        $r['pano_id'] = $panoId;
                        $r['title'] = $title;
                        $r['agentImgUrl'] = $agentImgUrl;
                        $r['agentPhone'] = $agentPhone;
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
        }else{
            $r['code'] = ApiErrDesc::NO_METHOD_POST[0];
            $r['msg'] = ApiErrDesc::NO_METHOD_POST[1];
            $r['iamges'][] = [];
        }
        return json_encode($r);
    }

    /**
     * 多全景图生成漫游
     * @return string
     */
    public function panosExec(Request $request){
        //调用上传全景图的API
        $getFilesData = $this->panos($request);
        $getFilesData = json_decode($getFilesData);

        $title = $getFilesData->title;
        $agentImgUrl = $getFilesData->agentImgUrl;
        $agentPhone = $getFilesData->agentPhone;
        $userId = $getFilesData->user_id;
        $panoId = $getFilesData->pano_id;

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

        //执行切片
        exec($krpano . ' makepano -config=' . 'templates/vtour-multires.config' . " " . $imgRealStr, $opt, $r);

        if (!$r) {
            if (!is_dir($imgRealDir . KrpanoContants::VTOUR_NAME)) {
                $res['code'] = ApiErrDesc::ERR_KRPANO_ENV[0];
                $res['msg'] = ApiErrDesc::ERR_KRPANO_ENV[1];
                $res['url'] = '';
            } else {
                changVtourskinXml($imgRealDir . 'vtour/skin/vtourskin.xml',$panoId,$agentImgUrl,$agentPhone);
                changTourXml($imgRealDir . 'vtour/tour.xml', $zh_name,$title,$panoId);

                foreach ($keepNameArr as $k_thumb => $v_mbName) {
                    //$thumb_path = $this->http_host . '/' . $this->base_name . '/storage/panos/' . $panoId . '/thumb/' . $k_thumb;
                    $thumb_path = $this->http_host . '/' .'storage/panos/' . $panoId . '/thumb/' . $k_thumb;
                    DB::update("update imgs set thumb='" . $thumb_path . "',mb_name='" . $v_mbName . "' where name=?", [$k_thumb]);
                }

                $res['code'] = ApiErrDesc::SUCCESS_KRPANO[0];
                $res['msg'] = ApiErrDesc::SUCCESS_KRPANO[1];
                $res['pano_id'] = $panoId;
                //$res['url'] = $this->http_host . '/' . $this->base_name . '/storage/panos/' . $panoId . '/tour.html';
                $res['url'] = $this->http_host . '/' . 'storage/panos/' . $panoId . '/tour.html';
                $updated_at = date('Y-m-d H:i', time());
                DB::update("update panos set panoUrl='" . $res['url'] . "',updated_at='" . $updated_at . "' where pano_id=? and user_id=?", [$panoId, $userId]);
            }
        } else {
            $res['code'] = ApiErrDesc::ERR_KRPANO_PARAMS[0];
            $res['msg'] = ApiErrDesc::ERR_KRPANO_PARAMS[1];
            $res['url'] = '';
        }
        return $res;
    }


}
