<?php

namespace paxxion\crafteternalslug\services;

use Craft;
use craft\base\Component;
use paxxion\crafteternalslug\EternalSlug;
use paxxion\crafteternalslug\records\RedirectRecord;
use Twig\Error\RuntimeError;
use yii\web\HttpException;

class RedirectService extends Component
{
    public static function handleException($event)
    {
        $exception = $event->exception;

        if ($exception instanceof RuntimeError) {
            $exception = $exception->getPrevious() ?? null;
        }
        
        if ($exception instanceof HttpException && $exception->statusCode !== 404) {
            return;
        }

        $request = Craft::$app->getRequest();

        if (!$request->isSiteRequest) {
            return;
        }

        $currentSite = Craft::$app->getSites()->getCurrentSite();
        
        $requestedUrl = $request->getAbsoluteUrl();
        $requestedUrl = parse_url($request->getAbsoluteUrl(), PHP_URL_PATH);
        $requestedUrl = '/' . trim($requestedUrl, '/');

        // redirect if exact match
        $redirect = RedirectRecord::find()
            ->where([
                'siteId' => $currentSite->id,
                'entryOldUrl' => $requestedUrl,
            ])
            ->one();

        // fallback to global redirect without site id check
        if (is_null($redirect)) {
            $redirect = RedirectRecord::find()
                ->where([
                    'entryOldUrl' => $requestedUrl,
                ])
                ->one();
        }

        // redirect
        if (!is_null($redirect)) {
            $redirect->totalHits = $redirect->totalHits + 1;
            $redirect->save();

            $site = Craft::$app->getSites()->getSiteById($redirect->siteId);
            $siteBaseUrl = trim($site->getBaseUrl(), '/') . '/';
            $redirectUrl = trim($redirect->entryNewUrl, '/');
            $redirectHttpCode = EternalSlug::getInstance()->settings->redirectHttpCode;
            
            Craft::$app->getResponse()->redirect($siteBaseUrl . $redirectUrl, $redirectHttpCode)->send();
            Craft::$app->end();
        }
    }
}
