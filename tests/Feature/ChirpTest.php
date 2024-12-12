<?php

namespace Tests\Feature;

use App\Models\Chirp;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class ChirpTest extends TestCase
{
    /**
     * A basic feature test example.
     */
    // Exercice 1
    public function test_un_utilisateur_peut_creer_un_chirp()
    {
        // Simuler un utilisateur connecté
        $utilisateur = User::factory()->create();
        $this->actingAs($utilisateur);
        // Envoyer une requête POST pour créer un chirp
        $reponse = $this->post('/chirps', [
            'message' => 'Mon premier chirp !'
        ]);
        // Vérifier que le chirp a été ajouté à la base de données
        $reponse->assertStatus(302);
        $this->assertDatabaseHas('chirps', [
            'message' => 'Mon premier chirp !',
            'user_id' => $utilisateur->id,
        ]);
    }
    // Exercice 2
    public function test_un_chirp_ne_peut_pas_avoir_un_contenu_vide()
    {
        $utilisateur = User::factory()->create();
        $this->actingAs($utilisateur);

        $response = $this->post('/chirps', [
            'message' => '',
        ]);

        $response->assertSessionHasErrors(['message']);
    }

    public function test_un_chirp_ne_peut_pas_depasse_255_caracteres()
    {
        $utilisateur = User::factory()->create();
        $this->actingAs($utilisateur);

        $response = $this->post('/chirps', [
            'message' => str_repeat('a', 256),
        ]);

        $response->assertSessionHasErrors(['message']);
    }

    // Exercice 3
    // public function test_les_chirps_sont_affiches_sur_la_page_d_accueil()
    // {
    //     $chirps = Chirp::factory()->count(3)->create();

    //     $response = $this->get('/');

    //     foreach ($chirps as $chirp) {
    //         $response->assertSee($chirp->contenu);
    //     }
    // }

    // Exercice 4
    public function test_un_utilisateur_peut_modifier_son_chirp()
    {
        $utilisateur = User::factory()->create();
        $chirp = Chirp::factory()->create(['user_id' => $utilisateur->id]);

        $this->actingAs($utilisateur);

        $response = $this->put("/chirps/{$chirp->id}", [
            'message' => 'Chirp modifié',
        ]);

        $response->assertStatus(302);

        $this->assertDatabaseHas('chirps', [
            'id' => $chirp->id,
            'message' => 'Chirp modifié',
        ]);
    }

    // Exercie 5
    public function test_un_utilisateur_peut_supprimer_son_chirp()
    {
        $utilisateur = User::factory()->create();
        $chirp = Chirp::factory()->create(['user_id' => $utilisateur->id]);

        $this->actingAs($utilisateur);

        $response = $this->delete("/chirps/{$chirp->id}");

        $response->assertStatus(302);

        $this->assertDatabaseMissing('chirps', [
            'id' => $chirp->id,
        ]);
    }

    // Exercice 6
    public function test_un_utilisateur_ne_peut_pas_modifier_ou_supprimer_le_chirp_d_un_autre_utilisateur()
    {
        $user1 = User::factory()->create();
        $user2 = User::factory()->create();
        $chirp = Chirp::factory()->create(['user_id' => $user1->id]);

        $this->actingAs($user2);

        $responseUpdate = $this->put("/chirps/{$chirp->id}", [
            'message' => 'Modification interdite',
        ]);
        $responseUpdate->assertStatus(403);

        $responseDelete = $this->delete("/chirps/{$chirp->id}");
        $responseDelete->assertStatus(403);
    }

    // Exercice 7
    public function test_un_chirp_mis_a_jour_ne_peut_pas_avoir_un_contenu_vide()
    {
        $utilisateur = User::factory()->create();
        $chirp = Chirp::factory()->create(['user_id' => $utilisateur->id]);

        $this->actingAs($utilisateur);

        $response = $this->put("/chirps/{$chirp->id}", [
            'message' => '',
        ]);

        $response->assertSessionHasErrors(['message']);
    }

    public function test_un_chirp_mis_a_jour_ne_peut_pas_depasse_255_caracteres()
    {
        $utilisateur = User::factory()->create();
        $chirp = Chirp::factory()->create(['user_id' => $utilisateur->id]);

        $this->actingAs($utilisateur);

        $response = $this->put("/chirps/{$chirp->id}", [
            'message' => str_repeat('a', 256),
        ]);

        $response->assertSessionHasErrors(['message']);
    }

}
