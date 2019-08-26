<?php

/**
 * 热点操作[add edit del]
 * @param $xmlFile
 */
function makeSelectAction($xmlFile)
{
    $vtourDocXml = new \DOMDocument();
    $vtourDocXml->load($xmlFile);
    //根节点
    $krpanoNode = $vtourDocXml->getElementsByTagName("krpano")->item(0);

    //add_scene_items
    $add_scene_items = $vtourDocXml->createElement("action");
    $add_scene_items->setAttribute("name", "add_scene_items");
    $add_scene_items->setAttribute("scope", "local");
    $add_scene_items->nodeValue = "
		for(set(i,0), i LT scene.count, inc(i),
		caller.additem(
		calc('[img src=[dq]' + scene[get(i)].thumburl + '[dq] style=[dq]border:1px solid rgba(255,255,255,0.5);
		width:45px;height:45px;vertical-align:middle;margin-right:180px;[dq]/] '+scene[get(i)].title),
		calc('ajax_change_select('+ scene[get(i)].name +',null,MERGE,BLEND(0.5))') );
		);
    ";


    //ajax_save_hotspot
    $ajax_save_hotspot = $vtourDocXml->createElement("action");
    $ajax_save_hotspot->setAttribute("name", "ajax_save_hotspot");
    $ajax_save_hotspot->setAttribute("type", "Javascript");
    $ajax_save_hotspot->nodeValue = "
		$.ajax({
		headers: {'X-CSRF-TOKEN': $('meta[name=\"csrf-token\"]').attr('content')},
		type:\"POST\",
		url:'/pano/admin/keepspot',
		data:{'hostName':args[1],'h':args[2], 'v':args[3],
		'sceneName':args[4],'panoId':args[5],'sceneIndex':args[6],'selectName':args[7]},
		success:function(e){
		console.log(e);
		leaf.message('Add Hotspot Succeed!',\"pano\");
		krpano.call(\"loadpano(tour.xml, NULL, MERGE, BLEND(0.1));\");
		krpano.call(\"loadscene(\" + e.sceneName + \", NULL, MERGE, BLEND(0.1));\");
		}
		});
    ";


    //ajax_update_hotspot
    $ajax_update_hotspot = $vtourDocXml->createElement("action");
    $ajax_update_hotspot->setAttribute("name", "ajax_update_hotspot");
    $ajax_update_hotspot->setAttribute("type", "Javascript");
    $ajax_update_hotspot->nodeValue = "
		$.ajax({
		headers: {'X-CSRF-TOKEN': $('meta[name=\"csrf-token\"]').attr('content')},
		type:\"POST\",
		url:'/pano/admin/ediths',
		data:{'hostName':args[1],'h':args[2], 'v':args[3],
		'sceneName':args[4],'panoId':args[5],'sceneIndex':args[6],'selectName':args[7]},
		success:function(e){
		leaf.message('Update Hotspot Succeed!',\"pano\");
		krpano.call(\"loadpano(tour.xml, NULL, MERGE, BLEND(0.1));\");
		krpano.call(\"loadscene(\" + e.sceneName + \", NULL, MERGE, BLEND(0.1));\");
		}
		});
    ";

    //ajax_delete_hotspot
    $ajax_delete_hotspot = $vtourDocXml->createElement("action");
    $ajax_delete_hotspot->setAttribute("name","ajax_delete_hotspot");
    $ajax_delete_hotspot->setAttribute("type","Javascript");
    $ajax_delete_hotspot->nodeValue ="
		$.ajax({
		headers: {'X-CSRF-TOKEN': $('meta[name=\"csrf-token\"]').attr('content')},
		type:\"POST\",
		url:'/pano/admin/delspot',
		data:{'hsName':args[1],'panoId':args[2],'sceneIndex':args[3]},
		success:function(e){
		console.log(e);
		leaf.message('Delete Hotspot Succeed!',\"pano\");
		}
		});
    ";

    // ajax_change_select
    $ajax_change_select = $vtourDocXml->createElement("action");
    $ajax_change_select->setAttribute("name","ajax_change_select");
    $ajax_change_select->setAttribute("type","Javascript");
    $ajax_change_select->nodeValue = "
		$.ajax({
		headers: {'X-CSRF-TOKEN': $('meta[name=\"csrf-token\"]').attr('content')},
		type:\"POST\",
		url:'/pano/admin/changespot',
		data:{'changeName':args[1]},
		success:function(e){
		console.log(e);
		}
		});
    ";

    $krpanoNode->appendChild($add_scene_items);
    $krpanoNode->appendChild($ajax_save_hotspot);
    $krpanoNode->appendChild($ajax_update_hotspot);
    $krpanoNode->appendChild($ajax_delete_hotspot);
    $krpanoNode->appendChild($ajax_change_select);
    $vtourDocXml->save($xmlFile);
}
