<?php

namespace App\Providers;

use Exception;
use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\ServiceProvider;
use App\Models\PropertyListing;
use App\Observers\PropertyListingObserver;

class AppServiceProvider extends ServiceProvider
{
    /**
     * The path to the "home" route for your application.
     *
     * Typically, users are redirected here after authentication.
     *
     * @var string
     */
    public const HOME = '/dashboard';

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
        if ($this->app->environment() == 'production') {
            $this->app['request']->server->set('HTTPS', true);
        }

        $this->setSchemaDefaultLength();

        Validator::extend('base64image', function ($attribute, $value, $parameters, $validator) {
            $explode = explode(',', $value);
            $allow = ['png', 'jpg', 'svg', 'jpeg'];
            $format = str_replace(
                [
                    'data:image/',
                    ';',
                    'base64',
                ],
                [
                    '', '', '',
                ],
                $explode[0]
            );

            // check file format
            if (! in_array($format, $allow)) {
                return false;
            }

            // check base64 format
            if (! preg_match('%^[a-zA-Z0-9/+]*={0,2}$%', $explode[1])) {
                return false;
            }

            return true;
        });

        PropertyListing::observe(PropertyListingObserver::class);

        $this->bootRoute();
        
        // Override devdojo/auth language config with translations
        $this->overrideAuthLanguage();
    }

    /**
     * Override devdojo/auth language configuration with i18n translations
     */
    private function overrideAuthLanguage(): void
    {
        // Only override after translations are loaded
        $this->app->booted(function () {
            config([
                'devdojo.auth.language.login.page_title' => __('auth.login.page_title'),
                'devdojo.auth.language.login.headline' => __('auth.login.headline'),
                'devdojo.auth.language.login.subheadline' => __('auth.login.subheadline'),
                'devdojo.auth.language.login.email_address' => __('auth.login.email_address'),
                'devdojo.auth.language.login.password' => __('auth.login.password'),
                'devdojo.auth.language.login.remember_me' => __('auth.login.remember_me'),
                'devdojo.auth.language.login.edit' => __('auth.login.edit'),
                'devdojo.auth.language.login.button' => __('auth.login.button'),
                'devdojo.auth.language.login.forget_password' => __('auth.login.forget_password'),
                'devdojo.auth.language.login.dont_have_an_account' => __('auth.login.dont_have_an_account'),
                'devdojo.auth.language.login.sign_up' => __('auth.login.sign_up'),
                'devdojo.auth.language.login.social_auth_authenticated_message' => __('auth.login.social_auth_authenticated_message'),
                'devdojo.auth.language.login.change_email' => __('auth.login.change_email'),
                'devdojo.auth.language.login.couldnt_find_your_account' => __('auth.login.couldnt_find_your_account'),
                
                'devdojo.auth.language.register.page_title' => __('auth.register.page_title'),
                'devdojo.auth.language.register.headline' => __('auth.register.headline'),
                'devdojo.auth.language.register.subheadline' => __('auth.register.subheadline'),
                'devdojo.auth.language.register.name' => __('auth.register.name'),
                'devdojo.auth.language.register.email_address' => __('auth.register.email_address'),
                'devdojo.auth.language.register.password' => __('auth.register.password'),
                'devdojo.auth.language.register.password_confirmation' => __('auth.register.password_confirmation'),
                'devdojo.auth.language.register.already_have_an_account' => __('auth.register.already_have_an_account'),
                'devdojo.auth.language.register.sign_in' => __('auth.register.sign_in'),
                'devdojo.auth.language.register.button' => __('auth.register.button'),
                'devdojo.auth.language.register.email_registration_disabled' => __('auth.register.email_registration_disabled'),
                'devdojo.auth.language.register.registrations_disabled' => __('auth.register.registrations_disabled'),
                
                'devdojo.auth.language.verify.page_title' => __('auth.verify.page_title'),
                'devdojo.auth.language.verify.headline' => __('auth.verify.headline'),
                'devdojo.auth.language.verify.subheadline' => __('auth.verify.subheadline'),
                'devdojo.auth.language.verify.description' => __('auth.verify.description'),
                'devdojo.auth.language.verify.new_request_link' => __('auth.verify.new_request_link'),
                'devdojo.auth.language.verify.new_link_sent' => __('auth.verify.new_link_sent'),
                'devdojo.auth.language.verify.or' => __('auth.verify.or'),
                'devdojo.auth.language.verify.logout' => __('auth.verify.logout'),
                
                'devdojo.auth.language.passwordConfirm.page_title' => __('auth.password_confirm.page_title'),
                'devdojo.auth.language.passwordConfirm.headline' => __('auth.password_confirm.headline'),
                'devdojo.auth.language.passwordConfirm.subheadline' => __('auth.password_confirm.subheadline'),
                'devdojo.auth.language.passwordConfirm.password' => __('auth.password_confirm.password'),
                'devdojo.auth.language.passwordConfirm.button' => __('auth.password_confirm.button'),
                
                'devdojo.auth.language.passwordResetRequest.page_title' => __('auth.password_reset_request.page_title'),
                'devdojo.auth.language.passwordResetRequest.headline' => __('auth.password_reset_request.headline'),
                'devdojo.auth.language.passwordResetRequest.subheadline' => __('auth.password_reset_request.subheadline'),
                'devdojo.auth.language.passwordResetRequest.email' => __('auth.password_reset_request.email'),
                'devdojo.auth.language.passwordResetRequest.button' => __('auth.password_reset_request.button'),
                'devdojo.auth.language.passwordResetRequest.or' => __('auth.password_reset_request.or'),
                'devdojo.auth.language.passwordResetRequest.return_to_login' => __('auth.password_reset_request.return_to_login'),
                
                'devdojo.auth.language.passwordReset.page_title' => __('auth.password_reset.page_title'),
                'devdojo.auth.language.passwordReset.headline' => __('auth.password_reset.headline'),
                'devdojo.auth.language.passwordReset.subheadline' => __('auth.password_reset.subheadline'),
                'devdojo.auth.language.passwordReset.email' => __('auth.password_reset.email'),
                'devdojo.auth.language.passwordReset.password' => __('auth.password_reset.password'),
                'devdojo.auth.language.passwordReset.password_confirm' => __('auth.password_reset.password_confirm'),
                'devdojo.auth.language.passwordReset.button' => __('auth.password_reset.button'),
                
                'devdojo.auth.language.twoFactorChallenge.page_title' => __('auth.two_factor_challenge.page_title'),
                'devdojo.auth.language.twoFactorChallenge.headline_auth' => __('auth.two_factor_challenge.headline_auth'),
                'devdojo.auth.language.twoFactorChallenge.subheadline_auth' => __('auth.two_factor_challenge.subheadline_auth'),
                'devdojo.auth.language.twoFactorChallenge.headline_recovery' => __('auth.two_factor_challenge.headline_recovery'),
                'devdojo.auth.language.twoFactorChallenge.subheadline_recovery' => __('auth.two_factor_challenge.subheadline_recovery'),
            ]);
        });
    }

    private function setSchemaDefaultLength(): void
    {
        try {
            Schema::defaultStringLength(191);
        } catch (Exception $exception) {
        }
    }

    public function bootRoute()
    {
        RateLimiter::for('api', function (Request $request) {
            return Limit::perMinute(60)->by($request->user()?->id ?: $request->ip());
        });

    }
}
