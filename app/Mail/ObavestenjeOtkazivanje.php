<?php

// Autori: Mateja Milošević 2020/0487

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

/**
 * ObavestenjeOtkazivanje – Mailable koji predstavlja mejl koji će se poslati prilikom otkazivanja termina od strane
 * biznisa
 *
 * @version 1.0
 */
class ObavestenjeOtkazivanje extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * @var string $biznis Biznis koji otkazuje termin
     */
    private $biznis;

    /**
     * @var string $zaposleni Zaposleni čiji se termin otkazuje
     */
    private $zaposleni;

    /**
     * @var string $vreme Vremenski period u kome je termin bio zakazan (datum i vreme)
     */
    private $vreme;

    /**
     * Konstruktor ovog Mailable objekta
     *
     * @param string $biznis Biznis koji šalje ovaj zahtev za zaposlenje
     * @param string $zaposleni Zaposleni čiji se termin otkazuje
     * @param string $vreme Vremenski period u kome je termin bio zakazan (datum i vreme)
     *
     * @return void
     */
    public function __construct(string $biznis, string $zaposleni, string $vreme) {
        $this->biznis = $biznis;
        $this->zaposleni = $zaposleni;
        $this->vreme = $vreme;
    }

    public function envelope(): Envelope {
        return new Envelope(subject: "Zakazi - Otkazan Termin");
    }

    public function content(): Content {
        return new Content(view: "emails.otkazivanje", with: [
            "biznis" => $this->biznis,
            "zaposleni" => $this->zaposleni,
            "vreme" => $this->vreme
        ]);
    }
}
