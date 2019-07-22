<?php

namespace mipotech\criticalcss\storage;

use yii\helpers\FileHelper;

/**
 * This handles reading and writing critical-path CSS from disk.
 */
interface StorageInterface
{
     /** @var string */
    //protected $storage;
    
    /** @var yii\helpers\FileHelper */
    //protected $files;
    
     /** @var bool */
    //protected $pretend;
    
    /**
     * Returns generated critical-path CSS for the given URI.
     *
     * @param  string $uri
     *
     * @return string
     *
     * @throws mipotech\criticalcss\storage\CssReadException
     */
    public function readCss($uri);
    
    /**
     * Write generated critical-path CSS for a given URI for later use.
     *
     * @param  string $uri
     * @param  string $css
     *
     * @return bool
     *
     * @throws mipotech\criticalcss\storage\CssWriteException
     */
    public function writeCss($uri, $css);
    

}