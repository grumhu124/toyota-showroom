<?php

namespace Tests\Feature\Auth;

use App\Enums\UserRole;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Route;
use Tests\TestCase;

class AuthenticationTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->withoutVite();
    }

    private function registration(array $overrides = []): array
    {
        return array_merge([
            'name' => 'Khách Demo', 'email' => 'customer@example.test',
            'phone' => '0901234567', 'password' => 'DemoPassword123',
            'password_confirmation' => 'DemoPassword123',
        ], $overrides);
    }

    public function test_guest_can_open_forms_but_not_account(): void
    {
        $this->get('/dang-ky')->assertOk()->assertSee('Tạo tài khoản khách hàng');
        $this->get('/dang-nhap')->assertOk()->assertSee('Đăng nhập');
        $this->get('/tai-khoan')->assertRedirect('/dang-nhap');
        $this->getJson('/tai-khoan')->assertUnauthorized();
    }

    public function test_registration_logs_in_customer_and_ignores_privileged_fields(): void
    {
        $this->withSession(['_token' => 'before-registration']);
        $sessionId = session()->getId();
        $this->post('/dang-ky', $this->registration([
            'email' => ' CUSTOMER@EXAMPLE.TEST ', 'role' => 'admin', 'is_active' => false,
        ]))->assertRedirect('/tai-khoan');

        $user = User::sole();
        $this->assertSame(UserRole::Customer, $user->role);
        $this->assertTrue($user->is_active);
        $this->assertSame('customer@example.test', $user->email);
        $this->assertTrue(Hash::check('DemoPassword123', $user->password));
        $this->assertAuthenticatedAs($user);
        $this->assertNotSame($sessionId, session()->getId());
        $this->get('/tai-khoan')->assertOk()->assertSee('Khách Demo');
        $this->get('/dang-nhap')->assertRedirect('/tai-khoan');
    }

    public function test_invalid_registration_does_not_create_user_or_flash_password(): void
    {
        $this->from('/dang-ky')->post('/dang-ky', $this->registration([
            'name' => '', 'phone' => '+84901234567', 'password_confirmation' => 'wrong',
        ]))->assertRedirect('/dang-ky')->assertSessionHasErrors(['name', 'phone', 'password']);
        $this->assertDatabaseCount('users', 0);
        $this->assertGuest();
        $this->assertNull(session()->getOldInput('password'));
        $this->assertNull(session()->getOldInput('password_confirmation'));
    }

    public function test_duplicate_email_and_array_input_are_rejected(): void
    {
        User::factory()->create(['email' => 'customer@example.test']);
        $this->postJson('/dang-ky', $this->registration(['email' => 'CUSTOMER@EXAMPLE.TEST']))
            ->assertUnprocessable()->assertJsonValidationErrors('email');
        $this->postJson('/dang-ky', $this->registration(['email' => ['bad']]))
            ->assertUnprocessable()->assertJsonValidationErrors('email');
        $this->postJson('/dang-nhap', ['email' => ['bad'], 'password' => 'wrong'])
            ->assertUnprocessable()->assertJsonValidationErrors('email');
        $this->assertDatabaseCount('users', 1);
    }

    public function test_passwords_not_representable_by_bcrypt_are_rejected_before_hashing(): void
    {
        foreach ([str_repeat('a', 73), str_repeat('á', 37), "abcdefgh\0suffix"] as $password) {
            $this->postJson('/dang-ky', $this->registration([
                'password' => $password, 'password_confirmation' => $password,
            ]))->assertUnprocessable()->assertJsonValidationErrors('password');
            $this->postJson('/dang-nhap', ['email' => 'customer@example.test', 'password' => $password])
                ->assertUnprocessable()->assertJsonValidationErrors('password');
        }
        $this->assertDatabaseCount('users', 0);
    }

    public function test_login_normalizes_email_and_rotates_session_then_logout_invalidates_it(): void
    {
        $user = User::factory()->create(['email' => 'customer@example.test']);
        $this->withSession(['_token' => 'old-token']);
        $oldId = session()->getId();
        $this->post('/dang-nhap', ['email' => ' CUSTOMER@EXAMPLE.TEST ', 'password' => 'password'])
            ->assertRedirect('/tai-khoan');
        $this->assertAuthenticatedAs($user);
        $this->assertNotSame($oldId, session()->getId());
        $this->withSession(['private-marker' => 'must-go']);
        $token = session()->token();
        $this->post('/dang-xuat')->assertRedirect('/dang-nhap');
        $this->assertGuest();
        $this->assertNull(session('private-marker'));
        $this->assertNotSame($token, session()->token());
        $this->get('/dang-xuat')->assertMethodNotAllowed();
        $this->get('/tai-khoan')->assertRedirect('/dang-nhap');
    }

    public function test_fifth_failure_is_allowed_sixth_is_throttled_and_expires(): void
    {
        $credentials = ['email' => 'missing@example.test', 'password' => 'wrong-password'];
        for ($attempt = 0; $attempt < 5; $attempt++) {
            $this->postJson('/dang-nhap', $credentials)->assertUnprocessable();
        }
        $this->postJson('/dang-nhap', $credentials)->assertStatus(429);
        $this->postJson('/dang-nhap', ['email' => 'another@example.test', 'password' => 'wrong-password'])
            ->assertUnprocessable();
        $this->travel(61)->seconds();
        $this->postJson('/dang-nhap', $credentials)->assertUnprocessable();
    }

    public function test_success_clears_failure_counter_and_successes_are_not_counted(): void
    {
        User::factory()->create(['email' => 'customer@example.test']);
        $credentials = ['email' => 'customer@example.test', 'password' => 'wrong-password'];
        for ($attempt = 0; $attempt < 4; $attempt++) {
            $this->postJson('/dang-nhap', $credentials)->assertUnprocessable();
        }
        for ($attempt = 0; $attempt < 6; $attempt++) {
            $this->post('/dang-nhap', array_merge($credentials, ['password' => 'password']))
                ->assertRedirect('/tai-khoan');
            $this->post('/dang-xuat')->assertRedirect('/dang-nhap');
        }
        for ($attempt = 0; $attempt < 5; $attempt++) {
            $this->postJson('/dang-nhap', $credentials)->assertUnprocessable();
        }
        $this->postJson('/dang-nhap', $credentials)->assertStatus(429);
    }

    public function test_locked_account_cannot_login_even_with_correct_password(): void
    {
        $user = User::factory()->create();
        $user->forceFill(['is_active' => false])->save();
        $this->postJson('/dang-nhap', ['email' => $user->email, 'password' => 'password'])
            ->assertUnprocessable()->assertJsonValidationErrors('email');
        $this->assertGuest();
    }

    public function test_locking_an_existing_session_logs_it_out_on_public_request(): void
    {
        $user = User::factory()->create();
        $this->actingAs($user)->withSession(['private-marker' => 'must-go']);
        User::whereKey($user->id)->update(['is_active' => false]);
        $this->get('/')->assertRedirect('/dang-nhap')->assertSessionHas('account_error');
        $this->assertGuest();
        $this->assertNull(session('private-marker'));
        $this->get('/dang-nhap')->assertOk();
    }

    public function test_locked_json_request_revokes_session_and_channel_access(): void
    {
        $user = User::factory()->create();
        $this->actingAs($user);
        User::whereKey($user->id)->update(['is_active' => false]);
        $this->postJson('/broadcasting/auth', [
            'socket_id' => '123.456', 'channel_name' => 'private-inquiry.1.v1',
        ])->assertForbidden();
        $this->assertGuest();
    }

    public function test_existing_role_groups_are_protected_by_auth_and_role(): void
    {
        Route::middleware(['web', 'auth', 'ensure.active', 'role:admin'])
            ->get('/auth-test-admin', fn () => response()->noContent());
        Route::middleware(['web', 'auth', 'ensure.active', 'role:sales,admin'])
            ->get('/auth-test-staff', fn () => response()->noContent());

        $user = User::factory()->create();
        $this->actingAs($user)->get('/auth-test-admin')->assertForbidden();
        $this->get('/auth-test-staff')->assertForbidden();
        $user->forceFill(['role' => UserRole::Sales])->save();
        $this->actingAs($user)->get('/auth-test-admin')->assertForbidden();
        $this->get('/auth-test-staff')->assertNoContent();
        $user->forceFill(['role' => UserRole::Admin])->save();
        $this->actingAs($user)->get('/auth-test-admin')->assertNoContent();
    }
}
