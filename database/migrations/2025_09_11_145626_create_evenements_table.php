<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('evenements', function (Blueprint $table) {
            $table->id();
            $table->string('nom', 255);
            $table->text('description')->nullable();
            $table->string('type_evenement', 100);
            $table->dateTime('date_debut')->nullable();
            $table->dateTime('date_fin')->nullable();
            $table->string('type_acces', 50)->nullable();
            $table->string('lieu', 255)->nullable();
            $table->text('adresse')->nullable();
            $table->string('lien_en_ligne', 255)->nullable();
            $table->integer('capacite')->nullable();
            $table->string('type_tarification', 100)->nullable();
            $table->decimal('prix', 10, 2)->nullable();
            $table->string('image_principale', 255)->nullable();
            $table->string('lien_billetterie', 255)->nullable();
            $table->unsignedBigInteger('organisateur_id');
            $table->foreign('organisateur_id')->references('id')->on('users')->onDelete('cascade');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('evenements');
    }
};
