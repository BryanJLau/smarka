<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateItemsTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('items', function(Blueprint $table)
		{
			$table->increments('id');
            $table->string('name');
            $table->string('description');
            $table->decimal('price', 5, 2);
            // Revolving kitchen
            $table->boolean('active')->default(false);
            // Second picture not necessary
            $table->boolean('picture2')->default(false);
            
            // Unique
            $table->unique('name');
		});
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('items');
	}

}
