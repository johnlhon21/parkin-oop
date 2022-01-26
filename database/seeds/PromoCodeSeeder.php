<?php

use \Illuminate\Database\Seeder;

class PromoCodeSeeder extends Seeder
{
    public function run() {


        $data = [

            [
                'plan_code' => 'SPA',
                'promo_code' => 'WHATAPPS001',
                'type' => 'whatsapp.my',
                'max_credit' => 5,
                'start_date' => '2020-12-01',
                'expired_date' => '2020-12-31',
            ],

            [
                'plan_code' => 'SPB',
                'promo_code' => 'WHATAPPS002',
                'type' => 'whatsapp.sg',
                'max_credit' => 5,
                'start_date' => '2020-12-01',
                'expired_date' => '2020-12-31',
            ],

            [
                'plan_code' => 'BSPA',
                'promo_code' => 'SMS001',
                'type' => 'sms.my',
                'max_credit' => 5,
                'start_date' => '2020-12-01',
                'expired_date' => '2020-12-31',
            ],

            [
                'plan_code' => 'BSPB',
                'promo_code' => 'SMS002',
                'type' => 'sms.sg',
                'max_credit' => 5,
                'start_date' => '2020-12-01',
                'expired_date' => '2020-12-31',
            ],

            [
                'plan_code' => 'BE',
                'promo_code' => 'EMAIL001',
                'type' => 'email',
                'max_credit' => 10,
                'start_date' => '2020-12-01',
                'expired_date' => '2020-12-31',
            ],
        ];

        foreach ($data as $datum) {
            \App\Models\PromoCode::firstOrCreate($datum);
        }
    }
}
