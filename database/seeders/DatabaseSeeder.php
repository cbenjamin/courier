<?php

namespace Database\Seeders;

use App\Models\Order;
use App\Models\Profile;
use App\Models\Setting;
use App\Models\Subscription;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // Default settings
        Setting::set('adhoc_price_cents', 2500);
        Setting::set('subscription_price_cents', 7900);
        Setting::set('contact_email', 'chris@chrisbenjamin.co');

        // Admin user
        $admin = User::create([
            'name' => 'Admin User',
            'email' => 'admin@wiregrasscourier.com',
            'password' => Hash::make('password'),
            'email_verified_at' => now(),
            'is_admin' => true,
        ]);
        Profile::create(['user_id' => $admin->id]);

        // Regular customers
        $customers = [];
        foreach ([
            ['name' => 'Mary Johnson', 'email' => 'mary@example.com'],
            ['name' => 'Bob Williams', 'email' => 'bob@example.com'],
            ['name' => 'Sarah Davis', 'email' => 'sarah@example.com'],
        ] as $data) {
            $user = User::create([
                ...$data,
                'password' => Hash::make('password'),
                'email_verified_at' => now(),
            ]);
            Profile::create([
                'user_id' => $user->id,
                'phone' => '(251) 555-' . rand(1000, 9999),
                'address' => rand(100, 9999) . ' County Road ' . rand(1, 99),
                'city' => collect(['Evergreen', 'Greenville', 'Brewton', 'Monroeville'])->random(),
                'state' => 'AL',
                'zip' => '3' . rand(6000, 6999),
            ]);
            $customers[] = $user;
        }

        // Active subscription for Mary
        $subscription = Subscription::create([
            'user_id' => $customers[0]->id,
            'stripe_subscription_id' => 'sub_seed_' . uniqid(),
            'status' => 'active',
            'orders_used' => 2,
            'period_start' => now()->startOfMonth(),
            'period_end' => now()->endOfMonth(),
        ]);

        // Orders for Mary (subscription)
        foreach (range(1, 2) as $i) {
            Order::create([
                'user_id'         => $customers[0]->id,
                'subscription_id' => $subscription->id,
                'type'            => 'subscription',
                'status'          => $i === 1 ? Order::STATUS_DELIVERED : Order::STATUS_CONFIRMED,
                'pickup_link'     => 'https://www.amazon.com/gp/buy/thankyou/handlers/display.html?orderID=114-seed-00' . $i,
                'pickup_time'     => now()->subDays($i * 5),
                'delivery_address' => $customers[0]->profile->address,
                'delivery_city'   => $customers[0]->profile->city,
                'delivery_state'  => 'AL',
                'delivery_zip'    => $customers[0]->profile->zip,
                'notes'           => 'Please leave at the back door.',
            ]);
        }

        // Ad-hoc orders for Bob
        Order::create([
            'user_id'                 => $customers[1]->id,
            'type'                    => 'adhoc',
            'status'                  => Order::STATUS_PENDING,
            'pickup_link'             => 'https://www.amazon.com/gp/buy/thankyou/handlers/display.html?orderID=114-seed-003',
            'pickup_time'             => now()->addDays(2),
            'delivery_address'        => $customers[1]->profile->address,
            'delivery_city'           => $customers[1]->profile->city,
            'delivery_state'          => 'AL',
            'delivery_zip'            => $customers[1]->profile->zip,
            'amount_cents'            => 2500,
            'stripe_payment_intent_id' => 'pi_seed_' . uniqid(),
        ]);

        Order::create([
            'user_id'          => $customers[1]->id,
            'type'             => 'adhoc',
            'status'           => Order::STATUS_PICKED_UP,
            'pickup_link'      => 'https://www.amazon.com/gp/buy/thankyou/handlers/display.html?orderID=114-seed-004',
            'pickup_time'      => now()->subDay(),
            'delivery_address' => $customers[1]->profile->address,
            'delivery_city'    => $customers[1]->profile->city,
            'delivery_state'   => 'AL',
            'delivery_zip'     => $customers[1]->profile->zip,
            'amount_cents'     => 2500,
        ]);
    }
}
