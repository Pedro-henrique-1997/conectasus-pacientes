<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use App\Models\HealthUnit;
use Tests\TestCase;

class PatientApiTest extends TestCase
{
    use RefreshDatabase;

    public function test_pode_cadastrar_um_paciente(): void
    {
        $healthUnit = HealthUnit::create([
            'uuid' => fake()->uuid(),
            'name' => 'Unidade de Saúde Teste',
            'city' => 'Rio de Janeiro',
        ]);

        $response = $this->postJson('/api/patients', [
            'name' => 'João da Silva',
            'cpf' => '12345678901',
            'birth_date' => '1995-05-20',
            'phone' => '21999999999',
            'health_unit_id' => $healthUnit->id,
        ]);

        $response->assertStatus(201);

        $this->assertDatabaseHas('patients', [
            'name' => 'João da Silva',
            'cpf' => '12345678901',
            'health_unit_id' => $healthUnit->id,
        ]);
    }


public function test_nao_pode_cadastrar_pacientes_com_cpf_duplicado(): void
{
    $healthUnit = HealthUnit::create([
        'uuid' => fake()->uuid(),
        'name' => 'Unidade de Saúde Teste',
        'city' => 'Rio de Janeiro',
    ]);

    $patientData = [
        'name' => 'João da Silva',
        'cpf' => '12345678901',
        'birth_date' => '1995-05-20',
        'phone' => '21999999999',
        'health_unit_id' => $healthUnit->id,
    ];

    $this->postJson('/api/patients', $patientData)
        ->assertStatus(201);

    $response = $this->postJson('/api/patients', [
        ...$patientData,
        'name' => 'Maria da Silva',
    ]);

    $response->assertStatus(422);

    $this->assertDatabaseCount('patients', 1);
}


public function test_nao_pode_cadastrar_paciente_com_unidade_inexistente(): void
{
    $response = $this->postJson('/api/patients', [
        'name' => 'Carlos Souza',
        'cpf' => '98765432100',
        'birth_date' => '1990-03-15',
        'phone' => '21988887777',
        'health_unit_id' => 99999,
    ]);

    $response->assertStatus(422);

    $response->assertJsonValidationErrors([
        'health_unit_id',
    ]);

    $this->assertDatabaseCount('patients', 0);
}


public function test_pode_consultar_a_lista_de_pacientes(): void
{
    $healthUnit = HealthUnit::create([
        'uuid' => fake()->uuid(),
        'name' => 'Unidade de Saúde Teste',
        'city' => 'Rio de Janeiro',
    ]);

    $this->postJson('/api/patients', [
        'name' => 'Ana Souza',
        'cpf' => '11122233344',
        'birth_date' => '1998-07-10',
        'phone' => '21999999999',
        'health_unit_id' => $healthUnit->id,
    ])->assertStatus(201);

    $response = $this->getJson('/api/patients');

    $response->assertOk();

    $response->assertJsonFragment([
        'name' => 'Ana Souza',
        'cpf' => '11122233344',
    ]);
}

public function test_pode_atualizar_um_paciente(): void
{
    $healthUnit = HealthUnit::create([
        'uuid' => fake()->uuid(),
        'name' => 'Unidade de Saúde Teste',
        'city' => 'Rio de Janeiro',
    ]);

    $createResponse = $this->postJson('/api/patients', [
        'name' => 'João da Silva',
        'cpf' => '12345678901',
        'birth_date' => '1995-05-20',
        'phone' => '21999999999',
        'health_unit_id' => $healthUnit->id,
    ]);

    $createResponse->assertStatus(201);

    $patientId = $createResponse->json('data.id');

    $response = $this->putJson("/api/patients/{$patientId}", [
        'name' => 'João Silva Atualizado',
        'cpf' => '12345678901',
        'birth_date' => '1995-05-20',
        'phone' => '21988887777',
        'health_unit_id' => $healthUnit->id,
    ]);

    $response->assertOk();

    $this->assertDatabaseHas('patients', [
        'id' => $patientId,
        'name' => 'João Silva Atualizado',
        'phone' => '21988887777',
    ]);
}

public function test_pode_excluir_um_paciente(): void
{
    $healthUnit = HealthUnit::create([
        'uuid' => fake()->uuid(),
        'name' => 'Unidade de Saúde Teste',
        'city' => 'Rio de Janeiro',
    ]);

    $createResponse = $this->postJson('/api/patients', [
        'name' => 'Maria Souza',
        'cpf' => '32165498700',
        'birth_date' => '1992-08-15',
        'phone' => '21977776666',
        'health_unit_id' => $healthUnit->id,
    ]);

    $createResponse->assertStatus(201);

    $patientId = $createResponse->json('data.id');

    $response = $this->deleteJson("/api/patients/{$patientId}");

    $response->assertOk();

    $this->assertDatabaseMissing('patients', [
        'id' => $patientId,
    ]);
}


public function test_nao_pode_cadastrar_paciente_com_cpf_invalido(): void
{
    $healthUnit = HealthUnit::create([
        'uuid' => fake()->uuid(),
        'name' => 'Unidade de Saúde Teste',
        'city' => 'Rio de Janeiro',
    ]);

    $response = $this->postJson('/api/patients', [
        'name' => 'Carlos Souza',
        'cpf' => '12345',
        'birth_date' => '1990-03-15',
        'phone' => '21988887777',
        'health_unit_id' => $healthUnit->id,
    ]);

    $response->assertStatus(422);

    $response->assertJsonValidationErrors(['cpf']);

    $this->assertDatabaseCount('patients', 0);
}


}

