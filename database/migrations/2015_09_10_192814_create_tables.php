<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateItemAndOrderTables extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        // Represents an individual order
        Schema::create('orders', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name');
            $table->string('phone');
            $table->string('address');
            $table->timestamp('ordered_on');
            $table->boolean('paid')->default(false);
            // Serialized array
            $table->string('item_array');
            
            $table->index('name');
        });
        
        // Represents an item
        Schema::create('items', function (Blueprint $table) {
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
        
        // Custom notifications
        Schema::create('notifications', function(Blueprint $table) {
            $table->increments('id');
            $table->string('text');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        //
        Schema::drop('orders');
        Schema::drop('items');
        Schema::drop('notifications');
    }
}
