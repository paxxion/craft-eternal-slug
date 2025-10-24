<?php

namespace paxxion\crafteternalslug\controllers\cp;

use craft\web\Controller;
use paxxion\crafteternalslug\records\RedirectRecord;
use paxxion\crafteternalslug\services\ExportService;

class ExportController extends Controller
{
    public $defaultAction = 'index';
    protected array|int|bool $allowAnonymous = self::ALLOW_ANONYMOUS_LIVE;

    public function actionIndex()
    {
        $rows = RedirectRecord::find()->all();

        ExportService::export($rows);
    }
}
