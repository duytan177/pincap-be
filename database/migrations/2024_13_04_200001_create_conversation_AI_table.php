.<?php

use App\Enums\AI\TypeAI;
use App\Enums\AI\TypeQA;
use App\Enums\User\UserStatus;
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
        Schema::create('conversation_AI', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->longText("content");
            $table->enum('type_qa',TypeQA::getValues());
            $table->enum('type_ai',TypeAI::getValues());
            $table->longText("image_url")->nullable();
            $table->foreignUuid("user_id")->references("id")->on("users");
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('conversation_AI');
    }
};
