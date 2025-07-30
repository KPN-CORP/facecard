<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('competency_assessments', function (Blueprint $table) {
            $table->string('proposed_grade', 10)->nullable()->after('matrix_grade');
            $table->string('priority_for_development', 5)->nullable()->after('proposed_grade'); // Yes/No
        });
    }

    public function down(): void
    {
        Schema::table('competency_assessments', function (Blueprint $table) {
            $table->dropColumn(['proposed_grade', 'priority_for_development']);
        });
    }
};