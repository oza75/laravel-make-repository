<?php

namespace Oza75\MakeRepository;

use Illuminate\Support\ServiceProvider;
use Oza75\MakeRepository\Commands\MakeRepositoryCommand;

class MakeRepositoryServiceProvider extends ServiceProvider
{
    public function boot()
    {
        $this->commands([
            MakeRepositoryCommand::class
        ]);

        $this->publishes([
            __DIR__.'/../stubs' => storage_path('app/make-repository/stubs')
        ]);
    }
}
