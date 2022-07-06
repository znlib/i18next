<?php

namespace ZnCore\Base\Tests\Unit;

use ZnCore\Instance\Helpers\ClassHelper;
use ZnLib\I18Next\Interfaces\Services\TranslationServiceInterface;
use ZnLib\I18Next\Services\TranslationService;
use PHPUnit\Framework\TestCase;

final class I18NextTest extends TestCase
{

    private $service;

    public function testBasics()
    {
        $service = $this->getService();

        $this->assertEquals('en', $service->getLanguage());

        $this->assertSame('dog', $service->t('test', 'animal.dog'));
        $this->assertSame('A friend', $service->t('test', 'friend'));
        $this->assertSame('1 cat', $service->t('test', 'animal.catWithCount', ['count' => 1]));
    }

    public function testPlural()
    {
        $service = $this->getService();

        // Simple plural
        $this->assertSame('dogs', $service->t('test', 'animal.dog', ['count' => 2]));
    }

    public function testModifiers()
    {
        $service = $this->getService();

        // Plural with language override
        $this->assertSame('koiraa', $service->t('test', 'animal.dog', ['count' => 2, 'lng' => 'fi']));

        // Context
        $this->assertSame('A girlfriend', $service->t('test', 'friend', ['context' => 'female']));

        // Context with plural
        $this->assertSame('100 girlfriends', $service->t('test', 'friend', ['context' => 'female', 'count' => 100]));

       // dd($service->t('test', 'animal.thedoglovers'));

        $thedoglovers = [
            "The Dog Lovers by Spike Milligan",
            "So they bought you",
            "And kept you in a",
            "Very good home",
            "Cental heating",
            "TV",
            "A deep freeze",
            "A very good home-",
            "No one to take you",
            "For that lovely long run-",
            "But otherwise",
            "'A very good home'",
            "They fed you Pal and Chun",
            "But not that lovely long run,",
            "Until, mad with energy and boredom",
            "You escaped- and ran and ran and ran",
            "Under a car.",
            "Today they will cry for you-",
            "Tomorrow they will but another dog.",
        ];

        // Multiline object
        $this->assertSame($thedoglovers, $service->t('test', 'animal.thedoglovers', ['returnObjectTrees' => true]));

        // Multiline Text
        $this->assertSame(implode("\n", $thedoglovers), $service->t('test', 'animal.thedoglovers'));
    }

    private function getService(): TranslationServiceInterface
    {
        $translationService = ClassHelper::createObject(TranslationService::class);
        $translationService->setLanguage('en');
        $translationService->setBundles([
            'test' => 'vendor/znlib/i18next/tests/example/',
        ]);
        return $translationService;
    }
}
