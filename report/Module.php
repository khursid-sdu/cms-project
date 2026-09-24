<?php

namespace app\modules\report;

use yii\base\Module as BaseModule;

/**
 * Report Module
 *
 * Handles reporting functionalities such as Employee Info Report.
 */
class Module extends BaseModule
{
    /**
     * @var string Controller namespace for this module
     */
    public $controllerNamespace = 'app\modules\report\controllers';

    /**
     * @var string Default route within this module
     */
    public $defaultRoute = 'employee/empinforeport';

    /**
     * {@inheritdoc}
     */
    public function init()
    {
        parent::init();
    }
}
