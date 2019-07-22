<?php

namespace mipotech\criticalcss\commands;

use yii\base\Action;

class MakeAction extends Action
{
    /**
     * {@inheritdoc}
     */
    public function run()
    {
        parent::run();
        $cssGenerator = Yi::$app->criticalCss->generator;
        foreach ($this->getUris() as $key => $uri) {
            $this->info(sprintf('Processing URI [%s]', $uri));
            $cssGenerator->generate($uri, $this->getUriAlias($key));
        }
        $this->clearViews();
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
