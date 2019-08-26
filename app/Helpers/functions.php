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











