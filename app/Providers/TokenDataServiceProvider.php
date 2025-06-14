<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\DB;

class TokenDataServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        //
    }

    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot()
    {
        app()->singleton('token_data', function () {
            $token = request()->bearerToken();
            if ($token) {
                $tokenRecord = DB::table('oauth_access_tokens')->where('token', $token)->first();
                return [
                    'organization_id' => $tokenRecord->organization_id ?? null,
                    'fiscal' => $tokenRecord->fiscal ?? null,
                ];
            }

            return [
                'organization_id' => null,
                'fiscal' => null,
            ];
        });
    }
}
