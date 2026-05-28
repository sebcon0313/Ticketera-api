<?php

namespace App\Providers;

use Illuminate\Filesystem\Filesystem;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\ServiceProvider;
use App\Domain\Event\Repositories\Contracts\EventRepositoryInterface;
use App\Domain\Event\Repositories\EventRepository;
use App\Domain\User\Repositories\Contracts\UserRepositoryInterface;
use App\Domain\User\Repositories\UserRepository;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
       $this->app->bind(
            EventRepositoryInterface::class,
            EventRepository::class
        );

        $this->app->bind(
            UserRepositoryInterface::class,
            UserRepository::class
        );
    }

    /* public function boot(): void
    {
        JsonResource::withoutWrapping();
    } */
}
