<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;

class GuestListEmail extends Mailable
{
    use Queueable, SerializesModels;

    public $guestlist;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($guestlist)
    {
        $this->guestlist = $guestlist;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->from('jangan_reply@unijaya.com')->
        view('emails.guestlist')->
        with(['nama' => $this->guestlist]);
    }
}
