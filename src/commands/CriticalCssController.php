<?php

namespace mipotech\criticalcss\commands;

use Yii;
use yii\console\Controller;
use yii\helpers\FileHelper;

use mipotech\criticalcss\storage\Storage;
use mipotech\criticalcss\htmlFetchers\HtmlFetcher;
use mipotech\criticalcss\cssGenerators\CriticalGenerator;

class CriticalCssController extends Controller
{
    /** @var \mipotech\criticalcss\storage\StorageInterface */
    protected $storage;

    /**
     * {@inheritdoc}
     */
    public function handle()
    {
        $this->setupStorage();
        $this->removeOldCss();
    }
    /**
     * Set up the storage.
     *
     * @return void
     */
    protected function setupStorage()
    {
        $this->storage = Yi::$app->criticalCss->storage;
        $this->storage->validateStoragePath();
    }
    /**
     * Remove old critical-path CSS.
     *
     * @return void
     */
    protected function removeOldCss()
    {
        $this->info('Removing old critical-path CSS.');
        $this->storage->clearCss();
    }
    
    public function actionMake()
    {
        $htmlFetcher = new HtmlFetcher;
        $storage = new Storage(['storage' => getcwd() . '/public_html/css/criticalCss']);
        $cssGenerator = new CriticalGenerator(['htmlFetcher' => $htmlFetcher, 'storage' => $storage, 'width' => 375, 'height' => 812]);
        foreach ($this->getUris() as $key => $uri) {
            Yii::info(sprintf('Processing URI [%s]', $uri['uri']), 'criticalCss');
            Yii::info('shalom', 'criticalCss');
            $cssGenerator->generate($uri, $this->getUriAlias($key));
        }
        //$this->clearViews();
    }
    
    /**
     * Returns the alias for a URI, if there is any. If not, returns null.
     *
     * @param  string|int $key The key for the given array item.
     *
     * @return string|null
     */
    protected function getUriAlias($key)
    {
        // If the key is a string, assume it's specified by the user, and
        // therefore is an alias.
        if (is_string($key)) {
            return $key;
        }
        // If not, return null.
        return null;
    }
    
    /**
     * Returns a list of URIs to generate critical-path CSS for.
     *
     * @return array
     */
    protected function getUris()
    {
        if (is_callable(Yii::$app->criticalCss->routes)) {
           return call_user_func(Yii::$app->criticalCss->routes);
        } else {
           return Yii::$app->criticalCss->routes;
        }
    }
    
    /**
     * Clear compiled views.
     *
     * @return void
     */
    /*protected function clearViews()
    {
        $this->info('Clearing compiled views');
        try {
            Artisan::call('view:clear');
        } catch (InvalidArgumentException $e) {
            $views = $this->laravel['files']->glob(
                $this->laravel['config']['view.compiled'].'/*'
            );
            foreach ($views as $view) {
                $this->laravel['files']->delete($view);
            }
        }
    }*/
    
}

