<?php
/**
 * Created by PhpStorm.
 * User: minus
 * Date: 5/23/15
 * Time: 10:30 PM
 */

namespace MiNuS199;


use GK\JavascriptPacker;

class CustomJsRecursiveIterator extends \RecursiveDirectoryIterator
{
    private $filesToUglify = array(), $uglify = true, $DirectoriesMapper;

    const JS_RECURSE_FLAG_DISABLE_UGLIFY = 'dis_uglify';

    public function __construct($path = true, $flags = null)
    {
        `grunt`;

        $this->setDirectoriesMapper();

        $path = $path ? $path : $this->getDirectoriesMapper()->getMinJsFolder();

        if ($flags == self::JS_RECURSE_FLAG_DISABLE_UGLIFY) {
            $path = $this->getDirectoriesMapper()->getJsFolderPath();
            $this->uglify = false;
            $flags = null;
        }

        parent::__construct($path, $flags);
    }

    public function current()
    {
        /* @var $current \SplFileInfo */
        $current = parent::current();

        if ($current->isFile() && $current->getExtension() == "js"){
            $file = $current->openFile();

            $content = '';
            while ($file->current()) {
                $content .= $file->current();
                $file->next();
            }

            if (preg_match('!~loading: (?P<loading>[\d]+)!', $current, $matches)){
                $this->filesToUglify[(int)$matches['loading']] = $content;
            } else {
                $this->filesToUglify[] = $content;
            }

            unlink($file->getRealPath());

            return $current;
        }

        return false;
    }

    public function next()
    {
        parent::next();
    }


    public function iterateTillEnd(){
        while($this->current()) { $this->next(); }
        return $this;
    }

    public function save(){
        $this->getDirectoriesMapper()->getUnifiedFilePath();
    }

    public function getContent(){
        if ($this->uglify)
            return $this->getUglifiedContent();

        return $this->getNormalContent();
    }

    private function getNormalContent(){
        return implode("\n\n", $this->iterateTillEnd()->filesToUglify);
    }

    /**
     * @return array
     */
    private function getUglifiedContent()
    {
        if (file_exists($this->getDirectoriesMapper()->getUnifiedFilePath())) {
            $jsFolderModifiedTime = (float)reset(explode(" ", `find {$this->getDirectoriesMapper()->getJsFolderPath()} -type f -printf '%T@ %p\n' | sort -n | tail -1`));
            $jsUnifiedModifiedTime = (float)reset(explode(" ", `find {$this->getDirectoriesMapper()->getUnifiedFilePath()} -type f -printf '%T@ %p\n' | sort -n | tail -1`));

            if ($jsFolderModifiedTime < $jsUnifiedModifiedTime){
                $handle = fopen($this->getDirectoriesMapper()->getUnifiedFilePath(), "r");
                $content = fread($handle, filesize($this->getDirectoriesMapper()->getUnifiedFilePath()));
                fclose($handle);
                return $content;
            }
        }

        echo PHP_EOL . "[Enter save unified]" . PHP_EOL;
        $uglified = implode("\n\n", $this->iterateTillEnd()->filesToUglify);
        $packer = new JavascriptPacker($uglified);
        $uglifiedContent = '!function (t, e) {' . $packer->pack() . '}({}, function () {return this}());';

        $handle = fopen($this->getDirectoriesMapper()->getUnifiedFilePath(), "w+");
        fwrite($handle, $uglifiedContent);
        fclose($handle);

        return $uglifiedContent;
    }

    /**
     * @return DirectoriesMapper
     */
    public function getDirectoriesMapper()
    {
        return $this->DirectoriesMapper;
    }

    /**
     * @return $this
     */
    private function setDirectoriesMapper()
    {
        $this->DirectoriesMapper = new DirectoriesMapper();
        return $this;
    }
}