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
