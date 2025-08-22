<?php

namespace App\Http\Controllers\Notifications;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Pusher\Pusher;

class PusherController extends Controller
{
    //
    public function __invoke(Request $request)
    {
        $user = auth()->user();
        if ($user) {
            $pusher = new Pusher(config('broadcasting.connections.pusher.key'), config('broadcasting.connections.pusher.secret'), config('broadcasting.connections.pusher.app_id'));
            $auth = $pusher->socket_auth($request->input('channel_name'), $request->input('socket_id'));
            return response($auth, 200)->header('Content-Type', 'application/json');
        } else {
            header('', true, 403);
            echo "Forbidden";
            return response()->json(['statut' => 403]);
        }
    }
}
