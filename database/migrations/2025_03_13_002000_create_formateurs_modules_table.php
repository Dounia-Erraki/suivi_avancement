<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('formateurs_modules', function (Blueprint $table) {
            $table->id();
            $table->foreignId('formateur_presentiel_id')->nullable()->constrained('formateurs')->onDelete('set null');
            $table->foreignId('formateur_sync_id')->nullable()->constrained('formateurs')->onDelete('set null');
            $table->foreignId('groupe_id')->constrained()->onDelete('cascade');
            $table->foreignId('module_id')->constrained()->onDelete('cascade');
            $table->float('mh_realisee_presentiel')->default(0.00);
            $table->float('mh_realisee_sync')->default(0.00);
            $table->float('mh_affectee_presentiel')->default(0.00);
            $table->float('mh_affectee_sync')->default(0.00);
            $table->timestamps();
            $table->unique(['groupe_id', 'module_id']);

        });
    }

    public function down()
    {
        Schema::dropIfExists('formateurs_modules');
    }
};
