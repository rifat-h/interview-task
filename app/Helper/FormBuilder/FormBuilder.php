<?php

namespace App\Helper\FormBuilder;

class FormBuilder
{
    private const TEXT_TYPE_INPUTS = ['text', 'number', 'email', 'password'];

    private static $inputBuilders = [
        'textarea' => 'buildTextareaInput',
        'file' => 'buildFileInput',
        'select' => 'buildSelectInput',
    ];

    public static function build(array $inputs): string
    {
        return implode('', array_map([self::class, 'buildInput'], $inputs));
    }

    public static function buildInput(array $input): string
    {
        $type = $input['type'];

        if (in_array($type, self::TEXT_TYPE_INPUTS, true)) {
            return self::buildTextTypeInput($input);
        }

        $builderMethod = self::$inputBuilders[$type] ?? null;
        
        if ($builderMethod) {
            return self::{$builderMethod}($input);
        }

        return '';
    }

    private static function buildTextTypeInput(array $input): string
    {
        return view('FormBuilder.textTypeInput', compact('input'))->render();
    }

    private static function buildTextareaInput(array $input): string
    {
        return view('FormBuilder.textareaInput', compact('input'))->render();
    }

    private static function buildSelectInput(array $input): string
    {
        return view('FormBuilder.selectInput', compact('input'))->render();
    }

    private static function buildFileInput(array $input): string
    {
        return view('FormBuilder.fileInput', compact('input'))->render();
    }
}