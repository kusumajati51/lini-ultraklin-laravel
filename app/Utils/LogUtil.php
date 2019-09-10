<?php

namespace App\Utils;

use Monolog\Logger as Log;
use Monolog\Handler\StreamHandler;
use Monolog\Formatter\JsonFormatter;

class LogUtil {
    protected $filename;
    protected $format;
    protected $name;
    protected $suffix;

    protected $stream;
    protected $log;
    protected $level;

    public function __construct($name = 'app', $format = 'json')
    {
        $this->name = $name;
        $this->suffix = '__'.date('Y-m-d');
        $this->filename = $this->name.$this->suffix.'.log';
    }

    public function createLogContext($request, $data) {
        $context = [
            'method' => $request->method(),
            'url' => $request->url(),
            'fullUrl' => $request->fullUrl(),
            'ip' => $request->ip(),
            'userAgent' => $request->header('User-Agent'),
            'user' => $request->user() ? $request->user()->name : null,
            'requestBody' => collect($request->all())->filter(function ($value, $key) {
                $ignoreKeys = [
                    'grant_type',
                    'client_id',
                    'client_secret',
                    'access_token',
                    'scope',
                    'refresh_token',
                    'password',
                    'password_confirmation',
                ];

                return !in_array($key, $ignoreKeys);
            }),
            'responseStatus' => $data['code'],
            'responseData' => $data['data']
        ];

        return $context;
    }

    public function info()
    {
        $this->level = 'INFO';

        $this->stream = new StreamHandler(storage_path('logs/'.$this->filename), Log::INFO);
        $this->stream->setFormatter(new JsonFormatter);

        $this->log = new Log($this->name);
        $this->log->pushHandler($this->stream);

        return $this;
    }

    public function error()
    {
        $this->level = 'ERROR';

        $this->stream = new StreamHandler(storage_path('logs/'.$this->filename), Log::ERROR);
        $this->stream->setFormatter(new JsonFormatter);

        $this->log = new Log($this->name);
        $this->log->pushHandler($this->stream);

        return $this;
    }

    public function create($message = '', $request, $data)
    {
        $context = $this->createLogContext($request, $data);

        switch ($this->level) {
            case 'INFO':
                $this->log->info(
                    $message,
                    $context
                );

                break;
            case 'ERROR':
                $this->log->error(
                    $message,
                    $context
                );

                break;
        }
    }

    public function createWithException($request, $e = null)
    {
        if ($e == null) return;

        $this->error()->create(
            $e->getMessage(),
            $request,
            [
                'code' => 500,
                'data' => [
                    'message' => $e->getMessage(),
                    'trace' => $e->getTrace()
                ]
            ]
        );
    }

    public function createWithResponse($request, $response = null)
    {
        if ($response == null) return;

        $isJsonResponse = $response->headers->get('Content-type') == 'application/json';

        if (preg_match('/^2\d{2}/', $response->status())) {
            $this->info()->create(
                $request->method().' '.$request->fullUrl().' '.$response->status(),
                $request,
                [
                    'code' => $response->status(),
                    'data' => $isJsonResponse ? json_decode($response->content()) : []
                ]
            );
        } else {
            $this->error()->create(
                $request->method().' '.$request->fullUrl().' '.$response->status(),
                $request,
                [
                    'code' => $response->status(),
                    'data' => $isJsonResponse ? json_decode($response->content()) : []
                ]
            );
        }

    }
}
