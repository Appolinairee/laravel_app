<?php

namespace App\Mail\Auth;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\URL;

class VerifyEmailMail extends Mailable
{
    use Queueable, SerializesModels;
    
    public $username;
    public $url;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($user)
    {
        $generate = URL::temporarySignedRoute('email.verify', now()->addMinutes(30), ['email' => $user->email]);
        $url = str_replace(env('URL_BACKEND'), env('URL_FRONTEND'), $generate);

        $this->url = $url;
        $this->username = $user->name;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->markdown('mail.verifyemail');
    }
}
