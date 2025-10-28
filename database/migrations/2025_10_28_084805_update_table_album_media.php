<?php

use App\Enums\Album_Media\InvitationStatus;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('album_media', function (Blueprint $table) {
            $table->foreignUuid('user_created')->nullable()->after('media_id')->references('id')->on('users')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('album_media', function (Blueprint $table) {
            $table->dropForeign(['user_created']);
            $table->dropColumn('user_created');
        });
    }
};
