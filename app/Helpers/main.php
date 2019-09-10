<?php

if (!function_exists('currency')) {
    function currency($num)
    {
        return 'Rp. '.number_format($num, 0, ',', '.');
    }
}

if (!function_exists('human_price')) {
    function human_price($num)
    {
        $util = new App\Utils\Util;;

        return $util->humanPrice($num);
    }
}

if (!function_exists('handle_error')) {
    function handle_error($request, $e)
    {
        $log = new \App\Utils\LogUtil('error');
        
        $log->createWithException($request, $e);

        if (env('APP_DEBUG')) {
            return [
                'error' => 1,
                'message' => $e->getMessage(),
                'trace' => $e->getTrace()
            ];
        }

        return [
            'error' => 1,
            'message' => 'Oops! Something went wrong.'
        ];
    }
}
