<?php

// Autori: Mateja Milošević 2020/0487

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

/**
 * VerifikacijaIzmena – Mailable koji predstavlja mejl koji će se poslati prilikom verifikacije izmena podataka
 *
 * @version 1.0
 */
class VerifikacijaIzmena extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * @var string $hash Heš koji će se nalaziti u linku u mejlu
     */
    private $hash;

    /**
     * Konstruktor ovog Mailable objekta
     *
     * @param string $hash Heš koji će se nalaziti u linku u mejlu
     *
     * @return void
     */
    public function __construct(string $hash) {
        $this->hash = $hash;
    }

    public function envelope(): Envelope {
        return new Envelope(subject: "Zakazi - Verifikujte Izmene Podataka");
    }

    public function content(): Content {
        return new Content(view: "emails.izmena", with: [ "hash" => $this->hash ]);
    }
}
