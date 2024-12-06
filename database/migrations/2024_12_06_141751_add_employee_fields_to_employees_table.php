<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('employees', function (Blueprint $table) {
            $table->string('first_name')->after('id');
            $table->string('last_name')->after('first_name');
            $table->foreignId('company_id')->after('last_name')->constrained()->onDelete('cascade');
            $table->string('email')->nullable()->after('company_id');
            $table->string('phone')->nullable()->after('email');
        });
    }

    public function down()
    {
        Schema::table('employees', function (Blueprint $table) {
            $table->dropForeign(['company_id']);
            $table->dropColumn(['first_name', 'last_name', 'company_id', 'email', 'phone']);
        });
    }
};