<?php

namespace Concrete\Package\Ht7C5WsTictactoe;

use \Concrete\Core\Foundation\Service\Provider as CoreServiceProvider;
use \Concrete\Package\Ht7C5WsTictactoe\Services\Winner;

class ServiceProvider extends CoreServiceProvider
{
    public function register()
    {
        $this->app->singleton(
            'ht7/ws/tictactoe/winner',
            Winner::class
        );
    }
}
