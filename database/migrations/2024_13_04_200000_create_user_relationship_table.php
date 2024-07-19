.
<?php

use App\Enums\User\UserStatus;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('user_relationship', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid("followee_id")->references("id")->on("users");
            $table->foreignUuid("follower_id")->references("id")->on("users");
            $table->enum('user_status', UserStatus::getValues())->default(UserStatus::FOLLOWING);
            $table->timestamps();
            $table->softDeletes();

            // Đảm bảo cặp follower_id và followee_id là duy nhất
            $table->unique(['followee_id', 'follower_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('user_relationship');
    }
};
