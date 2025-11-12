<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Symfony\Component\HttpKernel\Exception\HttpExceptionInterface;


return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withCommands(
        [
            App\Console\Commands\ProductsInitIndex::class,
            App\Console\Commands\AppDoctor::class,
        ],
    )
    ->withMiddleware(function (Middleware $middleware) {
        //
    })
    ->withExceptions(function (Exceptions $exceptions) {
        $json = function (\Throwable $e, int $status, ?string $title = null, array $extra = []) {
            $id = (string) \Str::uuid();
            $payload = array_merge([
                'type'   => "/errors/{$status}",
                'title'  => $title ?? ($e instanceof \Exception ? class_basename($e) : 'Error'),
                'status' => $status,
                'detail' => app()->hasDebugModeEnabled() ? $e->getMessage() : ($status >= 500 ? 'Server Error' : $e->getMessage()),
                'trace_id' => $id,
            ], $extra);

            return response()->json($payload, $status, [
                'X-Trace-Id' => $id,
            ]);
        };
         $exceptions->report(function (\Throwable $e) {
            $isHttp = $e instanceof HttpExceptionInterface;
            $status = $isHttp ? $e->getStatusCode() : 500;
            if ($status >= 500) {
                logger()->error('Unhandled exception ddd', ['exception' => $e]);
            }
        });
        $exceptions->render(function (HttpExceptionInterface $e, $request) use ($json) {
            return $json($e, $e->getStatusCode());
        });
        $exceptions->render(function (\Throwable $e, $request) use ($json) {
            return $json($e, 500, 'Server Error');
        });
    })->create();
