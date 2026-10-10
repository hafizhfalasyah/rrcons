<?php

use Illuminate\Support\Facades\Request;

if (!function_exists('isMenuActive')) {
    function isMenuActive(array $routeNames): bool {
        foreach ($routeNames as $routeName) {
            if (request()->routeIs($routeName)) {
                return true;
            }
        }
        return false;
    }
}

if (!function_exists('highlightLastWord')) {
    /**
     * Bungkus kata terakhir judul dengan <span> berwarna. Seluruh teks di-escape
     * sehingga aman dicetak dengan {!! !!}.
     */
    function highlightLastWord(?string $text, string $class = 'text-blue-500'): string {
        $words = preg_split('/\s+/', trim((string) $text));

        if (empty($words) || $words === ['']) {
            return '';
        }

        $lastWord = array_pop($words);
        $firstPart = implode(' ', $words);

        return trim(e($firstPart) . ' <span class="' . e($class) . '">' . e($lastWord) . '</span>');
    }
}
