<?php

if (! function_exists('unicode2Char')) {

    /**
     * 將unicode轉換成字元
     * @param int $unicode
     * @return string UTF-8字元
     * */
    function unicode2Char($unicode)
    {
        if ($unicode < 128) {
            return chr($unicode);
        }

        if ($unicode < 2048) {
            return chr(($unicode >> 6) + 192).
                chr(($unicode & 63) + 128);
        }

        if ($unicode < 65536) {
            return chr(($unicode >> 12) + 224).
                chr((($unicode >> 6) & 63) + 128).
                chr(($unicode & 63) + 128);
        }

        if ($unicode < 2097152) {
            return chr(($unicode >> 18) + 240).
                chr((($unicode >> 12) & 63) + 128).
                chr((($unicode >> 6) & 63) + 128).
                chr(($unicode & 63) + 128);
        }

        return false;
    }
}

if (! function_exists('char2Unicode')) {

    /**
     * 將字元轉換成unicode
     * @param string $char 必須是UTF-8字元
     * @return int
     * */
    function char2Unicode($char)
    {
        switch (strlen($char)) {
            case 1:
                return ord($char);
            case 2:
                return (ord($char{1}) & 63) | ((ord($char{0}) & 31) << 6);
            case 3:
                return (ord($char{2}) & 63) | ((ord($char{1}) & 63) << 6) | ((ord($char{0}) & 15) << 12);
            case 4:
                return (ord($char{3}) & 63) | ((ord($char{2}) & 63) << 6) | ((ord($char{1}) & 63) << 12) | ((ord($char{0}) & 7) << 18);
            default:
                trigger_error('Character is not UTF-8!', E_USER_WARNING);

                return false;
        }
    }
}

if (! function_exists('dbc2Sbc')) {

    /**
     * 半形轉全形
     * @param string $str
     * @return string
     *
     */
    function dbc2Sbc($str)
    {
        return preg_replace_callback(
            '/[\x{0020}\x{0020}-\x{7e}]/u',
            function ($m) {
                return ($unicode = char2Unicode($m[0])) == 0x0020 ? unicode2Char(0x3000) : (($code = $unicode + 0xfee0) > 256 ? unicode2Char($code) : chr($code));
            },
            $str
        );
    }
}

if (! function_exists('sbc2Dbc')) {

    /**
     * 全形轉半形
     * @param string $str
     * @return string
     *
     */
    function sbc2Dbc($str)
    {
        return preg_replace_callback(
            '/[\x{3000}\x{ff01}-\x{ff5f}]/u',
            function ($m) {
                return ($unicode = char2Unicode($m[0])) == 0x3000 ? ' ' : (($code = $unicode - 0xfee0) > 256 ? unicode2Char($code) : chr($code));
            },
            $str
        );
    }
}
