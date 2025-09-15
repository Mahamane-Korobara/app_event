<?php

namespace Tests\Feature;

use Tests\TestCase; // Pour les tests 
use Illuminate\Foundation\Testing\RefreshDatabase; // Pour reintialiser la BDD entre chaque test
use Illuminate\Http\UploadedFile; // Pour simuler les uploads de fichiers dans les tests
use Illuminate\Support\Facades\Storage;
use Laravel\Sanctum\Sanctum; // Pour simuler un utilisateur connecté avec sanctum

use App\Models\User; 
use App\Models\Ambiance;
use App\Models\Tag;
use App\Models\Evenement;

use Database\Seeders\AmbiancesSeeder;
use Database\Seeders\TagsSeeder;

class EventCreationTest extends TestCase
{
    use RefreshDatabase;

    public function test_un_utilisateur_connecte_peut_creer_un_evenement()
    {
        // Simule le disque 'public' pour éviter d'écrire sur le système de fichiers réel
        Storage::fake('public');

        // Crée un utilisateur (factory User doit exister) qui est dejà present par defaut 
        $user = User::factory()->create();

        // Réinjecte les ambiances et tags via les seeders 
        $this->seed(AmbiancesSeeder::class);
        $this->seed(TagsSeeder::class);

        // Récupère quelques ids d'ambiances/tags existants
        $ambiances = Ambiance::pluck('id')->take(2)->toArray();
        $tags = Tag::pluck('id')->take(2)->toArray();

        // Crée un fichier image factice
        $file = UploadedFile::fake()->image('event.jpg');

        // Authentifie l'utilisateur pour Sanctum
        Sanctum::actingAs($user);

        // Prépare la payload (même noms de données que dans le FormRequest)
        $payload = [
            'nom' => 'Soirée Test',
            'description' => 'Une super soirée test',
            'type_evenement' => 'musique',
            'date_debut' => now()->addDays(5)->format('Y-m-d H:i:s'),
            'date_fin' => now()->addDays(5)->addHours(4)->format('Y-m-d H:i:s'),
            'type_acces' => 'presentiel',
            'lieu' => 'Salle centrale',
            'adresse' => 'Rue exemple 123',
            'lien_en_ligne' => "https://example.com/event",
            'capacite' => 200,
            'type_tarification' => 'payant',
            'prix' => 150.00,
            'image_principale' => $file,           // fichier uploadé
            'lien_billetterie' => null,
            'ambiances' => $ambiances,
            'tags' => $tags,
        ];

        // Envoi la requête POST
        $response = $this->post('/api/events', $payload);

        // Vérifications
        $response->assertStatus(201)
                 ->assertJson(['message' => 'Evenement créé avec succès']);

        // BDD : l'événement existe et appartient à l'utilisateur
        $this->assertDatabaseHas('evenements', [
            'nom' => 'Soirée Test',
            'organisateur_id' => $user->id
        ]);

        // Fichier : l'image a été stockée (chemin dépend de la logique de stockage dans le EventController)
        Storage::disk('public')->assertExists('images/' . $file->hashName());

        // Pivots : vérifie que les liens ambiances/tags existent dans les tables pivot
        $eventId = Evenement::where('nom', 'Soirée Test')->first()->id;
        foreach ($ambiances as $a) {
            $this->assertDatabaseHas('evenement_ambiance', [
                'evenement_id' => $eventId,
                'ambiance_id' => $a,
            ]);
        }
        foreach ($tags as $t) {
            $this->assertDatabaseHas('evenement_tag', [
                'evenement_id' => $eventId,
                'tag_id' => $t,
            ]);
        }
    }
}
