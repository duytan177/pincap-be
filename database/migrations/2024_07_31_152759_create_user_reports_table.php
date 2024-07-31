<?php

use App\Enums\Album_Media\StateReport;
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
        Schema::create('user_reports', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->enum('report_state',StateReport::getValues())->default(StateReport::UNPROCESSED);
            $table->foreignUuid('user_id')->references('id')->on('users');
            $table->foreignUuid('user_report_id')->references('id')->on('users');
            $table->foreignUuid('reason_report_id')->nullable()->references('id')->on('reasons_report');
            $table->longText("other_reasons")->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('user_reports');
    }
};
