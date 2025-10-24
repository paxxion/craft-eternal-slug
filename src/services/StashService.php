<?php

namespace paxxion\crafteternalslug\services;

use Craft;
use craft\base\Component;
use craft\elements\Entry;
use craft\events\ElementEvent;
use craft\helpers\ElementHelper;
use paxxion\crafteternalslug\records\HistoryRecord;
use paxxion\crafteternalslug\records\RedirectRecord;
use paxxion\crafteternalslug\records\StashRecord;

class StashService extends Component
{
    public static function stash(ElementEvent $event)
    {
        $element = $event->element;
        if (!$element instanceof Entry) return;
        if (ElementHelper::isDraftOrRevision($element) || $element->propagating) return;
        
        $allUrls = self::getAllUrls($element);
        foreach ($allUrls as $siteId => $urls) {
            $stash = StashRecord::find()
                ->where([
                    'siteId' => $siteId,
                    'entryId' => $element->id,
                ])
                ->one();
            
            if (is_null($stash)) {
                $stash = new StashRecord();
                $stash->siteId = $siteId;
                $stash->entryId = $element->id;
            }

            $stash->entryOldUrl = $urls['path'];
            $stash->save();
        }
    }

    public static function save(ElementEvent $event)
    {
        $element = $event->element;
        if (!$element instanceof Entry) return;
        if (ElementHelper::isDraftOrRevision($element) || $element->propagating) return;

        $allNewUrls = self::getAllUrls($element);
        foreach ($allNewUrls as $siteId => $newUrls) {
            $stash = StashRecord::find()
                ->where([
                    'siteId' => $siteId,
                    'entryId' => $element->id,
                ])
                ->one();
        
            // continue only if new url changed
            if ($stash && $stash->entryOldUrl !== $newUrls['path']) {
                // create or update redirect
                $redirect = RedirectRecord::find()
                    ->where([
                        'siteId' => $siteId,
                        'entryId' => $element->id,
                        'entryOldUrl' => $stash->entryOldUrl,
                    ])
                    ->one();

                if (is_null($redirect)) {
                    $redirect = new RedirectRecord();
                    $redirect->siteId = $siteId;
                    $redirect->entryId = $element->id;
                    $redirect->entryOldUrl = $stash->entryOldUrl;
                }

                $redirect->entryNewUrl = $newUrls['uri'];
                $redirect->save();

                // update every redirects for this entry so it points to new url (to avoid cascading redirects)
                RedirectRecord::updateAll(
                    ['entryNewUrl' => $newUrls['uri']],
                    [
                        'siteId' => $siteId,
                        'entryId' => $element->id,
                    ]
                );

                // save previous url in history
                $history = new HistoryRecord();
                $history->siteId = $siteId;
                $history->entryId = $element->id;
                $history->entryUrl = $stash->entryOldUrl;
                $history->save();
            }
        }
    }

    private static function getAllUrls($element)
    {
        $urls = [];

        $sites = Craft::$app->getSites()->getAllSites();
        foreach ($sites as $site) {
            $entry = Entry::find()->id($element->id)->siteId($site->id)->one();
            if ($entry) {
                $url = $entry->url;
                $path = parse_url($url, PHP_URL_PATH);
                $path = '/' . trim($path, '/');

                $uri = trim($entry->uri, '/');

                $urls[$site->id] = [
                    'path' => $path,
                    'uri' => $uri,
                ];
            }
        }

        return $urls;
    }
}
