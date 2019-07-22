<?php

namespace mipotech\criticalcss\commands;

use yii\base\Action;

class ClearAction extends Action
{
    /**
     * {@inheritdoc}
     */
    protected $name = 'criticalcss:clear';
    /**
     * {@inheritdoc}
     */
    protected $description = 'Clear critical-path CSS';
    /**
     * {@inheritdoc}
     */
    public function run()
    {
        parent::handle();
    }
    
}