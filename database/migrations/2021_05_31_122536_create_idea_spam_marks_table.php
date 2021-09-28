<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateIdeaSpamMarksTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('idea_spam_marks', function (Blueprint $table) {
            $table->id();

            $table->foreignId('user_id')->onDelete('cascade');
            $table->foreignId('idea_id')->onDelete('cascade');

            $table->unique(['user_id', 'idea_id']);

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
        Schema::dropIfExists('idea_spam_marks');
    }
}
