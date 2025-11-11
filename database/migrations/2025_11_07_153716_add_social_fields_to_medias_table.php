<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('medias', function (Blueprint $table) {
            $table->text('permalink')->nullable();
            $table->string('media_social_id')->nullable()->after('permalink');
        });
    }

    public function down(): void
    {
        Schema::table('medias', function (Blueprint $table) {
            $table->dropColumn(['permalink', 'media_social_id']);
        });
    }
};
