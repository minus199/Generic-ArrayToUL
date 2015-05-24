<?php
/**
 * Created by PhpStorm.
 * User: minus
 * Date: 5/23/15
 * Time: 10:30 PM
 */

namespace MiNuS199;


class CustomJsRecursiveIterator extends \RecursiveDirectoryIterator
{
    private $filesToUglify = array(), $uglify = true, $DirectoriesMapper;

    const JS_RECURSE_FLAG_DISABLE_UGLIFY = 'dis_uglify';

    public function __construct($path = true, $flags = null)
    {
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
        if (!parent::isDir()) {
            $file = parent::openFile();
            $current = '';
            while ($file->current()) {
                $current .= $file->current();
                $file->next();
            }

            $this->filesToUglify[] = $current;

            return parent::current();
        }

        return false;
    }

    public function iterateTillEnd(){
        while($this->current()) { $this->next(); }
        return $this;
    }

    public function save(){
        $this->getDirectoriesMapper()->getUnifiedFilePath();
    }

    /**
     * @return array
     */
    public function getUglifiedContent()
    {
        if (!$this->uglify)
            return false;

        if (file_exists($this->getDirectoriesMapper()->getUnifiedFilePath())) {
            $handle = fopen($this->getDirectoriesMapper()->getUnifiedFilePath(), "r");
            $content = fopen($handle, filesize($this->getDirectoriesMapper()->getUnifiedFilePath()));
            fclose($handle);
            return $content;
        }

        var_dump($this->iterateTillEnd()->filesToUglify);
        exit;
        return implode("\n\n", $this->iterateTillEnd()->filesToUglify);
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