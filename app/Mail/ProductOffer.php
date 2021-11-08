<?php
namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;

class ProductOffer extends Mailable
{
    use Queueable, SerializesModels;

    private $details;

    public function __construct($details)
    {
        $this->details = $details;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->markdown('emails.productoffer')
                    ->from('marketing@pestcare.co.id')
                    ->attach('marketing/Compro_SIP_Pest_Control.pdf')
                    ->with([
                        'name' => $this->details['name'],
                        'email' => $this->details['email']
                        ]);
    }


}
