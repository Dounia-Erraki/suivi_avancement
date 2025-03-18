<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('modules', function (Blueprint $table) {
            $table->id();
            $table->string('code_module')->nullable();
            $table->string('nom_module')->nullable();
            $table->integer('nb_cc')->nullable();
            $table->enum('regional',['O','N'])->nullable();
            $table->boolean('EFM_realisation')->default(false);
            $table->boolean('EFM_validation')->default(false);
            $table->float('MHT_total')->nullable();
            $table->float('MHT_presentiel_s1')->nullable();
            $table->float('MHT_sync_s1')->nullable();
            $table->float('MHT_presentiel_s2')->nullable();
            $table->float('MHT_sync_s2')->nullable();
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('modules');
    }
};

