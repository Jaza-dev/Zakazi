<?php

// Autori: Mateja Milošević 2020/0487

namespace App\Models;

use App\Models\Korisnici\BiznisModel;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * TipBiznisaModel – klasa ORM modela TipBiznisa tabele u bazi
 *
 * @version 1.0
 */
class TipBiznisaModel extends Model
{
    use HasFactory;

    protected $table = "tipbiznisa";
    protected $primaryKey = "idTipBiznisa";

    protected $fillable = [
        "naziv"
    ];

    public $timestamps = false;

    /**
     * Dohvatanje svih biznisa koji imaju ovaj tip biznisa
     *
     * @return HasMany
     */
    public function biznisi() {
        return $this->hasMany(BiznisModel::class, "TipBiznisa_idTipBiznisa", "idTipBiznisa");
    }
}
