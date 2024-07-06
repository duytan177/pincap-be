<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use App\Enums\Album_Media\StateReport;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('report_media', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->enum('reportState',StateReport::getValues())->default(StateReport::UNPROCESSED);
            $table->foreignUuid('user_id')->references('id')->on('users');
            $table->foreignUuid('media_id')->references('id')->on('medias');
            $table->foreignUuid('reason_report_id')->nullable()->references('id')->on('reasons_report');
            $table->longText("other_reasons")->nullable();
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
        Schema::dropIfExists('report_media');
    }
};
