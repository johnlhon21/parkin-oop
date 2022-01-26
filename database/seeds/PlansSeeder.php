<?php

use \Illuminate\Database\Seeder;

class PlansSeeder extends Seeder
{
    public function run() {

        $data = [
            [
                'code' => 'FP',
                'name' => 'Free Plan',
                'description' => 'Promo Code Subscription, Credit depends on promo code',
                'price' => 0,
                'currency' => 'USD',
                'max_credit' => 0,
                'is_enable' => true,
            ],
        ];

        // NOTE = Monthly subscription plans already transferred to STRIPE Dashboard Product Management

//        $data = [
//
//            [
//                'code' => 'SPA',
//                'name' => 'Standard Plan A (MY)',
//                'description' => 'Monthly Subscription, 500 Max Credit Monthly, WhatsApp Malaysia',
//                'price' => 50,
//                'currency' => 'USD',
//                'max_credit' => 500,
//                'is_enable' => true,
//            ],
//
//            [
//                'code' => 'SPB',
//                'name' => 'Standard Plan B (SG)',
//                'description' => 'Monthly Subscription, 500 Max Credit Monthly, WhatsApp Singapore',
//                'price' => 50,
//                'currency' => 'USD',
//                'max_credit' => 500,
//                'is_enable' => true,
//            ],
//
//
//
//            [
//                'code' => 'BSPA',
//                'name' => 'Bulk SMS Plan A (MY)',
//                'description' => 'Bulk SMS to Malaysia, 500 Max Credit Monthly',
//                'price' => 50,
//                'currency' => 'USD',
//                'max_credit' => 500,
//                'is_enable' => true,
//            ],
//
//            [
//                'code' => 'BSPB',
//                'name' => 'Bulk SMS Plan A (SG)',
//                'description' => 'Bulk SMS to Singapore, 500 Max Credit Monthly',
//                'price' => 50,
//                'currency' => 'USD',
//                'max_credit' => 500,
//                'is_enable' => true,
//            ],
//
//            [
//                'code' => 'BE',
//                'name' => 'Bulk Email',
//                'description' => 'Bulk Sending Email, 10,000 Max Credit Monthly',
//                'price' => 50,
//                'currency' => 'USD',
//                'max_credit' => 1000,
//                'is_enable' => true,
//            ],
//
//        ];

        foreach ($data as $datum) {
            \App\Models\Plan::firstOrCreate($datum);
        }
    }
}
