<?php
namespace App\Http\Controllers\Users;

use App\Enums\User\EventType;
use App\Http\Controllers\Controller;
use App\Models\UserEvent;
use App\Services\KafkaProducerService;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class TrackEventController extends Controller
{
    public function __invoke(Request $req)
    {
        $data = $req->validate(rules: [
            'event_type' => ['required','string', Rule::in(EventType::getValues())], // view, like, comment, save, search
            'media_id' => 'required|string|exists:medias,id',
            'metadata' => 'nullable|array',
        ]);

        $event = UserEvent::create([
            'user_id' => auth()->id(),
            'event_type' => $data['event_type'],
            'media_id' => $data['media_id'],
            'metadata' => $data['metadata'] ?? []
        ]);

        // Produce to Kafka
        $payload = json_encode([
            'user_id' => $event->getAttribute('user_id'),
            'media_id' => $event->getAttribute('media_id'),
            'event_type' => $event->getAttribute('event_type'),
            'metadata' => $event->getAttribute('metadata'),
            'created_at' => $event->getAttribute('created_at')->toDateTimeString(),
        ]);

        (new KafkaProducerService('user_events'))->send($payload);
        return response()->noContent();
    }
}
