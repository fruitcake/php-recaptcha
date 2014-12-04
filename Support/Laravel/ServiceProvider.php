<?php

namespace FruitcakeStudio\ReCaptcha\Support\Laravel;

use FruitcakeStudio\ReCaptcha\ReCaptcha;
use Illuminate\Support\ServiceProvider as BaseServiceProvider;


/**
 * ServiceProvider for Laravel integration
 *
 * @author Barry vd. Heuvel <barryvdh@gmail.com>
 * @author Fruitcake Studio (http://fruitcakestudio.com)
 */
class ServiceProvider extends BaseServiceProvider {

    /**
     * Indicates if loading of the provider is deferred.
     *
     * @var bool
     */
    protected $defer = false;

    /**
     * {@inheritdoc}
     */
    public function register()
    {
        $this->app['recaptcha'] = $this->app->share(
            function ($app) {
                $reCaptcha = new ReCaptcha(
                    $app['config']->get('recaptcha::sitekey'),
                    $app['config']->get('recaptcha::secret'),
                    $app['config']->get('recaptcha::lang') ?: $app->getLocale()
                );

                $reCaptcha->setRequest($app['request']);

                return $reCaptcha;
            }
        );
    }

    /**
     * {@inheritdoc}
     */
    public function boot()
    {
        $this->app['config']->package('fruitcakestudio/recaptcha', __DIR__ . '/../../config');
    }

    /**
     * {@inheritdoc}
     */
    public function provides()
    {
        return array('recaptcha');
    }
}
