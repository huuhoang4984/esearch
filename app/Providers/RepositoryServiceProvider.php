<?php

namespace App\Providers;

use App\Repositories\Contracts\ApartmentBuildingRepository;
use App\Repositories\Eloquent\ApartmentBuildingRepositoryEloquent;
use Illuminate\Support\ServiceProvider;

class RepositoryServiceProvider extends ServiceProvider
{
    /**
     * @var array
     */
    protected $fieldSearchable = [
        'first_name' => 'like',
        'last_name' => 'like',
        'email' => 'like',
    ];

    /**
     * Bootstrap the application services.
     *
     * @return void
     */
    public function boot()
    {
        $this->app->bind(ApartmentBuildingRepository::class, ApartmentBuildingRepositoryEloquent::class);
    }

    /**
     * Register the application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }
}
