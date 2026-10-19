<?php

namespace Tests\Feature;

use App\Enums\CarStatus;
use App\Enums\InquiryStatus;
use App\Enums\UserRole;
use App\Models\Car;
use App\Models\CarCategory;
use App\Models\Inquiry;
use App\Models\Message;
use App\Models\User;
use Database\Seeders\UserSeeder;
use Illuminate\Database\QueryException;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Str;
use Tests\TestCase;

class FoundationTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->withoutVite();
    }

    public function test_demo_seeding_is_idempotent_and_does_not_reset_accounts(): void
    {
        config(['showroom.demo_password' => 'Demo-Only-Testing-123']);
        $this->seed(UserSeeder::class);
        $this->assertDatabaseCount('users', 6);
        $admin = User::where('email', 'admin@example.test')->firstOrFail();
        $this->assertSame(UserRole::Admin, $admin->role);
        $this->assertTrue(Hash::check('Demo-Only-Testing-123', $admin->password));
        $admin->forceFill(['is_active' => false, 'password' => 'Changed-Testing-123'])->save();
        $this->seed(UserSeeder::class);
        $this->assertDatabaseCount('users', 6);
        $this->assertFalse($admin->fresh()->is_active);
        $this->assertTrue(Hash::check('Changed-Testing-123', $admin->fresh()->password));
    }

    public function test_role_cannot_be_mass_assigned_from_customer_payload(): void
    {
        $user = User::factory()->make();
        $user->fill(['role' => UserRole::Admin, 'is_active' => false]);
        $user->save();
        $this->assertSame(UserRole::Customer, $user->fresh()->role);
        $this->assertTrue($user->fresh()->is_active);
    }

    public function test_domain_relationships_and_message_uuid_uniqueness(): void
    {
        $customer = User::factory()->create();
        $category = CarCategory::create(['brand' => 'Demo', 'name' => 'Demo', 'slug' => 'demo']);
        $car = Car::create([
            'category_id' => $category->id, 'code' => 'DEMO-1', 'slug' => 'demo-1',
            'name' => 'Demo only', 'year' => 2026, 'color' => 'Demo', 'price' => '1000000000',
        ]);
        $inquiry = Inquiry::create([
            'customer_id' => $customer->id, 'car_id' => $car->id,
            'contact_name' => 'Demo', 'contact_phone' => '0900000000', 'content' => 'Demo question',
        ]);
        $this->assertSame(CarStatus::Available, $car->fresh()->status);
        $this->assertSame(InquiryStatus::New, $inquiry->fresh()->status);
        $this->assertSame($car->id, $category->cars()->firstOrFail()->id);
        $uuid = (string) Str::uuid();
        $message = Message::create([
            'inquiry_id' => $inquiry->id, 'sender_id' => $customer->id,
            'content' => 'Demo', 'client_message_id' => $uuid,
        ]);
        $this->assertSame($customer->id, $message->sender->id);
        $this->assertSame($inquiry->id, $message->inquiry->id);
        $this->expectException(QueryException::class);
        Message::create($message->only(['inquiry_id', 'sender_id', 'content', 'client_message_id']));
    }

    public function test_active_and_role_middleware_block_wrong_and_locked_users(): void
    {
        Route::get('/foundation-admin-test', fn () => response()->json(['ok' => true]))
            ->middleware(['web', 'auth', 'ensure.active', 'role:admin']);
        $customer = User::factory()->create();
        $this->actingAs($customer)->getJson('/foundation-admin-test')->assertForbidden();
        $admin = User::factory()->create();
        $admin->forceFill(['role' => UserRole::Admin])->save();
        $this->actingAs($admin)->getJson('/foundation-admin-test')->assertOk();
        $admin->forceFill(['is_active' => false])->save();
        // ensure.active revokes the session of a locked account instead of leaving it signed in.
        $this->getJson('/foundation-admin-test')->assertForbidden();
        $this->assertGuest();
    }

    public function test_business_channel_is_denied_until_its_policy_is_implemented(): void
    {
        config([
            'broadcasting.default' => 'reverb',
            'broadcasting.connections.reverb.key' => 'testing-only-key',
            'broadcasting.connections.reverb.secret' => 'testing-only-secret',
            'broadcasting.connections.reverb.app_id' => 'testing-only-app',
        ]);
        require base_path('routes/channels.php');
        $user = User::factory()->create();
        $this->actingAs($user)->postJson('/broadcasting/auth', [
            'socket_id' => '123.456', 'channel_name' => 'private-inquiry.1.v1',
        ])->assertForbidden();
    }
}
