<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCreatorsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('creators', function (Blueprint $table) {
            $table->id();
            $table->string('name')->unique();
            $table->integer('phone')->unique();
            $table->string('email')->nullable();
            $table->string('logo')->nullable();
            $table->boolean('status')->default(false);
            $table->string('description')->nullable();
            $table->string('location')->nullable();
            $table->string('delivery_options')->nullable();
            $table->string('payment_options')->nullable();
            $table->timestamp('verified_at')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('creators');
    }
}
