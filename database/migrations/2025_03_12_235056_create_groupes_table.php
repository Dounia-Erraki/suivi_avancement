<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('groupes', function (Blueprint $table) {
            $table->id();
            $table->string('nom_groupe')->nullable();
            $table->integer('effectif_groupe')->nullable();
            $table->string('annee_formation')->nullable();
            $table->foreignId('filiere_id')->nullable()->constrained()->onDelete('cascade');
            $table->timestamps(); 
            $table->unique(['nom_groupe', 'filiere_id']);
        });
    }

    public function down()
    {
        Schema::dropIfExists('groupes');
    }
};