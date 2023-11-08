<?php

// Autori: Mateja Milošević 2020/0487

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

/**
 * PotvrdaZaposlenja – Mailable koji predstavlja mejl koji će se poslati prilikom slanja zahteva za zaposlenje
 *
 * @version 1.0
 */
class PotvrdaZaposlenja extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * @var string $biznis Biznis koji šalje ovaj zahtev za zaposlenje
     */
    private $biznis;

    /**
     * @var string $hash Heš koji će se nalaziti u linku u mejlu
     */
    private $hash;

    /**
     * Konstruktor ovog Mailable objekta
     *
     * @param string $biznis Biznis koji šalje ovaj zahtev za zaposlenje
     * @param string $hash Heš koji će se nalaziti u linku u mejlu
     *
     * @return void
     */
    public function __construct(string $biznis, string $hash) {
        $this->biznis = $biznis;
        $this->hash = $hash;
    }

    public function envelope(): Envelope {
        return new Envelope(subject: "Zakazi - Zahtev za Zaposlenje");
    }

    public function content(): Content {
        return new Content(view: "emails.zaposlenje", with: [
            "biznis" => $this->biznis,
            "hash" => $this->hash
        ]);
    }
}
