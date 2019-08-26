<?php


namespace App\Common\Krpano;

/**
 * krpano类封装
 * @package App\Common\Krpano
 */
class KrpanoEntity
{
    /**
     * 总标题
     */
    private $title;
    /**
     * 场景标题
     */
    private $sceneTitle;
    /**
     * krpano exe执行文件地址
     */
    private $krpanoPath;
    /**
     * 源文件地址
     */
    private $sourcePath;
    /**
     * 目标文件夹
     */
    private $targetPath;
    /**
     * 载入动画地址
     */
    private $loadingPath;
    /**
     * 音乐文件地址
     */
    private $musicPath;
    /**
     * logo文件地址
     */
    private $logoPath;

    /**
     * 启动页副title
     */
    private $startTitle;

    public function getKrpanoPath()
    {
        return $this->krpanoPath;
    }

    public function getSourcePath()
    {
        return $this->sourcePath;
    }

    public function getTargetPath()
    {
        return $this->targetPath;
    }

    public function getLoadingPath()
    {
        return $this->loadingPath;
    }

    public function getMusicPath()
    {
        return $this->musicPath;
    }

    public function getLogoPath()
    {
        return $this->logoPath;
    }

    public function getStartTitle()
    {
        return $this->startTitle;
    }


    public function setKrpanoPath($krpanoPath)
    {
        $this->krpanoPath = $krpanoPath;
    }
    public function setSourcePath($sourcePath)
    {
        $this->sourcePath = $sourcePath;
    }
    public function setTargetPath($targetPath)
    {
        $this->targetPath = $targetPath;
    }
    public function setLoadingPath($loadingPath)
    {
        $this->loadingPath = $loadingPath;
    }
    public function setMusicPath($musicPath)
    {
        $this->musicPath = $musicPath;
    }
    public function setLogoPath($logoPath)
    {
        $this->logoPath = $logoPath;
    }
    public function setStartTitle($startTitle)
    {
        $this->startTitle = $startTitle;
    }

    public function getTitle()
    {
        return $this->title;
    }
    public function setTitle($title)
    {
        $this->title = $title;
    }
    public function getSceneTitle()
    {
        return $this->sceneTitle;
    }
    public function setSceneTitle($sceneTitle)
    {
        $this->sceneTitle = $sceneTitle;
    }

}