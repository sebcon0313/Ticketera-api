<?php

namespace App\Providers;

use Illuminate\Filesystem\Filesystem;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\ServiceProvider;
use App\Domain\Event\Repositories\Contracts\EventRepositoryInterface;
use App\Domain\Event\Repositories\EventRepository;
use App\Domain\Event_Sections\Repositories\EventSectionsRepository;
use App\Domain\Event_Sections\Repositories\IEventSectionsRepository;
use App\Domain\Venue\Repositories\IVenueRepository;
use App\Domain\Venue\Repositories\VenueRepository;
use App\Domain\Section\Repositories\ISectionRepository;
use App\Domain\Section\Repositories\SectionRepository;
use App\Domain\Seat\Repositories\ISeatRepository;
use App\Domain\Seat\Repositories\SeatRepository;
use App\Domain\User\Repositories\Contracts\UserRepositoryInterface;
use App\Domain\User\Repositories\UserRepository;
use App\Domain\Invoice\Repositories\InvoiceRepositoryInterface;
use App\Domain\Invoice\Repositories\InvoiceRepository;

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

        $this->app->bind(
            ISectionRepository::class,
            SectionRepository::class
        );

        $this->app->bind(
            ISeatRepository::class,
            SeatRepository::class
        );

        $this->app->bind(
            IEventSectionsRepository::class,
            EventSectionsRepository::class
        );

        $this->app->bind(
            IVenueRepository::class,
            VenueRepository::class
        );

        $this->app->bind(
            InvoiceRepositoryInterface::class,
            InvoiceRepository::class
        );
    }

    /* public function boot(): void
    {
        JsonResource::withoutWrapping();
    } */
}
