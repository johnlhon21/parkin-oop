<?php
namespace App\Mail;

use App\Models\Affiliate;
use App\Models\Customer;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class VerificationMail extends Mailable
{
    use Queueable, SerializesModels;

    public $customer;

    public $link;

    public function __construct(Customer $affiliate, string $link)
    {
        $this->customer = $affiliate;
        $this->link = $link;
    }

    public function build()
    {
        return $this->subject('Innosys Account Verification')->view('emails.verify');
    }
}
