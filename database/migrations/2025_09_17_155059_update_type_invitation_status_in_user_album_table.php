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
        Schema::table('user_album', function (Blueprint $table) {
            $table->dropColumn('invitation_status');

            $table->enum('invitation_status', ['INVITED', 'ACCEPTED', 'REJECTED'])
                ->default(InvitationStatus::INVITED)
                ->comment('Status invite: INVITED, ACCEPTED, REJECTED')
                ->after('user_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('user_album', function (Blueprint $table) {
            $table->dropColumn('invitation_status');

            $table->boolean('invitation_status')
                ->default(false)
                ->after('user_id');
        });
    }
};
