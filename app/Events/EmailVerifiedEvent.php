<?php


namespace App\Events;


use App\Models\Customer;

class EmailVerifiedEvent extends Event
{
    public $customer;

    public function __construct(Customer $customer)
    {
        $this->customer = $customer;
    }
}
