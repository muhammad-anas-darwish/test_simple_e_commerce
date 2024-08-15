<?php

if (!function_exists('format_price')) {
    /**
     * Format the price with a currency symbol.
     *
     * @param float $amount
     * @param string $currencySymbol
     * @return string
     */
    function format_price($amount, $currencySymbol = '$')
    {
        return $currencySymbol . number_format($amount, 2);
    }
}

if (!function_exists('truncate_text')) {
    /**
     * Truncate the given text to a specified length and add ellipsis (…).
     *
     * @param string $text
     * @param int $maxLength
     * @param string $ellipsis
     * @return string
     */
    function truncate_text($text, $maxLength = 100, $ellipsis = '...')
    {
        if (strlen($text) <= $maxLength) {
            return $text;
        }

        return substr($text, 0, $maxLength) . $ellipsis;
    }
}

