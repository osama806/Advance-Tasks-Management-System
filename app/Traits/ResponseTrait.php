<?php

namespace App\Traits;

trait ResponseTrait
{
    /**
     * Return a specified format from response
     * @param mixed $key
     * @param mixed $val
     * @param int $code
     * @return \Illuminate\Contracts\Routing\ResponseFactory|\Illuminate\Http\Response
     */
    public function getResponse( $key, $val, int $code)
    {
        return response([
            'isSuccess'         => ($code >= 200 && $code < 300) ? true : false,
            $key                =>      $val
        ], $code);
    }
}
