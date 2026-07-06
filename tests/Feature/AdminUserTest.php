<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\SpxAccount;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class AdminUserTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Test that guest users cannot access admin user list.
     */
    public function test_guest_cannot_access_admin_users(): void
    {
        $response = $this->get(route('admin-users.index'));
        $response->assertRedirect(route('login'));
    }

    /**
     * Test that rider users cannot access admin user list.
     */
    public function test_rider_cannot_access_admin_users(): void
    {
        $rider = User::factory()->create(['role' => 'rider']);

        $response = $this->actingAs($rider)->get(route('admin-users.index'));
        $response->assertStatus(403);
    }

    /**
     * Test that admin users can view the admin list and creation form.
     */
    public function test_admin_can_access_admin_users_pages(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);

        $response = $this->actingAs($admin)->get(route('admin-users.index'));
        $response->assertOk();
        $response->assertSee('Admin Users');

        $responseCreate = $this->actingAs($admin)->get(route('admin-users.create'));
        $responseCreate->assertOk();
        $responseCreate->assertSee('Add New Admin');
    }

    /**
     * Test that an admin can create another admin user.
     */
    public function test_admin_can_create_new_admin_user(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);

        $response = $this->actingAs($admin)->post(route('admin-users.store'), [
            'name' => 'Second Admin',
            'email' => 'second@admin.com',
            'password' => 'password123',
            'password_confirmation' => 'password123',
        ]);

        $response->assertRedirect(route('admin-users.index'));
        $this->assertDatabaseHas('users', [
            'name' => 'Second Admin',
            'email' => 'second@admin.com',
            'role' => 'admin',
        ]);

        $newAdmin = User::where('email', 'second@admin.com')->first();
        $this->assertTrue(Hash::check('password123', $newAdmin->password));
    }

    /**
     * Test that admins have separate scoped data (multi-tenancy).
     */
    public function test_admins_have_isolated_multi_tenant_data(): void
    {
        $admin1 = User::factory()->create(['role' => 'admin']);
        $admin2 = User::factory()->create(['role' => 'admin']);

        // Create SPX Account under Admin 1 (by acting as Admin 1)
        $account1 = $this->actingAs($admin1)->post(route('spx-accounts.store'), [
            'account_code' => 'SPX-A1',
            'account_name' => 'Admin 1 Hub',
            'notes' => 'Admin 1 notes',
            'is_active' => true,
        ]);

        // Create SPX Account under Admin 2 (by acting as Admin 2)
        $account2 = $this->actingAs($admin2)->post(route('spx-accounts.store'), [
            'account_code' => 'SPX-A2',
            'account_name' => 'Admin 2 Hub',
            'notes' => 'Admin 2 notes',
            'is_active' => true,
        ]);

        // Assert they both exist in the database with their respective admin IDs
        $this->assertDatabaseHas('spx_accounts', [
            'account_code' => 'SPX-A1',
            'admin_id' => $admin1->id,
        ]);
        $this->assertDatabaseHas('spx_accounts', [
            'account_code' => 'SPX-A2',
            'admin_id' => $admin2->id,
        ]);

        // Accessing the list as Admin 1 - should ONLY see SPX-A1
        $response1 = $this->actingAs($admin1)->get(route('spx-accounts.index'));
        $response1->assertSee('SPX-A1');
        $response1->assertDontSee('SPX-A2');

        // Accessing the list as Admin 2 - should ONLY see SPX-A2
        $response2 = $this->actingAs($admin2)->get(route('spx-accounts.index'));
        $response2->assertSee('SPX-A2');
        $response2->assertDontSee('SPX-A1');
    }

    /**
     * Test that an admin can edit and update another admin user.
     */
    public function test_admin_can_edit_and_update_another_admin(): void
    {
        $admin1 = User::factory()->create(['role' => 'admin']);
        $admin2 = User::factory()->create(['role' => 'admin']);

        // View edit page
        $responseEdit = $this->actingAs($admin1)->get(route('admin-users.edit', $admin2));
        $responseEdit->assertOk();
        $responseEdit->assertSee('Edit Admin User');

        // Submit update request
        $responseUpdate = $this->actingAs($admin1)->put(route('admin-users.update', $admin2), [
            'name' => 'Updated Admin Name',
            'email' => 'updated@admin.com',
        ]);
        $responseUpdate->assertRedirect(route('admin-users.index'));

        $this->assertDatabaseHas('users', [
            'id' => $admin2->id,
            'name' => 'Updated Admin Name',
            'email' => 'updated@admin.com',
        ]);
    }

    /**
     * Test that an admin can reset another admin's password to "password".
     */
    public function test_admin_can_reset_password_of_another_admin(): void
    {
        $admin1 = User::factory()->create(['role' => 'admin']);
        $admin2 = User::factory()->create(['role' => 'admin', 'password' => Hash::make('original_password')]);

        $responseReset = $this->actingAs($admin1)->post(route('admin-users.reset-password', $admin2));
        $responseReset->assertRedirect(route('admin-users.index'));

        $admin2->refresh();
        $this->assertTrue(Hash::check('password', $admin2->password));
    }
}
