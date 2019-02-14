<?php

function hash_hmac_adler32($data, $key)
{
    $blockSize = 4;
    $K = [];
    if(strlen($key) > $blockSize) {
        $K = adler32_final($key);
    }
    else {
        for($i = 0; $i < $blockSize; $i++) {
            if(!empty(substr($key, $i, 1))) {
                $K[] = ord(substr($key, $i, 1));
            }
            else {
                $K[] = 0;
            }
        }
    }

    for($i = 0; $i < $blockSize; $i++) {
        $K[$i] ^= 0x36;
    }

    $context = adler32_update($K);
    $context = adler32_update($data, $context);

    $digest = adler32_final(null, $context);

    for($i = 0; $i < $blockSize; $i++) {
        $K[$i] ^= 0x6A;
    }

    $context = adler32_update($K);
    $context = adler32_update($digest, $context);
    $digest = adler32_final(null, $context);

    return hash_bin2hex($digest);
}

function hash_bin2hex($binaryArray) {

    $len = count($binaryArray);

    $hexits = ['0', '1', '2', '3', '4', '5', '6', '7', '8', '9', 'a', 'b', 'c', 'd', 'e', 'f'];

    $out = [];
    for($i = 0; $i < $len; $i++) {
        $out[$i * 2]       = $hexits[$binaryArray[$i] >> 4];
        $out[($i * 2) + 1] = $hexits[$binaryArray[$i] & 0x0F];
    }
    return implode($out);
}

function adler32_final($data, $context = null) {
    if($context == null) {
        $context = adler32_update($data);
    }
    $digest = [];
    $digest[0] = ($context >> 24) & 0xff;
    $digest[1] = ($context >> 16) & 0xff;
    $digest[2] = ($context >> 8) & 0xff;
    $digest[3] = $context & 0xff;

    return $digest;
}

function adler32_update($data, $context = 1){

    $len = 0;
    if(is_string($data)) {
        $len = strlen($data);
    }
    else if( is_array($data) ) {
        $len = count($data);
    }

    $a = $context & 0xffff;
    $b = ($context >> 16) & 0xffff;
    for($i = 0; $i < $len; $i++) {
        if(is_string($data)) {
            $a += ord(substr($data, $i, 1));
        }
        else if( is_array($data) ) {
            $a += $data[$i];
        }
        $b += $a;
        if($b >= 0x7fffffff) {
            $a = $a % 65521;
            $b = $b % 65521;
        }
    }
    $a = $a % 65521;
    $b = $b % 65521;
    return $a + ($b << 16);
}