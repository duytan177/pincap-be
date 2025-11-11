<?php

use App\Enums\User\SocialType;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('user_social_accounts', function (Blueprint $table) {
            $table->uuid('id')->primary();

            $table->uuid('user_id');
            $table->string('name')->nullable();
            $table->text('avatar')->nullable();
            $table->text('permalink')->nullable();
            $table->string('social_id');
            $table->text('access_token')->nullable();
            $table->dateTime('access_token_expired')->nullable();
            $table->text('refresh_token')->nullable();
            $table->dateTime('refresh_token_expired')->nullable();

            // Enum cho loại mạng xã hội
            $table->enum('social_type', SocialType::getValues());

            $table->timestamps();

            // Khóa ngoại tới bảng users
            $table->foreign('user_id')
                  ->references('id')
                  ->on('users')
                  ->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('user_social_accounts');
    }
};
