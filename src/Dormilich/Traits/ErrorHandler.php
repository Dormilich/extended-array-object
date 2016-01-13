<?php

namespace Dormilich\Traits;

trait ErrorHandler
{
    /**
     * Convert errors to exceptions and reset the error handler while at it. 
     * The nested ErrorException serves as means to get a hold on the line the 
     * original error occurred at.
     * 
     * @param integer $code Error code.
     * @param string $msg Error message.
     * @param string $file File where the error originated.
     * @param integer $line Line where the error originated.
     * @return RuntimeException
     */
    public function errorHandler($code, $msg, $file, $line)
    {
        restore_error_handler();

        $error = new \ErrorException($msg, 0, $code, $file, $line);

        throw new \RuntimeException($msg, $code, $error);
    }
}
