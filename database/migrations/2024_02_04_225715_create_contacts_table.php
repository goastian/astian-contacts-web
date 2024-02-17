<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('contacts', function (Blueprint $table) {
            $table->uuid('id')->unique()->index();
            $table->string('name', 100);
            $table->string('last_name', 100);
            $table->string('address', 150)->nullable();
            $table->string('company', 150)->nullable();
            $table->boolean('favorite')->default(0)->index();
            $table->uuid('group_id')->nullable()->index();
            $table->uuid('user_id')->index();
            $table->timestamps();
            $table->primary('id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('contacts');
    }
};
