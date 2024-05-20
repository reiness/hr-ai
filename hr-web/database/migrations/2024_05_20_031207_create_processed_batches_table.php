<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateProcessedBatchesTable extends Migration
{
    public function up()
    {
        Schema::create('processed_batches', function (Blueprint $table) {
            $table->id();
            $table->foreignId('batch_id')->constrained()->onDelete('cascade');
            $table->string('name'); // Add name column
            $table->string('user_input'); // Add user_input column
            $table->string('status');
            $table->json('result')->nullable();
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('processed_batches');
    }
}
