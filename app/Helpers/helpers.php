<?php

if (!function_exists('num2letras')) {
    function num2letras(float $numero): string
    {
        $entero  = intval($numero);
        $decimal = round(($numero - $entero) * 100);
        $letras  = convertirEntero($entero);
        $dec     = $decimal > 0 ? ' CON '.str_pad($decimal,2,'0',STR_PAD_LEFT).'/100' : ' CON 00/100';
        return trim($letras).$dec;
    }
}

if (!function_exists('convertirEntero')) {
    function convertirEntero(int $n): string
    {
        if ($n === 0) return 'CERO';
        $u = ['','UNO','DOS','TRES','CUATRO','CINCO','SEIS','SIETE','OCHO','NUEVE',
              'DIEZ','ONCE','DOCE','TRECE','CATORCE','QUINCE','DIECISÉIS','DIECISIETE','DIECIOCHO','DIECINUEVE'];
        $d = ['','','VEINTE','TREINTA','CUARENTA','CINCUENTA','SESENTA','SETENTA','OCHENTA','NOVENTA'];
        $c = ['','CIENTO','DOSCIENTOS','TRESCIENTOS','CUATROCIENTOS','QUINIENTOS',
              'SEISCIENTOS','SETECIENTOS','OCHOCIENTOS','NOVECIENTOS'];
        if ($n < 0)    return 'MENOS '.convertirEntero(abs($n));
        if ($n < 20)   return $u[$n];
        if ($n < 30)   return 'VEINTI'.strtolower($u[$n%10]);
        if ($n < 100)  return $d[intval($n/10)].($n%10?' Y '.$u[$n%10]:'');
        if ($n === 100) return 'CIEN';
        if ($n < 1000) return $c[intval($n/100)].($n%100?' '.convertirEntero($n%100):'');
        if ($n < 2000) return 'MIL'.($n%1000?' '.convertirEntero($n%1000):'');
        if ($n < 1000000) {
            $m=$n%1000; return convertirEntero(intval($n/1000)).' MIL'.($m?' '.convertirEntero($m):'');
        }
        if ($n < 2000000) return 'UN MILLÓN'.($n%1000000?' '.convertirEntero($n%1000000):'');
        $m=$n%1000000; return convertirEntero(intval($n/1000000)).' MILLONES'.($m?' '.convertirEntero($m):'');
    }
}
