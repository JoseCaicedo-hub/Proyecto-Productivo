<?php

namespace App\Helpers;

/**
 * PriceHelper - Manejo estandarizado de precios en COP (Pesos Colombianos)
 * 
 * Todos los precios se guardan como enteros (sin decimales) en la BD.
 * Ejemplo: 25000 (equivalente a $25.000 COP)
 */
class PriceHelper
{
    /**
     * Limpia y normaliza un precio ingresado por el usuario
     * 
     * Acepta formatos:
     * - "1000" → 1000
     * - "1.000" → 1000
     * - "1,000" → 1000
     * - 1000 → 1000
     * 
     * @param mixed $price Valor a limpiar
     * @return int|null Precio como entero, o null si es vacío
     */
    public static function cleanPrice($price): ?int
    {
        // Si es null o vacío, retornar null
        if ($price === null || $price === '' || $price === '0') {
            return null;
        }

        // Convertir a string si es número
        $price = (string) $price;

        // Remover espacios
        $price = trim($price);

        // Remover símbolo $ si lo tiene
        $price = str_replace('$', '', $price);

        // Remover espacios de nuevo
        $price = trim($price);

        // Remover puntos (separadores de miles) y comas
        // Aceptar ambos formatos: 1.000 o 1,000
        $price = str_replace(['.', ','], '', $price);

        // Convertir a integer
        $intPrice = (int) $price;

        // Validar que sea positivo
        if ($intPrice <= 0) {
            return null;
        }

        return $intPrice;
    }

    /**
     * Formatea un precio para visualización en COP
     * 
     * Ejemplos:
     * - 1000 → $1.000
     * - 25000 → $25.000
     * - 1500000 → $1.500.000
     * 
     * @param int|float $price Precio a formatear
     * @param bool $showSymbol Si mostrar símbolo $ al inicio (default: true)
     * @return string Precio formateado
     */
    public static function formatCOP($price, bool $showSymbol = true): string
    {
        // Convertir a entero si es necesario
        $price = (int) $price;

        // Formatea con separador de miles (punto en Colombia)
        $formatted = number_format($price, 0, ',', '.');

        // Agregar símbolo si es requerido
        if ($showSymbol) {
            return '$' . $formatted;
        }

        return $formatted;
    }

    /**
     * Formatea precio sin símbolo (solo número con separadores)
     * 
     * @param int|float $price Precio a formatear
     * @return string Precio como "1.250.000"
     */
    public static function formatCOPWithoutSymbol($price): string
    {
        return self::formatCOP($price, false);
    }

    /**
     * Valida si un precio es válido
     * 
     * @param mixed $price Precio a validar
     * @return bool True si es válido, false sino
     */
    public static function isValidPrice($price): bool
    {
        return self::cleanPrice($price) !== null;
    }

    /**
     * Obtiene el precio limpio y validado, o un valor por defecto
     * 
     * @param mixed $price Precio a procesar
     * @param int $default Valor por defecto si es inválido (default: 0)
     * @return int Precio válido o default
     */
    public static function getPriceOrDefault($price, int $default = 0): int
    {
        return self::cleanPrice($price) ?? $default;
    }
}
