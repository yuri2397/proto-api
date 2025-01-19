<?php

if (!function_exists('format_currency')) {
    function format_currency($amount, $currency = 'F CFA')
    {
        // Vérifie si le montant est numérique
        if (!is_numeric($amount)) {
            return $amount; // Retourne tel quel si ce n'est pas un nombre
        }

        // Formate le montant avec des espaces comme séparateurs de milliers
        $formatted = number_format($amount, 0, '', ' ');

        // Ajoute la devise
        return $formatted . ' ' . $currency;
    }
}
