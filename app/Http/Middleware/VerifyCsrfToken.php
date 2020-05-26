<?php

namespace App\Http\Middleware;

use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken as BaseVerifier;

class VerifyCsrfToken extends BaseVerifier
{
    /**
     * The URIs that should be excluded from CSRF verification.
     *
     * @var array
     */
    protected $except = [
        'test',
        'upload',
        'krpano/upload',                //上传api
        'krpano/fr.blade.php',      //切片漫游api
        'krpano/indexs',
        'krpano/uploads',
        'krpano/panos',
        'krpano/exec',
    ];
}
