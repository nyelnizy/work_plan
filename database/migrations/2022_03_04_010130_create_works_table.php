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
        Schema::create('works', function (Blueprint $table) {
            $table->id();
            $table->string('subject');
            $table->dateTime('start_date');
            $table->tinyInteger('hours');
            $table->string('start_time');
            $table->string('end_time');
            $table->string('to');
            $table->string('from');
            $table->enum('work_type',['Plan','Status']);
            $table->longText('summary');
            $table->longText('content');
            $table->longText('original_content');
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
        Schema::dropIfExists('works');
    }
};
