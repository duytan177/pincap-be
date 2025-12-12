<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateUserEventsTable extends Migration
{
    public function up()
    {
        Schema::create('user_events', function (Blueprint $table) {
            $table->id();
            $table->foreignUuid('user_id')->references('id')->on('users')->onDelete("cascade");
            $table->enum('event_type', \App\Enums\User\EventType::getValues())->nullable(); // Use enum values directly
            $table->foreignUuid('media_id')->references('id')->on('medias')->onDelete("cascade");
            $table->json('metadata')->nullable();
            $table->boolean('processed')->default(false);
            $table->timestamps();

            $table->index(['user_id', 'created_at']);
            $table->index(['media_id', 'created_at']);
        });

        Schema::create('media_stats', function (Blueprint $table) {
            $table->id();
            $table->foreignUuid('media_id')->references('id')->on('medias')->onDelete("cascade");
            $table->unsignedBigInteger('views')->default(0);
            $table->unsignedBigInteger('likes')->default(0);
            $table->unsignedBigInteger('comments')->default(0);
            $table->unsignedBigInteger('saves')->default(0);
            $table->decimal('score', 14, 4)->default(0);
            $table->timestamps();

            $table->index('updated_at');
        });
    }
    public function down()
    {
        Schema::dropIfExists('user_events');
        Schema::dropIfExists('media_stats');

    }
}
