<?php


namespace mipotech\criticalcss\storage;

use yii\base\Component;
use yii\helpers\FileHelper;

/**
 * Read and write to the filesystem using the FileHelper in Yii2.
 */
class Storage extends Component
{
    /** @var string */
    public $storage;
    
    /** @var bool */
    public $pretend = false;
   
    
    /**
     * Validate that the storage directory exists. If it does not, create it.
     *
     * @return bool
     */
    public function validateStoragePath()
    {
        if (!file_exists($this->storage)) {
            return FileHelper::createDirectory($this->storage);
        }
        return true;
    }
    /**
     * {@inheritdoc}
     */
    public function readCss($uri)
    {
        $path = sprintf('%s/%s.css', $this->storage, urlencode($uri));
        if (!file_exists($path)) {
            return sprintf(
                '/* Critical-path CSS for URI [%s] not found at [%s]. '.
                'Check the config and run `php artisan criticalcss:make`. */',
                $uri,
                $path
            );
        }
        return file_get_contents($path);
    }
    /**
     * Wrap the critical-path CSS inside a '<style>' HTML element and return
     * the HTML.
     *
     * @param  string $uri
     *
     * @return string
     */
    public function css($uri)
    {
        if ($this->pretend) {
            return '';
        }
        return '<style data-inlined>'.$this->readCss($uri).'</style>';
    }
    /**
     * {@inheritdoc}
     */
    public function writeCss($uri, $css)
    {
        $ok = file_put_contents(
            $this->storage.'/'.urlencode($uri . '/').'.css',
            $css
        );
        if (!$ok) {
            throw new CssWriteException(
                sprintf(
                    'Unable to write the critical-path CSS for the URI [%s] to [%s].',
                    $uri,
                    $css
                )
            );
        }
        return $ok;
    }
    /**
     * Clear the storage.
     *
     * @return bool
     */
    public function clearCss()
    {
        return FileHelper::removeDirectory($this->storage, true);
    }
}