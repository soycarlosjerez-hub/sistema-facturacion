<?php

if (!function_exists('renderMoneda')) {
    function renderMoneda($amount): string
    {
        return number_format((float) ($amount ?? 0), 2, '.', ',');
    }
}
