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
        $app = $this->app;
        $app['config']->package('fruitcakestudio/recaptcha', __DIR__ . '/../../config');

        $app['validator']->extend('recaptcha', function($attribute, $value, $parameters) use ($app) {
                $remoteip = $app['request']->getClientIp();
                return $app['recaptcha']->verify($value, $remoteip);
            });
    }

    /**
     * {@inheritdoc}
     */
    public function provides()
    {
        return array('recaptcha');
    }
}
