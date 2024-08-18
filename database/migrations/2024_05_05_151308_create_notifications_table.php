<?php

use App\Enums\Notifications\NotificationType;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('notifications', function (Blueprint $table) {
            $table->uuid("id")->primary();
            $table->text("title");
            $table->longText("content");
            $table->boolean("is_read")->default(false);
            $table->foreignUuid('sender_id')->nullable()->references('id')->on('users');
            $table->foreignUuid('receiver_id')->nullable()->references('id')->on('users');
            $table->enum('notification_type', NotificationType::getValues());
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('notifications');
    }
};
