<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class RthWarningMail extends Mailable
{
    use Queueable, SerializesModels;

    public $userName;
    public $rth;
    public $gender;
    public $threshold;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($userName, $rth, $gender, $threshold)
    {
        $this->userName = $userName;
        $this->rth = $rth;
        $this->gender = $gender;
        $this->threshold = $threshold;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->subject('Alerte Santé : Votre Rapport Taille/Hanche (RTH) - Healing Nutrition')
                    ->view('emails.rth_warning');
    }
}
