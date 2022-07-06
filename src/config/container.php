<?php

use Psr\Container\ContainerInterface;
use ZnLib\I18Next\Interfaces\Services\TranslationServiceInterface;
use ZnLib\I18Next\Services\TranslationService;

//$translationService = new TranslationService([], Yii::$app->language);
//$translationService = I18NextServiceFactory::create('ru', 'ru', $_ENV['I18NEXT_BUNDLES'] ?? []);

$defaultLang = 'ru';

return [
    'singletons' => [
        /*TranslationServiceInterface::class => function() {
            return I18NextServiceFactory::create('ru', 'ru', $_ENV['I18NEXT_BUNDLES'] ?? []);
        },*/
        TranslationServiceInterface::class => function (ContainerInterface $container) use($defaultLang) {
            /** @var TranslationServiceInterface $translationService */
            $translationService = $container->get(TranslationService::class);
            $translationService->setLanguage($defaultLang);

            /** @var \ZnCore\ConfigManager\Interfaces\ConfigManagerInterface $configManager */
            $configManager = $container->get(\ZnCore\ConfigManager\Interfaces\ConfigManagerInterface::class);
            $bundleConfig = $configManager->get('i18nextBundles', []);

            $translationService->setBundles($bundleConfig);
            $translationService->setDefaultLanguage($defaultLang);
//            \ZnLib\I18Next\Facades\I18Next::setService($translationService);
            return $translationService;
            //return I18NextServiceFactory::create('ru', 'ru', $_ENV['I18NEXT_BUNDLES'] ?? []);
        },
    ],
];

//config i18next bundles
//'symfony' => 'vendor/zncore/base/src/Libs/I18Next/SymfonyTranslation/i18next/__lng__/__ns__.json',
