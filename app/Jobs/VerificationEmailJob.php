<?php


namespace App\Jobs;


use App\Mail\VerificationMail;
use App\Models\Affiliate;
use App\Models\Customer;
use App\Models\User;
use Illuminate\Support\Facades\Mail;

class VerificationEmailJob extends Job
{
    protected $customer;

    protected $code;

    public function __construct(Customer $affiliate, $code)
    {
        $this->customer = $affiliate;
        $this->code = $code;
    }

    public function handle()
    {
        $link = env('DASHBOARD_URL') . '/verify?q=' . $this->code;
        Mail::to($this->customer->email)->send(new VerificationMail($this->customer, $link));
    }
}
