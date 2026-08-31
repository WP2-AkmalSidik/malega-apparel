<?php

namespace App\Services\Barcode;

class Code128BarcodeGenerator
{
    /**
     * Standard Code 128 patterns (ISO/IEC 15417).
     * 107 symbols from 0 to 106.
     * Each symbol is an array of 6 integers representing bar and space widths (total 11 modules),
     * except stop code (106) which has 7 integers (total 13 modules).
     *
     * @var array<int, string>
     */
    protected static array $patterns = [
        '212222', '222122', '222221', '121223', '121322', '131222', '122213', '122312', '132212', '221213', // 0-9
        '221312', '231212', '112232', '122132', '122231', '113222', '123122', '123221', '223211', '221132', // 10-19
        '221231', '213212', '223112', '312131', '311222', '321122', '321221', '312212', '322112', '322211', // 20-29
        '212123', '212321', '232121', '111323', '131123', '131321', '112313', '132113', '132311', '211313', // 30-39
        '231113', '231311', '112133', '112331', '132131', '113123', '113321', '133121', '313121', '211331', // 40-49
        '231131', '213113', '213311', '213131', '311123', '311321', '331121', '312113', '312311', '332111', // 50-59
        '314111', '221411', '431111', '111224', '111422', '121124', '121421', '141122', '141221', '112214', // 60-69
        '112412', '122114', '122411', '142112', '142211', '241211', '221114', '413111', '241112', '134111', // 70-79
        '111242', '121142', '121241', '114212', '124112', '124211', '411212', '421112', '421211', '212141', // 80-89
        '214121', '412121', '111143', '111341', '131141', '114113', '114311', '411113', '411311', '113141', // 90-99
        '114131', '311141', '411131', '211412', '211214', '211232', '2331112'                                 // 100-106 (104=StartB, 106=Stop)
    ];

    /**
     * Generate 100% Real Code 128 (Set B) SVG string for any input text.
     */
    public static function generateSvg(string $text, int $height = 42, int $moduleWidth = 2): string
    {
        $text = trim($text);
        if (empty($text)) {
            $text = 'MAL-0000';
        }

        // Start with Code Set B (Index 104)
        $symbols = [104];
        $checksum = 104;

        $length = strlen($text);
        for ($i = 0; $i < $length; $i++) {
            $ascii = ord($text[$i]);
            // In Code 128 Set B, character value = ASCII - 32 (for ASCII 32 to 127)
            $val = $ascii - 32;
            if ($val < 0 || $val > 95) {
                $val = 0; // Fallback space
            }
            $symbols[] = $val;
            $checksum += ($val * ($i + 1));
        }

        // Calculate Checksum Modulo 103
        $checkSymbol = $checksum % 103;
        $symbols[] = $checkSymbol;

        // Add Stop Code (Index 106)
        $symbols[] = 106;

        // Convert symbols to bars and spaces string
        $svgElements = '';
        $currentX = 0;

        foreach ($symbols as $symIndex) {
            $pattern = self::$patterns[$symIndex] ?? '212222';
            $isBar = true;

            $patLen = strlen($pattern);
            for ($p = 0; $p < $patLen; $p++) {
                $width = (int) $pattern[$p] * $moduleWidth;
                if ($isBar) {
                    $svgElements .= "<rect x=\"{$currentX}\" y=\"0\" width=\"{$width}\" height=\"{$height}\" fill=\"#000000\" />\n";
                }
                $currentX += $width;
                $isBar = ! $isBar;
            }
        }

        $totalWidth = $currentX;

        return "<svg width=\"{$totalWidth}\" height=\"{$height}\" viewBox=\"0 0 {$totalWidth} {$height}\" xmlns=\"http://www.w3.org/2000/svg\" style=\"display: block; margin: 0 auto; max-width: 100%; height: auto;\">\n{$svgElements}</svg>";
    }
}
