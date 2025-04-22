<?php

namespace App\Providers;

use App\Models\PersonalAccessToken;
use Dedoc\Scramble\Scramble;
use Dedoc\Scramble\Support\Generator\OpenApi;
use Dedoc\Scramble\Support\Generator\SecurityScheme;
use Illuminate\Routing\Route;
use Illuminate\Support\ServiceProvider;
use Laravel\Sanctum\Sanctum;
use Str;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Scramble::configure()
        ->routes(function (Route $route) {
            return \Illuminate\Support\Str::startsWith($route->uri, 'api/');
        });
        Scramble::configure()
        ->withDocumentTransformers(function (OpenApi $openApi) {
            $openApi->secure(
                SecurityScheme::http('bearer')
            );
        });
        Scramble::configure()
        ->withDocumentTransformers(function (OpenApi $document) {
            $document->info->description = 'API for the best Todo app!';
        });
        Sanctum::usePersonalAccessTokenModel(PersonalAccessToken::class);
    }
}
