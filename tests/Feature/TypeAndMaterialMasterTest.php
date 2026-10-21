<?php

namespace Tests\Feature;

use App\Models\Material;
use App\Models\Type;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class TypeAndMaterialMasterTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        // Clean up test data
        DB::table('material_type')->delete();
        Material::whereIn('branch_id', [8881, 8882])->delete();
        Type::whereIn('branch_id', [8881, 8882])->delete();
    }

    protected function tearDown(): void
    {
        DB::table('material_type')->delete();
        Material::whereIn('branch_id', [8881, 8882])->delete();
        Type::whereIn('branch_id', [8881, 8882])->delete();

        parent::tearDown();
    }

    private function createTestUser(int $branchId): User
    {
        $user = new User();
        $user->id = $branchId;
        $user->branch_id = $branchId;
        $user->role = 'sub-admin';
        $user->name = "Branch $branchId Admin";

        return $user;
    }

    public function test_type_crud_and_validation()
    {
        $user = $this->createTestUser(8881);
        Sanctum::actingAs($user, ['*']);

        // 1. Create Type
        $response = $this->postJson('/api/types', [
            'type_name' => 'Copper',
        ]);

        $response->assertStatus(201)
            ->assertJson([
                'status'  => true,
                'message' => 'Type created successfully.',
            ])
            ->assertJsonPath('data.type_name', 'Copper')
            ->assertJsonPath('data.branch_id', 8881)
            ->assertJsonPath('data.is_deleted', false);

        $typeId = $response->json('data.id');

        // 2. Duplicate Type in Same Branch -> Validation Error
        $dupResponse = $this->postJson('/api/types', [
            'type_name' => 'Copper',
        ]);

        $dupResponse->assertStatus(422)
            ->assertJson([
                'status' => false,
            ]);

        // 3. Same Type in Different Branch -> Succeeds
        $user2 = $this->createTestUser(8882);
        Sanctum::actingAs($user2, ['*']);
        $diffBranchResponse = $this->postJson('/api/types', [
            'type_name' => 'Copper',
        ]);

        $diffBranchResponse->assertStatus(201)
            ->assertJsonPath('data.branch_id', 8882);

        // 4. List Types for Branch 8881 (Should not include 8882)
        Sanctum::actingAs($user, ['*']);
        $listResponse = $this->getJson('/api/types');

        $listResponse->assertStatus(200)
            ->assertJson([
                'status' => true,
            ]);

        $this->assertCount(1, $listResponse->json('data'));
        $this->assertEquals('Copper', $listResponse->json('data.0.type_name'));

        // 5. Update Type
        $updateResponse = $this->putJson("/api/types/{$typeId}", [
            'type_name' => 'Pure Copper',
        ]);

        $updateResponse->assertStatus(200)
            ->assertJsonPath('data.type_name', 'Pure Copper');

        // 6. Show Type
        $showResponse = $this->getJson("/api/types/{$typeId}");

        $showResponse->assertStatus(200)
            ->assertJsonPath('data.type_name', 'Pure Copper');

        // 7. Soft Delete Type
        $delResponse = $this->deleteJson("/api/types/{$typeId}");

        $delResponse->assertStatus(200)
            ->assertJson([
                'status'  => true,
                'message' => 'Type deleted successfully.',
            ]);

        // Verify type is excluded from listing
        $listAfterDel = $this->getJson('/api/types');
        $this->assertCount(0, $listAfterDel->json('data'));

        // Verify database has is_deleted = 1
        $this->assertDatabaseHas('types', [
            'id'         => $typeId,
            'is_deleted' => 1,
        ]);
    }

    public function test_material_crud_with_types_relation()
    {
        $user = $this->createTestUser(8881);
        Sanctum::actingAs($user, ['*']);

        // Seed 2 types
        $copper = Type::create([
            'type_name'  => 'Copper Type',
            'branch_id'  => 8881,
            'is_deleted' => 0,
        ]);

        $tambu = Type::create([
            'type_name'  => 'Tambu Type',
            'branch_id'  => 8881,
            'is_deleted' => 0,
        ]);

        // 1. Validation error when missing required fields
        $invalidResp = $this->postJson('/api/materials', []);

        $invalidResp->assertStatus(422)
            ->assertJsonValidationErrors(['name', 'price', 'type_ids']);

        // 2. Validation error when type does not exist
        $invalidTypeResp = $this->postJson('/api/materials', [
            'name'     => 'Lokhand',
            'price'    => 120.50,
            'type_ids' => [999999],
        ]);

        $invalidTypeResp->assertStatus(422);

        // 3. Create Material with both types
        $createResp = $this->postJson('/api/materials', [
            'name'     => 'Lokhand Ingot',
            'price'    => 125.50,
            'type_ids' => [$copper->id, $tambu->id],
        ]);

        $createResp->assertStatus(201)
            ->assertJson([
                'status'  => true,
                'message' => 'Material created successfully.',
            ])
            ->assertJsonPath('data.name', 'Lokhand Ingot')
            ->assertJsonPath('data.price', '125.50')
            ->assertJsonPath('data.branch_id', 8881);

        $matId = $createResp->json('data.id');

        // Check pivot table
        $this->assertDatabaseHas('material_type', [
            'material_id' => $matId,
            'type_id'     => $copper->id,
        ]);
        $this->assertDatabaseHas('material_type', [
            'material_id' => $matId,
            'type_id'     => $tambu->id,
        ]);

        // 4. Show Material includes related types (id + type_name) and type_ids
        $showResp = $this->getJson("/api/materials/{$matId}");

        $showResp->assertStatus(200)
            ->assertJsonPath('data.id', $matId)
            ->assertJsonPath('data.name', 'Lokhand Ingot')
            ->assertJsonPath('data.types.0.id', $copper->id)
            ->assertJsonPath('data.types.0.type_name', 'Copper Type')
            ->assertJsonPath('data.types.1.id', $tambu->id)
            ->assertJsonPath('data.types.1.type_name', 'Tambu Type');

        $this->assertEquals([$copper->id, $tambu->id], $showResp->json('data.type_ids'));

        // 5. Update Material - re-sync only 1 type and update price
        $updateResp = $this->putJson("/api/materials/{$matId}", [
            'name'     => 'Lokhand Heavy Ingot',
            'price'    => 150.00,
            'type_ids' => [$copper->id],
        ]);

        $updateResp->assertStatus(200)
            ->assertJsonPath('data.name', 'Lokhand Heavy Ingot')
            ->assertJsonPath('data.price', '150.00');

        // Verify pivot table updated
        $this->assertDatabaseHas('material_type', [
            'material_id' => $matId,
            'type_id'     => $copper->id,
        ]);
        $this->assertDatabaseMissing('material_type', [
            'material_id' => $matId,
            'type_id'     => $tambu->id,
        ]);

        // 6. Search Material by name or type
        $searchResp = $this->getJson('/api/materials?search=Heavy');

        $searchResp->assertStatus(200);
        $this->assertCount(1, $searchResp->json('data'));

        // 7. Soft Delete Material
        $delResp = $this->deleteJson("/api/materials/{$matId}");

        $delResp->assertStatus(200)
            ->assertJson([
                'status'  => true,
                'message' => 'Material deleted successfully.',
            ]);

        $this->assertDatabaseHas('materials', [
            'id'         => $matId,
            'is_deleted' => 1,
        ]);

        // 8. Verify excluded from normal listing
        $listAfterDel = $this->getJson('/api/materials');

        $this->assertCount(0, $listAfterDel->json('data'));
    }
}
