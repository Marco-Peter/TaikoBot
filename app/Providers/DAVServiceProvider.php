<?php

use Illuminate\Support\ServiceProvider;
use LaravelSabre\LaravelSabre;
use Sabre\DAVACL\PrincipalBackend\PDO as PrincipalBackend;
use Sabre\DAVACL\PrincipalCollection;

class DAVServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        LaravelSabre::nodes(function () {
            return $this->nodes();
        });
    }

    /**
     * List of nodes for DAV Collection.
     */
    private function nodes() : array
    {
        $principalBackend = new PrincipalBackend();

        return [
            new PrincipalCollection($principalBackend),
        ];
    }
}
