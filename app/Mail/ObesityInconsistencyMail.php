<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class ObesityInconsistencyMail extends Mailable
{
    use Queueable, SerializesModels;

    public $userName;
    public $data; // Contains weight, height, etc.
    public $imc;
    public $calcGrade;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($userName, $data, $imc, $calcGrade)
    {
        $this->userName = $userName;
        $this->data = $data;
        $this->imc = $imc;
        $this->calcGrade = $calcGrade;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->subject('Incohérence détectée - Healing Nutrition')
                    ->view('emails.check_obesity_consistency');
    }
}
