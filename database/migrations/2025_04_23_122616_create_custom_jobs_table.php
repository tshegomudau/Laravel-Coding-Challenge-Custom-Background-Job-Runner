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
        Schema::create('custom_jobs', function (Blueprint $table) {
            $table->id();
            $table->string('class');
            $table->string('method');
            $table->text('params')->nullable();
            $table->text('chain')->nullable();
            $table->integer('priority')->default(5);
            $table->timestamp('run_at')->nullable();
            $table->timestamp('started_at')->nullable();
            $table->timestamp('finished_at')->nullable();
            $table->text('error')->nullable();
            $table->tinyInteger('status')->default(0); // 0 = pending, 1 = running, 2 = complete, 3 = failed
            $table->integer('retries')->default(0);
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
        Schema::dropIfExists('custom_jobs');
    }
};
