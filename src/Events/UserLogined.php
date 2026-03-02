<?php

namespace CherryneChou\Admin\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Http\Request;
use Illuminate\Queue\SerializesModels;

class UserLogined
{
	use Dispatchable, InteractsWithSockets, SerializesModels;

	public Request $request;

	/**
     * Create a new event instance.
     */
    public function __construct()
    {
        $this->request = request();
    }

}

