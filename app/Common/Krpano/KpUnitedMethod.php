<?php


namespace App\Common\Krpano;

use Couchbase\Document;

/**
 * krpano功能化的方法
 * Class KpUnitedMethod
 * @package App\Common\Krpano
 */

class KpUnitedMethod
{
    public $xmlPath;    //xml文件
    public $rootNode;   //xml根节点

    public function __construct($xmlPath,$rootNode)
    {
        $this->xmlPath = $xmlPath;
        $this->rootNode = $rootNode;

        $vtourDocXml = new \DOMDocument();
        $vtourDocXml->load($this->xmlPath);

        $this->rootNode = $vtourDocXml->getElementsByTagName("krpano")->item(0);
    }

    //container背景
    public function layerContainer()
    {
    }

}