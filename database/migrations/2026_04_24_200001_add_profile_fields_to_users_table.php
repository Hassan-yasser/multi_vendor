<?php

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
        Schema::table('users', function (Blueprint $table) {
            $table->string('first_name')->nullable()->after('name');
            $table->string('last_name')->nullable()->after('first_name');
            $table->date('birthday')->nullable()->after('last_name');
            $table->string('gender')->nullable()->after('birthday');
            $table->string('country')->nullable()->after('gender');
            $table->text('address')->nullable()->after('country');
            $table->string('postal_code')->nullable()->after('address');
            $table->string('city')->nullable()->after('postal_code');
            $table->string('locale', 10)->default('en')->after('city');
            $table->string('state')->nullable()->after('locale');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn([
                'first_name',
                'last_name',
                'birthday',
                'gender',
                'country',
                'address',
                'postal_code',
                'city',
                'locale',
                'state'
            ]);
        });
    }
};
