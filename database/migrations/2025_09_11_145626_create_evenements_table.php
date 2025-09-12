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
            $table->string('type_evenement', 100);        //ex: musique, conference, etc.
            $table->dateTime('date_debut')->nullable();
            $table->dateTime('date_fin')->nullable();
            $table->string('type_acces', 50)->nullable();  //ex: presentiel, en-ligne, hybride
            $table->string('lieu', 255)->nullable();
            $table->text('adresse')->nullable();
            $table->string('lien_en_ligne', 255)->nullable();
            $table->integer('capacite')->nullable();
            $table->string('type_tarification', 100)->nullable();  //ex: gratuit, payant
            $table->decimal('prix', 10, 2)->nullable();
            $table->string('image_principale', 255)->nullable();
            $table->string('lien_billetterie', 255)->nullable();
            $table->unsignedBigInteger('organisateur_id');
            $table->foreign('organisateur_id')->references('id')->on('users')->onDelete('cascade');
            $table->timestamps();


            // Index pour accélérer les recherches
            $table->index(['date_debut', 'date_fin'], 'idx_evenements_date');
            $table->index('type_evenement', 'idx_evenements_type');
            $table->index('type_acces', 'idx_evenements_acces');
            $table->index('prix', 'idx_evenements_prix');
            $table->index('lieu', 'idx_evenements_lieu');
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
