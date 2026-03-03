<?php

namespace CherryneChou\Admin\Events;

use CherryneChou\Admin\Models\Administrator;
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

    public Administrator $administrator;

	/**
     * Create a new event instance.
     */
    public function __construct(Administrator $administrator)
    {
        $this->request = request();
        $this->administrator = $administrator;
    }

}

