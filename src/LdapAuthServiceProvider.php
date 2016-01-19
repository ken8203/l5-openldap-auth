<?php namespace kcchung\L5OpenldapAuth;

/**
 * @package   kcchung/l5-opendldap-auth
 * @author    kcchung <ken8203@gmail.com>
 * @copyright Copyright (c) kcchung
 * @licence   http://mit-license.org/
 * @link      https://github.com/ken8203/l5-openldap-auth
 */

use Auth;
use Illuminate\Auth\Guard;
use Illuminate\Support\ServiceProvider;

class LdapAuthServiceProvider extends ServiceProvider
{
    /**
     * Indicates if loading of the provider is deferred.
     *
     * @var bool
     */
    protected $defer = false;

    /**
     * Perform post-registration booting of services.
     *
     * @return void
     */
    public function boot()
    {
        Auth::extend('ldap', function($app) {
            $provider = new LdapAuthUserProvider();
            return new Guard($provider, $app['session.store']);
        });
    }

    /**
     * Register bindings in the container.
     *
     * @return void
     */
    public function register()
    {
        //
    }

    /**
     * Get the services provided by the provider.
     *
     * @return array
     */
    public function provides()
    {
        return array('auth');
    }
}