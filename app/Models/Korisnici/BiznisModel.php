<?php

// Autori: Mateja Milošević 2020/0487
//         Miloš Paunović 2018/0294

namespace App\Models\Korisnici;

use Illuminate\Database\Eloquent\Builder;
use App\Models\TipBiznisaModel;
use App\Models\Traits\KorisnikTrait;
use App\Models\ZaposlenjeModel;
use App\Models\TerminModel;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Database\Eloquent\Collection;

/**
 * BiznisModel – klasa ORM modela Biznis tabele u bazi
 *
 * @version 1.0
 */
class BiznisModel extends Authenticatable
{
    use KorisnikTrait;

    protected $table = "biznis";
    protected $primaryKey = "Korisnik_idKor";

    protected $fillable = [
        "zvanicnoIme",
        "brojTelefona",
        "imeVlasnika",
        "prezimeVlasnika",
        "PIB",
        "opis"
    ];

    protected $guarded = [
        "verifikovan",
        "noviEmail",
        "novoZvanicnoIme",
        "potvrdioIzmene"
    ];

    public $timestamps = false;

    /**
     * Dohvatanje tipa biznisa ovog biznisa
     *
     * @return BelongsTo
     */
    public function tipBiznisa() {
        return $this->belongsTo(TipBiznisaModel::class, "TipBiznisa_idTipBiznisa", "idTipBiznisa");
    }

    /**
     * Dohvatanje svih zapisa o zaposlenju vezanih za ovaj biznis
     *
     * @return HasMany
     */
    public function zaposlenja() {
        return $this->hasMany(ZaposlenjeModel::class, "Biznis_Korisnik_idKor", "Korisnik_idKor");
    }

    /**
     * Dohvatanje svih zaposlenih ovog biznisa
     *
     * @return ZaposleniModel[]
     */
    public function zaposleni() {
        // Nažalost, ne može kao Eloquent relacija bez menjanja baze

        $res = [];

        foreach ($this->zaposlenja as $zaposlenje) {
            if ($zaposlenje->zaposleni != null) {
                $res[] = $zaposlenje->zaposleni;
            }
        }

        return $res;
    }

    /**
     * Dohvatanje svih termina vezanih za ovaj biznis
     *
     * @return HasMany
     */
    public function termini() {
        return $this->hasMany(TerminModel::class, "Biznis_Korisnik_idKor", "Korisnik_idKor");
    }

    /**
     * Dohvatanje biznisa po kriterijumima pretrage
     * BiznisModel-u se dodaje polje prosečne ocene korisnika
     * 
     * @param String $imeBiznisa ime biznisa ili tipa biznisa po kojem se pretražuje
     * @param String $sortiraj kriterijum po kojem se sortira rezultat, moguće vrednosti poOceni i poImenu
     * @param Boolean $testirajVerifikovan da li se pretražuju svi ili samo verifikovani biznisi, ako vrednost nije postavljena, pretražuju se samo verifikovani
     * 
     * @return BiznisModel
     */
    public static function pretraga($imeBiznisa, $sortiraj, $testirajVerifikovan = true) {
        $pomocniBuilder = BiznisModel::svi();

        if(!empty($imeBiznisa))
            $pomocniBuilder = $pomocniBuilder->where('zvanicnoime', 'like', '%'.$imeBiznisa.'%')
                                             ->orWhereHas('tipBiznisa', function(Builder $query) use($imeBiznisa) {
                                                    $query->where('naziv', 'like', $imeBiznisa);
                                            });
        
        if($sortiraj=='poImenu')
            $pomocniBuilder = $pomocniBuilder->orderBy('zvanicnoIme');
        else
            $pomocniBuilder = $pomocniBuilder->orderBy('prosecnaOcena', 'desc');
        
        if($testirajVerifikovan==false) {
            return $pomocniBuilder->get();
        }

        $prikaz = new Collection();
        foreach ($pomocniBuilder->get() as $biznis) {
            if ($biznis->aktivan == 1 && $biznis->verifikovan == 1) {
                $prikaz->add($biznis);
            }
        }

        return $prikaz;
    }

    /**
     * Dohvatanje svih biznisa, umesto all()
     * 
     * @return Builder
     */
    public static function svi() {
        return BiznisModel::withAvg('termini as prosecnaOcena', 'ocenaKorisnika');
    }
}
