<?php

namespace App\Http\Helper;

class NumberToWordsConverter
{
    private static array $unitsMasculine = [
        '', 'واحد', 'اثنان', 'ثلاثة', 'أربعة', 'خمسة', 'ستة', 'سبعة', 'ثمانية', 'تسعة',
        'عشرة', 'أحد عشر', 'اثنا عشر', 'ثلاثة عشر', 'أربعة عشر', 'خمسة عشر', 'ستة عشر',
        'سبعة عشر', 'ثمانية عشر', 'تسعة عشر'
    ];

    private static array $unitsFeminine = [
        '', 'واحدة', 'اثنتان', 'ثلاث', 'أربع', 'خمس', 'ست', 'سبع', 'ثمان', 'تسع',
        'عشر', 'إحدى عشرة', 'اثنتا عشرة', 'ثلاث عشرة', 'أربع عشرة', 'خمس عشرة', 'ست عشرة',
        'سبع عشرة', 'ثماني عشرة', 'تسع عشرة'
    ];

    private static array $tens = [
        '', '', 'عشرون', 'ثلاثون', 'أربعون', 'خمسون', 'ستون', 'سبعون', 'ثمانون', 'تسعون'
    ];

    private static array $hundreds = [
        '', 'مائة', 'مائتان', 'ثلاثمائة', 'أربعمائة', 'خمسمائة', 'ستمائة', 'سبعمائة', 'ثمانمائة', 'تسعمائة'
    ];

    private static array $thousandsMasculineSingular = ['ألف'];
    private static array $thousandsMasculineDual = ['ألفان'];
    private static array $thousandsMasculinePlural = ['آلاف'];

    // Add more for millions, billions, etc. as needed

    public static function toArabicWordsWithCurrency(float $number, string $currencyNameMasculineSingular, string $currencyNameFeminineSingular, string $currencyNamePlural): string
    {
        $integerPart = (int) $number;
        $decimalPart = round(($number - $integerPart) * 100);

        $integerWords = self::convertIntegerWithCurrency($integerPart, $currencyNameMasculineSingular, $currencyNameFeminineSingular, $currencyNamePlural);
        $decimalWords = '';

        if ($decimalPart > 0) {
            $decimalWords = ' فاصلة ' . self::convertInteger($decimalPart); // Basic decimal conversion
        }

        return trim($integerWords . ' ' . $decimalWords);
    }

    private static function convertIntegerWithCurrency(int $number, string $currencyNameMasculineSingular, string $currencyNameFeminineSingular, string $currencyNamePlural): string
    {
        if ($number === 0) {
            return 'صفر ' . $currencyNameMasculineSingular; // Adjust as needed
        }

        if ($number === 1) {
            return 'واحد ' . $currencyNameMasculineSingular;
        }

        if ($number === 2) {
            return 'اثنان ' . $currencyNameMasculineDual; // Assuming dual form exists for the currency
        }

        if ($number >= 3 && $number <= 10) {
            return self::convertGroup($number, false) . ' ' . $currencyNamePlural; // Feminine form for 3-10 with masculine plural
        }

        if ($number >= 11 && $number <= 19) {
            return self::$unitsMasculine[$number] . ' ' . $currencyNameMasculineSingular; // Adjust gender if needed
        }

        if ($number >= 20 && $number <= 99) {
            return self::convertTwoDigits($number, true) . ' ' . $currencyNameMasculineSingular; // Adjust gender if needed
        }

        if ($number >= 100 && $number <= 999) {
            return self::convertHundreds($number, true) . ' ' . $currencyNameMasculineSingular; // Adjust gender if needed
        }

        if ($number >= 1000 && $number <= 9999) {
            $thousands = (int) ($number / 1000);
            $remainder = $number % 1000;
            $thousandsWord = self::convertThousands($thousands, true); // Basic thousands with masculine
            $remainderWord = $remainder > 0 ? ' و' . self::convertInteger($remainder) : '';
            return $thousandsWord . ' ' . $currencyNamePlural . $remainderWord; // Plural for thousands range
        }

        // Add more logic for larger numbers and their currency forms

        return self::convertInteger($number) . ' ' . $currencyNameMasculineSingular; // Default fallback
    }

    private static function convertGroup(int $number, bool $isMasculine): string
    {
        if ($number === 0) {
            return '';
        }
        if ($number < 20) {
            return $isMasculine ? self::$unitsMasculine[$number] : self::$unitsFeminine[$number];
        }
        $unit = $number % 10;
        $ten = (int) ($number / 10);
        $unitWord = $unit > 0 ? ( $isMasculine ? self::$unitsMasculine[$unit] : self::$unitsFeminine[$unit] ) . ' و' : '';
        return $unitWord . self::$tens[$ten];
    }

    private static function convertTwoDigits(int $number, bool $isMasculine): string
    {
        if ($number < 20) {
            return self::convertGroup($number, $isMasculine);
        }
        $unit = $number % 10;
        $ten = (int) ($number / 10);
        $unitWord = $unit > 0 ? ( $isMasculine ? self::$unitsMasculine[$unit] : self::$unitsFeminine[$unit] ) . ' و' : '';
        return $unitWord . self::$tens[$ten];
    }

    private static function convertHundreds(int $number, bool $isMasculine): string
    {
        $hundreds = (int) ($number / 100);
        $remainder = $number % 100;
        $hundredsWord = self::$hundreds[$hundreds];
        $remainderWord = $remainder > 0 ? ' و' . self::convertTwoDigits($remainder, $isMasculine) : '';
        return $hundredsWord . $remainderWord;
    }

    private static function convertThousands(int $number, bool $isMasculine): string
    {
        if ($number === 1) {
            return self::$thousandsMasculineSingular;
        }
        if ($number === 2) {
            return self::$thousandsMasculineDual;
        }
        if ($number >= 3 && $number <= 10) {
            return self::convertGroup($number, false) . ' ' . self::$thousandsMasculinePlural; // Feminine number with masculine plural
        }
        if ($number > 10) {
            return self::convertInteger($number) . ' ' . self::$thousandsMasculinePlural; // Basic for now
        }
        return '';
    }

    private static function convertInteger(int $number): string
    {
        // ... (same logic as the previous convertInteger function) ...
        if ($number === 0) {
            return 'صفر';
        }

        if ($number < 20) {
            return self::$unitsMasculine[$number]; // Default to masculine for standalone numbers
        }

        if ($number < 100) {
            return self::convertTwoDigits($number, true);
        }

        if ($number < 1000) {
            return self::convertHundreds($number, true);
        }

        if ($number < 100000) {
            $thousands = (int) ($number / 1000);
            $remainder = $number % 1000;
            $thousandsWord = self::convertThousands($thousands, true);
            $remainderWord = $remainder > 0 ? ' و' . self::convertInteger($remainder) : '';
            return $thousandsWord . ' ألف' . $remainderWord;
        }

        return 'رقم كبير جدا';
    }
}