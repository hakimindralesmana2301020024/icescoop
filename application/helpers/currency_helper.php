<?php defined('BASEPATH') OR exit('No direct script access allowed');

if (!function_exists('format_rp')) {
    /**
     * Format number to Indonesian Rupiah style: "Rp 1.000"
     * Accepts numeric, formatted strings like "8.000", "16,000.00", or floats.
     *
     * @param mixed $amount
     * @return string
     */
    function format_rp($amount)
    {
        if ($amount === null || $amount === '') {
            return 'Rp 0';
        }

        // Normalize to string
        $str = (string)$amount;

        // Remove any characters except digits, dot, comma and minus
        $str = preg_replace('/[^0-9\.,\-]/', '', $str);

        // Handle cases with both dot and comma
        if (strpos($str, ',') !== false) {
            if (strpos($str, '.') !== false) {
                // assume dot is thousand separator and comma is decimal: "1.234,56" -> "1234.56"
                $str = str_replace('.', '', $str);
                $str = str_replace(',', '.', $str);
            } else {
                // only comma present -> assume decimal separator: "1234,56" -> "1234.56"
                $str = str_replace(',', '.', $str);
            }
        } else {
            // only dot or none present
            // if dot appears and is followed by exactly 3 digits at the end, treat as thousand separator and remove
            if (preg_match('/\.\d{3}$/', $str)) {
                $str = str_replace('.', '', $str);
            }
            // otherwise keep dot as decimal separator
        }

        // Convert to float then round to nearest integer (Rupiah is usually shown without cents)
        $num = (float) $str;
        $int = (int) round($num);

        return 'Rp ' . number_format($int, 0, ',', '.');
    }
}
