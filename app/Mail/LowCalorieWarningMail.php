<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class LowCalorieWarningMail extends Mailable
{
    use Queueable, SerializesModels;

    public $userName;
    public $data;
    public $threshold;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($userName, $data, $threshold)
    {
        $this->userName = $userName;
        $this->data = $data;
        $this->threshold = $threshold;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->subject('Alerte : Apport calorique insuffisant détecté - Healing Nutrition')
                    ->view('emails.low_calorie_warning');
    }
}
