<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class ImgInconsistencyMail extends Mailable
{
    use Queueable, SerializesModels;

    public $userName;
    public $data;
    public $img;
    public $threshold;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($userName, $data, $img, $threshold)
    {
        $this->userName = $userName;
        $this->data = $data;
        $this->img = $img;
        $this->threshold = $threshold;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->subject('Incohérence Indice de Masse Grasse (IMG) - Healing Nutrition')
                    ->view('emails.img_inconsistency');
    }
}
