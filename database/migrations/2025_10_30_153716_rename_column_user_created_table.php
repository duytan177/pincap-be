<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('album_media', function (Blueprint $table) {
            $table->foreignUuid('added_by_user_id')->nullable()->after('user_created')->references('id')->on('users')->onDelete('set null');
        });

        DB::statement('UPDATE album_media SET added_by_user_id = user_created');

        Schema::table('album_media', function (Blueprint $table) {
            $table->dropForeign(['user_created']);
            $table->dropColumn('user_created');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {

        // addtional column user_created
        Schema::table('album_media', function (Blueprint $table) {
            $table->foreignUuid('user_created')->nullable()->after('media_id')->references('id')->on('users')->onDelete('set null');
        });

        // Copy data
        DB::statement('UPDATE album_media SET user_created = added_by_user_id');

        // Remove foreign key + add new column
        Schema::table('album_media', function (Blueprint $table) {
            // Xóa ràng buộc foreign key trước
            $table->dropForeign(['added_by_user_id']);
            // Sau đó xóa cột
            $table->dropColumn('added_by_user_id');
        });
    }
};
