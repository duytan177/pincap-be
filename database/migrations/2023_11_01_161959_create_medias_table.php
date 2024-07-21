<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use App\Enums\Album_Media\Privacy;
use App\Enums\Album_Media\MediaType;
return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('medias', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('media_name')->nullable();
            $table->longText('media_url')->nullable();
            $table->longText('description')->nullable();
            $table->enum('type',MediaType::getValues())->nullable();
            $table->enum('privacy',Privacy::getValues())->default(Privacy::PUBLIC);
            $table->boolean('is_created')->default(false);
            $table->boolean('is_comment')->default(true);
            $table->foreignUuid('media_owner_id')->references('id')->on('users');
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('medias');
    }
};
