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
