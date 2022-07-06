<?php

namespace ZnLib\I18Next\Libs\TranslationLoaders;

use ZnCore\FileSystem\Helpers\FilePathHelper;
use ZnLib\I18Next\Interfaces\TranslationLoaders\TranslationLoaderInterface;

class JsonLoader implements TranslationLoaderInterface
{

    private $pathMask;

    public function getPathMask(): string
    {
        return $this->pathMask;
    }

    public function setPathMask(string $pathMask): void
    {
        $this->pathMask = $pathMask;
    }

    public function load(string $language): array
    {
        $translationResult = [];
        $pathMask = $this->forgePath($this->pathMask);
        $pathMask = $this->normalizePath($pathMask);
        $path = preg_replace('/__(.+?)__/', '*', $pathMask, 2, $hasNs);
        $dir = $this->scan($path);
        foreach ($dir as $file) {
            $translationData = $this->loadFromFile($file);
            if ($hasNs) {
                list($lng, $ns) = $this->matchPath($pathMask, $file);
                if (empty($lng)) {
                    $lng = $language;
                }
                $this->mergeTranslations($lng, $ns, $translationData, $translationResult);
            } else {
                if (array_key_exists($language, $translationData)) {
                    $translationResult = $translationData;
                } else {
                    $translationResult = array_merge($translationResult, $translationData);
                }
            }
        }
        return $translationResult;
    }

    private function mergeTranslations(string $lng, string $ns, array $translationData, array &$translationResult)
    {
        if (!empty($ns)) {
            if (array_key_exists($lng, $translationResult) && array_key_exists($ns, $translationResult[$lng])) {
                $translationResult[$lng][$ns] = array_merge($translationResult[$lng][$ns], [$ns => $translationData]);
            } elseif (array_key_exists($lng, $translationResult)) {
                $translationResult[$lng] = array_merge($translationResult[$lng], [$ns => $translationData]);
            } else {
                $translationResult[$lng] = [$ns => $translationData];
            }
        } else {
            if (array_key_exists($lng, $translationResult)) {
                $translationResult[$lng] = array_merge($translationResult[$lng], $translationData);
            } else {
                $translationResult[$lng] = $translationData;
            }
        }
    }

    private function scan(string $path)
    {
        $path = $this->normalizePath($path);
        $dir = glob($path);
        if (count($dir) === 0) {
            throw new \Exception('Translation file not found in "' . $path . '"');
        }
        return $dir;
    }

    private function normalizePath(string $path): string
    {
        if (!preg_match('/\.json$/', $path)) {
            $path = $path . 'translation.json';
        }
        return $path;
    }

    private function forgePath(string $bundlePath): string
    {
        $rootDir = FilePathHelper::rootPath();
        $rootDir = rtrim($rootDir, '/');
        $bundlePath = ltrim($bundlePath, '/');
        $fileMask = "$rootDir/$bundlePath";
        return $fileMask;
    }

    private function loadFromFile(string $file): array
    {
        $translationJson = file_get_contents($file);
        $translationData = json_decode($translationJson, true);
        if ($translationData === null) {
            throw new \Exception('Invalid json ' . $file);
        }
        return $translationData;
    }

    private function matchPath(string $pathMask, string $file): array
    {
        $regexp = preg_replace('/__(.+?)__/', '(?<$1>.+)?', preg_quote($pathMask, '/'));
        preg_match('/^' . $regexp . '$/', $file, $ns);
        return [
            $ns['lng'] ?? null,
            $ns['ns'] ?? null,
        ];
    }
}
