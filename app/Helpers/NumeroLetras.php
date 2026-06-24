<?php

if (!function_exists('num2letras')) {
    /**
     * Convierte número a letras en español (para PDFs de facturas)
     */
    function num2letras(float $numero): string
    {
        $entero  = intval($numero);
        $decimal = round(($numero - $entero) * 100);

        $letras  = convertirEntero($entero);
        $result  = trim($letras);

        if ($decimal > 0) {
            $result .= ' CON ' . str_pad($decimal, 2, '0', STR_PAD_LEFT) . '/100';
        } else {
            $result .= ' CON 00/100';
        }

        return $result;
    }
}

if (!function_exists('convertirEntero')) {
    function convertirEntero(int $n): string
    {
        if ($n === 0) return 'CERO';

        $unidades  = ['','UNO','DOS','TRES','CUATRO','CINCO','SEIS','SIETE','OCHO','NUEVE',
                       'DIEZ','ONCE','DOCE','TRECE','CATORCE','QUINCE','DIECISÉIS',
                       'DIECISIETE','DIECIOCHO','DIECINUEVE'];
        $decenas   = ['','','VEINTE','TREINTA','CUARENTA','CINCUENTA','SESENTA','SETENTA','OCHENTA','NOVENTA'];
        $centenas  = ['','CIENTO','DOSCIENTOS','TRESCIENTOS','CUATROCIENTOS','QUINIENTOS',
                       'SEISCIENTOS','SETECIENTOS','OCHOCIENTOS','NOVECIENTOS'];

        if ($n < 0)    return 'MENOS ' . convertirEntero(abs($n));
        if ($n < 20)   return $unidades[$n];
        if ($n < 100) {
            $d = intval($n / 10);
            $u = $n % 10;
            if ($n >= 21 && $n <= 29) return 'VEINTI' . strtolower($unidades[$u]);
            return $decenas[$d] . ($u ? ' Y ' . $unidades[$u] : '');
        }
        if ($n === 100) return 'CIEN';
        if ($n < 1000) {
            $c = intval($n / 100);
            $r = $n % 100;
            return $centenas[$c] . ($r ? ' ' . convertirEntero($r) : '');
        }
        if ($n < 2000) return 'MIL' . ($n % 1000 ? ' ' . convertirEntero($n % 1000) : '');
        if ($n < 1000000) {
            $miles = intval($n / 1000);
            $resto = $n % 1000;
            return convertirEntero($miles) . ' MIL' . ($resto ? ' ' . convertirEntero($resto) : '');
        }
        if ($n < 2000000) return 'UN MILLÓN' . ($n % 1000000 ? ' ' . convertirEntero($n % 1000000) : '');
        $mill = intval($n / 1000000);
        $rest = $n % 1000000;
        return convertirEntero($mill) . ' MILLONES' . ($rest ? ' ' . convertirEntero($rest) : '');
    }
}
