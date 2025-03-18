<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('formations', function (Blueprint $table) {
            $table->id();
            $table->string('niveau_formation')->nullable();
            $table->string('type_formation')->nullable();
            $table->string('mode_formation')->nullable();
            $table->string('creneau')->nullable();
            $table->dateTime("date_maj")->nullable();
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('formations');
    }
};

