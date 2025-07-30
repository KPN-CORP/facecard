<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('import_logs', function (Blueprint $table) {
            // Column for save path file
            $table->string('original_file_path')->nullable()->after('status');
            $table->string('error_file_path')->nullable()->after('original_file_path');
            
            // Adding user_id column if it doesn't exist yet
            if (!Schema::hasColumn('import_logs', 'user_id')) {
                $table->foreignId('user_id')->nullable()->constrained()->after('id');
            }
            
            // Change data column type
            $table->text('result')->change();
            $table->string('status', 50)->change(); // e.g., 'Success', 'Failed'
        });
    }

    public function down(): void
    {
        Schema::table('import_logs', function (Blueprint $table) {
            $table->dropColumn(['original_file_path', 'error_file_path']);
            if (Schema::hasColumn('import_logs', 'user_id')) {
                 $table->dropForeign(['user_id']);
                 $table->dropColumn('user_id');
            }
        });
    }
};