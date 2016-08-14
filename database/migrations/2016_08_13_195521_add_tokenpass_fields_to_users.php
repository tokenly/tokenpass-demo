<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddTokenpassFieldsToUsers extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
		Schema::table('users', function(Blueprint $table)
		{
			$table->string('tokenly_uuid')->nullable();
			$table->string('oauth_token')->nullable();
		});
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
		Schema::table('users', function(Blueprint $table)
		{
			$table->dropColumn('tokenly_uuid');
			$table->dropColumn('oauth_token');
		});
    }
}
