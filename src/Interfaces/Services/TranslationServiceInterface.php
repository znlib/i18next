<?php

namespace ZnLib\I18Next\Interfaces\Services;

interface TranslationServiceInterface
{

    public function setBundles(array $bundles): void;

    public function addBundle(string $bundleName, $loaderDefinition);
    
    public function getLanguage(): string;

    public function setLanguage(string $language, string $fallback = null);

    public function getDefaultLanguage(): string;

    public function setDefaultLanguage(string $defaultLanguage): void;
    
    public function t(string $bundleName, string $key, array $variables = []);
    
}
