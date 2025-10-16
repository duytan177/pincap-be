<?php

use App\Enums\Album_Media\AlbumRole;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Step 1: extend enum to include old values
        $allValues = array_merge(['0', '1', '2'], AlbumRole::getValues());
        $enumList = "'" . implode("','", $allValues) . "'";
        DB::statement("ALTER TABLE user_album MODIFY album_role ENUM($enumList) DEFAULT '" . AlbumRole::VIEW . "'");

        // Step2 : update existing data
        DB::table('user_album')
            ->where('album_role', '0')
            ->update(['album_role' => AlbumRole::OWNER]);
        DB::table('user_album')
            ->where('album_role', '1')
            ->update(['album_role' => AlbumRole::EDIT]);
        DB::table('user_album')
            ->where('album_role', '2')
            ->update(['album_role' => AlbumRole::VIEW]);

        // B3: remove old enum values
        $newEnumList = "'" . implode("','", AlbumRole::getValues()) . "'";
        DB::statement("ALTER TABLE user_album MODIFY album_role ENUM($newEnumList) DEFAULT '" . AlbumRole::VIEW . "'");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Rollback the changes made in the up() method
        $allValues = array_merge(['0', '1', '2'], AlbumRole::getValues());
        $enumList = "'" . implode("','", $allValues) . "'";
        DB::statement("ALTER TABLE user_album MODIFY album_role ENUM($enumList) DEFAULT '1'");

        // Map back the roles to their original numeric values
        DB::table('user_album')
            ->where('album_role', AlbumRole::OWNER)
            ->update(['album_role' => '0']);
        DB::table('user_album')
            ->where('album_role', AlbumRole::EDIT)
            ->update(['album_role' => '1']);
        DB::table('user_album')
            ->where('album_role', AlbumRole::VIEW)
            ->update(['album_role' => '2']);

        // Revert enum to original state
        DB::statement("ALTER TABLE user_album MODIFY album_role ENUM('0','1','2') DEFAULT '1'");
    }
};
