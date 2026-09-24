<?php

namespace app\modules\academics;

use yii\base\Module as BaseModule;

/**
 * Academics Module
 *
 * Handles Tabulation Register (TR) generation status reporting,
 * cascading filters, KPI metrics, and Excel (.xlsx) data export.
 */
class Module extends BaseModule
{
    /**
     * @var string Controller namespace for this module
     */
    public $controllerNamespace = 'app\modules\academics\controllers';

    /**
     * @var string Default route within this module
     */
    public $defaultRoute = 'report/index';

    /**
     * @var array Controller map for supporting legacy routes
     */
    public $controllerMap = [
        'tr-report' => 'app\modules\academics\controllers\ReportController',
        'report'    => 'app\modules\academics\controllers\ReportController',
    ];

    /**
     * {@inheritdoc}
     */
    public function init()
    {
        parent::init();
    }
}

