<?php

namespace Roxdigital\PinpointImage;

use Roxdigital\PinpointImage\Fieldtypes\PinPointImage;
use Roxdigital\PinpointImage\GraphQL\PinPointImageFieldType;
use Statamic\Facades\GraphQL;
use Statamic\Providers\AddonServiceProvider;
use Statamic\Statamic;

class ServiceProvider extends AddonServiceProvider
{
    protected $scripts = [
        __DIR__.'/../resources/dist/js/cp.js',
    ];

    protected $fieldtypes = [
        PinPointImage::class,
    ];

    public function boot()
    {
        parent::boot();

        Statamic::booted(function () {
            $this->bootAddonViews();
        });

        $this->bootGraphQL();
    }

    protected function bootAddonViews(): self
    {
        $this->loadViewsFrom(__DIR__.'/../resources/views/tags', 'pinpoint-image');

        $this->publishes([
            __DIR__.'/../resources/views/tags' => resource_path('views/vendor/pinpoint-image/tags'),
        ], 'pinpoint-image-views');

        return $this;
    }

    private function bootGraphQL(): self
    {
        GraphQL::addType(PinPointImageFieldType::class);

        return $this;
    }
}
