<?php
/**
 * Created by PhpStorm.
 * User: minus
 * Date: 5/23/15
 * Time: 11:20 PM
 */

namespace MiNuS199;


/**
 * Class DirectoriesMapper
 * @package MiNuS199
 */
class DirectoriesMapper{
    private $basePath, $jsFolderPath, $minJsFolder, $unifiedFilePath;

    /**
     * @param bool $basePath
     * @param bool $jsFolderPath
     * @param bool $unifiedFilePath
     * @throws \Exception
     */
    function __construct($basePath = false, $jsFolderPath = false, $unifiedFilePath = false)
    {
        $this
            ->setBasePath(array(dirname(__DIR__), "Resources"))
            ->setJsFolderPath("JS")
            ->setMinJsFolder()
            ->setUnifiedFilePath(".min" . DIRECTORY_SEPARATOR . "togglerApp.min.js");
    }

    /**
     * @return mixed
     */
    public function getUnifiedFilePath()
    {
        return $this->unifiedFilePath;
    }

    /**
     * @param $unifiedFilePath
     * @return $this
     */
    private function setUnifiedFilePath($unifiedFilePath)
    {
        $this->unifiedFilePath = $this->getJsFolderPath() . DIRECTORY_SEPARATOR . $unifiedFilePath;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getJsFolderPath()
    {
        return $this->jsFolderPath;
    }

    /**
     * @param $jsFolderPath
     * @return $this
     */
    private function setJsFolderPath($jsFolderPath)
    {
        $this->jsFolderPath = $this->getBasePath() . DIRECTORY_SEPARATOR . $jsFolderPath;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getBasePath()
    {
        return $this->basePath;
    }

    /**
     * @param array $basePath
     * @return $this
     * @throws \Exception
     */
    private function setBasePath($basePath = array())
    {
        if (!is_array($basePath))
            throw new \Exception("Must pass path in form of array.");

        $basePath = !empty($basePath) ? $basePath : array(dirname(__DIR__), "Resources");

        $this->basePath = implode(DIRECTORY_SEPARATOR, $basePath);
        return $this;
    }

    /**
     * @return mixed
     */
    public function getMinJsFolder()
    {
        return $this->minJsFolder;
    }

    /**
     * @return $this
     */
    private function setMinJsFolder()
    {
        $this->minJsFolder = $this->getJsFolderPath() . ".min";
        return $this;
    }
}