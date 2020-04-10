<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateUsersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->char('status', 1)->defult('A');

            $table->string('name', 150);
            $table->string('email', 190)->unique();
            $table->string('cellphone', 20)->nullable();
            $table->string('password', 90);
            $table->string('description', 255)->nullable();
            $table->string('verification_level', 150);
            $table->boolean('verified');
            
            $table->integer('number_of_reviews');

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
        Schema::dropIfExists('users');
    }
}
