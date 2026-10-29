<?php

use App\Console\Kernel as ConsoleKernel;
use App\Exceptions\Handler as ExceptionHandler;
use App\Http\Kernel as HttpKernel;
use Illuminate\Contracts\Console\Kernel as ConsoleKernelContract;
use Illuminate\Contracts\Debug\ExceptionHandler as ExceptionHandlerContract;
use Illuminate\Contracts\Http\Kernel as HttpKernelContract;
use Illuminate\Foundation\Application;

$app = new Application(dirname(__DIR__));

$app->singleton(HttpKernelContract::class, HttpKernel::class);
$app->singleton(ConsoleKernelContract::class, ConsoleKernel::class);
$app->singleton(ExceptionHandlerContract::class, ExceptionHandler::class);

return $app;
