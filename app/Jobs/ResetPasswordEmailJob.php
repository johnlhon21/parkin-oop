<?php


namespace App\Jobs;


use App\Mail\PasswordResetMail;
use App\Models\Customer;
use App\Models\User;
use Illuminate\Support\Facades\Mail;

class ResetPasswordEmailJob extends Job
{
    protected $user;

    protected $code;

    public function __construct(User $user, $code)
    {
        $this->user = $user;
        $this->code = $code;
    }

    public function handle()
    {
        Mail::to($this->user->email)->send(new PasswordResetMail($this->user, $this->code));
    }
}
