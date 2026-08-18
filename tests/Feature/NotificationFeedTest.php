<?php

namespace Tests\Feature;

use App\Models\BusinessInstance;
use App\Models\Cliente;
use App\Models\User;
use App\Models\UserNotification;
use App\Services\NotificationService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Auth;
use Tests\TestCase;

class NotificationFeedTest extends TestCase
{
    use RefreshDatabase;

    private BusinessInstance $instance;
    private array $team = [];

    protected function setUp(): void
    {
        parent::setUp();

        $this->instance = BusinessInstance::factory()->create([
            'setup_completed' => true,
        ]);

        for ($i = 0; $i < 3; $i++) {
            $this->team[] = User::factory()->create([
                'business_instance_id' => $this->instance->id,
            ]);
        }
    }

    public function test_feed_page_loads(): void
    {
        $this->actingAs($this->team[0]);

        $this->get(route('notifications.index'))
            ->assertOk();
    }

    public function test_notify_instance_creates_notifications_for_whole_team(): void
    {
        app(NotificationService::class)->notifyInstance(
            type: 'sale_created',
            category: 'sale',
            title: 'Venta registrada #00001',
            body: 'Se registró una venta por RD$ 1,500.00 a Cliente X',
            extra: [
                'icon' => 'bi-receipt',
                'color' => '#10b981',
                'action_url' => '/ventas/1',
                'category_icon' => 'bi-cart-check',
                'category_label' => 'Ventas',
                'verb' => 'registró la venta',
            ],
            tenantId: $this->instance->id,
            actor: $this->team[0],
        );

        $this->assertSame(3, UserNotification::count());

        $notif = UserNotification::where('user_id', $this->team[1]->id)->first();
        $this->assertSame('sale_created', $notif->type);
        $this->assertSame('registró la venta', $notif->action);
        $this->assertSame($this->instance->id, $notif->tenant_id);
        $this->assertSame($this->team[0]->name, $notif->actor_name);
        $this->assertNull($notif->read_at);
    }

    public function test_feed_endpoint_returns_items_and_unread_count(): void
    {
        app(NotificationService::class)->notifyInstance(
            type: 'payment_received',
            category: 'payment',
            title: 'Pago registrado',
            body: 'Pago de RD$ 500.00 vía efectivo',
            extra: ['category_label' => 'Pagos', 'verb' => 'registró un pago'],
            tenantId: $this->instance->id,
            actor: $this->team[0],
        );

        $user = $this->team[1];

        $this->actingAs($user);

        $this->getJson(route('api.notifications.unread-count'))
            ->assertOk()
            ->assertJson(['count' => 1, 'has_unread' => true]);

        $response = $this->getJson(route('api.notifications.feed') . '?limit=8')
            ->assertOk()
            ->assertJsonPath('unread_count', 1);

        $this->assertCount(1, $response->json('items'));
        $this->assertSame('Pago registrado', $response->json('items.0.title'));
        $this->assertSame($this->team[0]->name, $response->json('items.0.actor_name'));
        $this->assertFalse($response->json('items.0.read'));
    }

    public function test_mark_as_read_endpoint(): void
    {
        app(NotificationService::class)->notifyInstance(
            type: 'shift_opened',
            category: 'cash',
            title: 'Caja abierta',
            body: 'Caja principal abierta',
            extra: ['category_label' => 'Caja'],
            tenantId: $this->instance->id,
            actor: $this->team[0],
        );

        $user = $this->team[2];
        $this->actingAs($user);

        $notif = UserNotification::where('user_id', $user->id)->first();

        $this->putJson(route('api.notifications.mark-read', $notif->id))
            ->assertOk()
            ->assertJson(['success' => true]);

        $this->assertNotNull($notif->fresh()->read_at);

        $this->getJson(route('api.notifications.unread-count'))
            ->assertJson(['count' => 0]);
    }

    public function test_auditable_trait_generates_feed_entries(): void
    {
        $user = $this->team[0];
        $this->actingAs($user);

        Cliente::create([
            'nombre' => 'Cliente Feed',
            'tenant_id' => $this->instance->id,
            'activo' => true,
        ]);

        $feedEntries = UserNotification::where('type', 'cliente_created')->get();

        $this->assertCount(3, $feedEntries);

        $notif = $feedEntries->first();
        $this->assertSame('Cliente Feed', $notif->title);
        $this->assertSame($user->name, $notif->actor_name);
        $this->assertSame('creó', $notif->action);
    }

    public function test_stock_counter_update_does_not_generate_feed(): void
    {
        $user = $this->team[0];
        $this->actingAs($user);

        $cliente = Cliente::create([
            'nombre' => 'Cliente A',
            'tenant_id' => $this->instance->id,
            'activo' => true,
        ]);

        $cliente->decrement('balance_pendiente', 100);

        $this->assertSame(
            0,
            UserNotification::where('type', 'cliente_updated')->count(),
            'Actualizaciones de contadores no deben generar entradas de feed'
        );
    }
}