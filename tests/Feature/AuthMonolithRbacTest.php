<?php

use App\Models\User;

test('1. user baru dapat mendaftar melalui /register dan memiliki default role user', function () {
    $response = $this->post('/register', [
        'name' => 'Budi Santoso',
        'email' => 'budi@example.com',
        'password' => 'password123',
        'password_confirmation' => 'password123',
    ]);

    $response->assertRedirect(route('dashboard', absolute: false));
    $this->assertAuthenticated();

    $user = User::where('email', 'budi@example.com')->first();
    expect($user)->not->toBeNull();
    expect($user->role)->toBe('user');
    expect($user->isUser())->toBeTrue();
    expect($user->isAdmin())->toBeFalse();
});

test('2. user biasa dapat login melalui /login', function () {
    $user = User::factory()->create([
        'email' => 'loginuser@example.com',
        'password' => bcrypt('password123'),
        'role' => 'user',
    ]);

    $response = $this->post('/login', [
        'email' => 'loginuser@example.com',
        'password' => 'password123',
    ]);

    $this->assertAuthenticatedAs($user);
    $response->assertRedirect(route('dashboard', absolute: false));
});

test('3. user biasa dapat mengakses /user/dashboard', function () {
    $user = User::factory()->create([
        'role' => 'user',
    ]);

    $response = $this->actingAs($user)->get('/user/dashboard');

    $response->assertStatus(200);
});

test('4. user biasa mengakses /admin/dashboard menghasilkan 403 Forbidden', function () {
    $user = User::factory()->create([
        'role' => 'user',
    ]);

    $response = $this->actingAs($user)->get('/admin/dashboard');

    $response->assertStatus(403);
});

test('5. admin dapat login dengan kredensial yang valid', function () {
    $admin = User::factory()->create([
        'email' => 'admin_test@example.com',
        'password' => bcrypt('password123'),
        'role' => 'admin',
    ]);

    $response = $this->post('/login', [
        'email' => 'admin_test@example.com',
        'password' => 'password123',
    ]);

    $this->assertAuthenticatedAs($admin);
});

test('6. admin dapat mengakses /admin/dashboard dengan sukses dan melihat statistik', function () {
    $admin = User::factory()->create([
        'role' => 'admin',
    ]);

    $response = $this->actingAs($admin)->get('/admin/dashboard');

    $response->assertStatus(200);
    $response->assertSee('Dashboard Admin');
    $response->assertSee('Statistik Pengguna');
    $response->assertSee('Total Pengguna');
});

test('7. admin dapat melihat daftar pengguna pada dashboard admin', function () {
    $admin = User::factory()->create(['role' => 'admin']);
    $user = User::factory()->create(['name' => 'Peserta Uji Coba', 'role' => 'user']);

    $response = $this->actingAs($admin)->get('/admin/dashboard');

    $response->assertStatus(200);
    $response->assertSee('Daftar Pengguna');
    $response->assertSee('Peserta Uji Coba');
});

test('8. admin dapat mengubah role pengguna lain', function () {
    $admin = User::factory()->create(['role' => 'admin']);
    $targetUser = User::factory()->create(['role' => 'user']);

    $response = $this->actingAs($admin)->patch("/admin/users/{$targetUser->id}/role", [
        'role' => 'admin',
    ]);

    $response->assertRedirect(route('admin.dashboard'));
    expect($targetUser->fresh()->role)->toBe('admin');
});

test('9. admin dapat menghapus pengguna lain', function () {
    $admin = User::factory()->create(['role' => 'admin']);
    $targetUser = User::factory()->create(['role' => 'user']);

    $response = $this->actingAs($admin)->delete("/admin/users/{$targetUser->id}");

    $response->assertRedirect(route('admin.dashboard'));
    expect(User::find($targetUser->id))->toBeNull();
});

test('10. admin tidak dapat menghapus akunnya sendiri', function () {
    $admin = User::factory()->create(['role' => 'admin']);

    $response = $this->actingAs($admin)->delete("/admin/users/{$admin->id}");

    $response->assertRedirect(route('admin.dashboard'));
    $response->assertSessionHas('error');
    expect(User::find($admin->id))->not->toBeNull();
});

test('11. user biasa tidak dapat mengubah role pengguna lain', function () {
    $user = User::factory()->create(['role' => 'user']);
    $targetUser = User::factory()->create(['role' => 'user']);

    $response = $this->actingAs($user)->patch("/admin/users/{$targetUser->id}/role", [
        'role' => 'admin',
    ]);

    $response->assertStatus(403);
    expect($targetUser->fresh()->role)->toBe('user');
});

test('12. user biasa tidak dapat menghapus pengguna lain', function () {
    $user = User::factory()->create(['role' => 'user']);
    $targetUser = User::factory()->create(['role' => 'user']);

    $response = $this->actingAs($user)->delete("/admin/users/{$targetUser->id}");

    $response->assertStatus(403);
    expect(User::find($targetUser->id))->not->toBeNull();
});
