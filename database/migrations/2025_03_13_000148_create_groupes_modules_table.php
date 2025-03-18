<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('groupes_modules', function (Blueprint $table) {
            $table->id();
            $table->foreignId('groupe_id')->constrained()->onDelete('cascade');
            $table->foreignId('module_id')->constrained()->onDelete('cascade');
            $table->float('MHT_presentiel_realisees')->nullable();
            $table->float('MHT_sync_realisees')->nullable();
            $table->timestamps();
            $table->unique(['groupe_id', 'module_id']);
        });
    }

    public function down()
    {
        Schema::dropIfExists('groupes_modules');
    }
};
