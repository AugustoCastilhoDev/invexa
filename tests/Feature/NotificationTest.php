<?php

namespace Tests\Feature;

use App\Models\Company;
use App\Models\Product;
use App\Models\User;
use App\Notifications\LowStockNotification;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Notification;
use Tests\TestCase;

class NotificationTest extends TestCase
{
    use RefreshDatabase;

    public function test_low_stock_notification_is_sent(): void
    {
        Notification::fake();

        $company = Company::factory()->create();
        $user    = User::factory()->create(['company_id' => $company->id, 'role' => 'admin']);
        $product = Product::factory()->create(['company_id' => $company->id, 'quantity' => 3, 'min_stock' => 5]);

        $user->notify(new LowStockNotification($product));

        Notification::assertSentTo($user, LowStockNotification::class);
    }

    public function test_notifications_page_requires_auth(): void
    {
        $this->get(route('notifications.index'))
            ->assertRedirect(route('login'));
    }
}
