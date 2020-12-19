<?php

namespace Dt\Media\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Routing\Router;
use Dt\Media\Http\Controllers\UploadController;
use Dt\Media\Services\Contracts\FileManagerInterface;
use Dt\Media\Services\FileManager;

class MediaServiceProvider extends ServiceProvider
{
    public function register()
    {
        parent::register();

        $this->app->when(UploadController::class)
            ->needs(FileManagerInterface::class)
            ->give(FileManager::class);
    }

    public function boot()
    {
        $this->loadRoutesFrom(__DIR__.'/../../routes/api.php');
        $router = $this->app->make(Router::class);
    }
}
