<?php

namespace CherryneChou\Admin\Listeners;

use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Str;
use Illuminate\Support\Stringable;
use CherryneChou\Admin\Models\LoginLog as LoginLogModel;
use CherryneChou\Admin\Events\Login as LoginEvent;

class Login
{
	public function handle(LoginEvent $event): void
	{
		$request = $event->request;

		$log = [
            'account' => Admin::user()->name,
            'login_ip' => $request->ip(),
            'browser' => $this->getBrowserFrom(Str::of($request->userAgent())),
            'platform' => $this->getPlatformFrom(Str::of($request->userAgent())),
            'login_at' => Carbon::now()
        ];

        try {
            LoginLogModel::create($log);
        } catch (\Exception $exception) {
            // pass
        }
	}


    /**
     * get platform
     */
    protected function getBrowserFrom(Stringable $userAgent): string
    {
        return match (true) {
            $userAgent->contains('MSIE', true) => 'IE',
            $userAgent->contains('Firefox', true) => 'Firefox',
            $userAgent->contains('Chrome', true) => 'Chrome',
            $userAgent->contains('Opera', true) => 'Opera',
            $userAgent->contains('Safari', true) => 'Safari',
            default => 'unknown'
        };
    }

    /**
     * get os name
     */
    protected function getPlatformFrom(Stringable $userAgent): string
    {
        return match (true) {
            $userAgent->contains('win', true) => 'Windows',
            $userAgent->contains('mac', true) => 'Mac OS',
            $userAgent->contains('linux', true) => 'Linux',
            $userAgent->contains('iphone', true) => 'iphone',
            $userAgent->contains('android', true) => 'Android',
            default => 'unknown'
        };
    }

}
