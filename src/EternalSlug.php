<?php

namespace paxxion\crafteternalslug;

use Craft;
use craft\base\Event;
use craft\base\Model;
use craft\base\Plugin;
use craft\events\ElementEvent;
use craft\events\ExceptionEvent;
use craft\events\DefineFieldLayoutElementsEvent;
use craft\events\RegisterUrlRulesEvent;
use craft\models\FieldLayout;
use craft\services\Elements;
use craft\web\ErrorHandler;
use craft\web\UrlManager;
use paxxion\crafteternalslug\elements\UrlHistoryElement;
use paxxion\crafteternalslug\models\Settings;
use paxxion\crafteternalslug\services\RedirectService;
use paxxion\crafteternalslug\services\StashService;

/**
 * Eternal Slug plugin
 *
 * @method static EternalSlug getInstance()
 * @method Settings getSettings()
 * @author Paxxion <web@paxxion.com>
 * @copyright Paxxion
 * @license https://craftcms.github.io/license/ Craft License
 */
class EternalSlug extends Plugin
{
    public string $schemaVersion = '1.0.2';
    public bool $hasCpSettings = true;

    public function init(): void
    {
        parent::init();
        
        Event::on(
            Elements::class,
            Elements::EVENT_BEFORE_SAVE_ELEMENT,
            static function(ElementEvent $event): void {
                StashService::stash($event);
            }
        );

        Event::on(
            Elements::class,
            Elements::EVENT_AFTER_SAVE_ELEMENT,
            static function(ElementEvent $event): void {
                StashService::save($event);
            }
        );

        Event::on(
            Elements::class,
            Elements::EVENT_BEFORE_UPDATE_SLUG_AND_URI,
            static function(ElementEvent $event): void {
                StashService::stash($event);
            }
        );

        Event::on(
            Elements::class,
            Elements::EVENT_AFTER_UPDATE_SLUG_AND_URI,
            static function(ElementEvent $event): void {
                StashService::save($event);
            }
        );

        Event::on(
            FieldLayout::class,
            FieldLayout::EVENT_DEFINE_UI_ELEMENTS,
            function(DefineFieldLayoutElementsEvent $event) {
                $event->elements[] = UrlHistoryElement::class;
            }
        );

        Event::on(
            ErrorHandler::class,
            ErrorHandler::EVENT_BEFORE_HANDLE_EXCEPTION,
            static function(ExceptionEvent $event): void {
                RedirectService::handleException($event);
            }
        );

        Event::on(
            UrlManager::class,
            UrlManager::EVENT_REGISTER_CP_URL_RULES,
            function(RegisterUrlRulesEvent $event) {
                $event->rules['eternal-slug/export'] = [ 'route' => 'eternal-slug/cp/export'];
            }
        );
    }

    protected function createSettingsModel(): ?Model
    {
        return Craft::createObject(Settings::class);
    }

    protected function settingsHtml(): ?string
    {
        return Craft::$app->view->renderTemplate('eternal-slug/_settings.twig', [
            'plugin' => $this,
            'settings' => $this->getSettings(),
        ]);
    }
}
