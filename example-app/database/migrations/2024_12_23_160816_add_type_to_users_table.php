<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
  // database/migrations/YYYY_MM_DD_HHMMSS_add_type_to_users_table.php

public function up()
{
    Schema::table('users', function (Blueprint $table) {
        $table->enum('type', ['admin', 'seller', 'buyer'])->default('buyer'); // Default to 'buyer'
    });
}

public function down()
{
    Schema::table('users', function (Blueprint $table) {
        $table->dropColumn('type');
    });
}

};
