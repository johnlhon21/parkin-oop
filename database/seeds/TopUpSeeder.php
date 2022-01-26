<?php
use \Illuminate\Database\Seeder;

class TopUpSeeder extends Seeder
{
    public function run()
    {
        $data = [

            [
                'uuid' => \Ramsey\Uuid\Uuid::uuid1()->toString(),
                'name' => 'TOPUP WHATAPPS001',
                'type' => 'whatsapp.my',
                'max_credit' => 500,
                'currency' => 'usd',
                'price' => 50,
            ],

            [
                'uuid' => \Ramsey\Uuid\Uuid::uuid1()->toString(),
                'name' => 'TOPUP WHATAPPS002',
                'type' => 'whatsapp.sg',
                'max_credit' => 500,
                'currency' => 'usd',
                'price' => 50,
            ],

            [
                'uuid' => \Ramsey\Uuid\Uuid::uuid1()->toString(),
                'name' => 'TOPUP SMS001',
                'type' => 'sms.my',
                'max_credit' => 500,
                'currency' => 'usd',
                'price' => 50,
            ],

            [
                'uuid' => \Ramsey\Uuid\Uuid::uuid1()->toString(),
                'name' => 'TOPUP SMS002',
                'type' => 'sms.sg',
                'max_credit' => 500,
                'currency' => 'usd',
                'price' => 50,
            ],

            [
                'uuid' => \Ramsey\Uuid\Uuid::uuid1()->toString(),
                'name' => 'TOPUP EMAIL001',
                'type' => 'email',
                'max_credit' => 100000,
                'currency' => 'usd',
                'price' => 50,
            ],
        ];

        foreach ($data as $datum) {
            \App\Models\TopUp::firstOrCreate($datum);
        }
    }
}
