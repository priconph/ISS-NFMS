<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;

class SendNFMSMail extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public $data;
    public $warning_data;
    public $full_space_data;
    public $subject;
    
    public function __construct($data, $warning_data, $full_space_data, $subject)
    {
        //
        $this->data = $data;
        $this->warning_data = $warning_data;
        $this->full_space_data = $full_space_data;
        $this->subject = $subject;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        $data = $this->data;
        $warning_data = $this->warning_data;
        $full_space_data = $this->full_space_data;
        $subject = $this->subject;

        return $this->view('mail.iss_nfms', compact([
            'data',
            'warning_data',
            'full_space_data',
        ]))->subject($subject);
    }
}
