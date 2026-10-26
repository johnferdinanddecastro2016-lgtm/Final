<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('workspaces', function (Blueprint $table) {
            $table->string('council_identifier', 64)->nullable()->unique();
        });

        DB::table('workspaces')->orderBy('id')->each(function (object $workspace) {
            DB::table('workspaces')->where('id', $workspace->id)->update([
                'council_identifier' => hash('sha256', implode('|', array_map(
                    fn (string $part) => preg_replace('/\s+/u', ' ', mb_strtolower(trim($part))) ?? mb_strtolower(trim($part)),
                    [$workspace->province, $workspace->municipality, $workspace->barangay]
                ))),
            ]);
        });
    }

    public function down(): void
    {
        Schema::table('workspaces', function (Blueprint $table) {
            $table->dropUnique(['council_identifier']);
            $table->dropColumn('council_identifier');
        });
    }
};
