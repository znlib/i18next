<?php

namespace ZnLib\I18Next\Helpers;

use ZnCore\Arr\Helpers\ArrayHelper;

class TranslatorHelper
{

    public static function processVariables($template, array $attributes)
    {
        if (is_string($template)) {
            $attributes = self::prepareVariables($template, $attributes);
            foreach ($attributes as $variable => $value) {
                if (is_string($value) || is_numeric($value)) {
                    $template = preg_replace('/__' . $variable . '__/', $value, $template);
                    $template = preg_replace('/{{' . $variable . '}}/', $value, $template);
                }
            }
        }

        return $template;
    }

    private static function prepareVariables(string $template, array $attributes): array
    {
        if ($attributes) {
            $attributesFromTemplate = self::parseAttributes($template);
            foreach ($attributesFromTemplate as $varName) {
                $attributes[$varName] = ArrayHelper::getValue($attributes, $varName);
            }
        }
        return $attributes;
    }

    private static function parseAttributes(string $template): array
    {
        preg_match_all('/{{([\w\d\.-]+)}}/i', $template, $matches);
        return $matches[1] ?? [];
    }
}
