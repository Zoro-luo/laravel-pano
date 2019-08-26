<?php


namespace App\Exceptions;


use http\Exception\RuntimeException;
use Throwable;

class ApiException extends RuntimeException
{
    public function __construct(array $apiErrConst, Throwable $previous = null)
    {
        $code = $apiErrConst[0];
        $message =$apiErrConst[1];
        parent::__construct($message, $code, $previous);
    }
}