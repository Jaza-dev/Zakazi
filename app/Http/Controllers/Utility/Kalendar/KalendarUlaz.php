<?php

// Autori: Mateja Milošević 2020/0487

namespace App\Http\Controllers\Utility\Kalendar;

define("KTIP_NERADNOVREME", 0);
define("KTIP_TERMIN", 1);

/**
 * KalendarUlaz – pomoćna struct-like klasa namenjena za konvertovanje u JSON format za potrebe renderovanja kalendara
 *
 * @version 1.0
 */
class KalendarUlaz {
    /**
     * @var string $title Naslov ovog ulaza u kalendaru
     */
    public $title;

    /**
     * @var int $start Unix vremenski pečat koji označava vreme početka ovog ulaza u kalendaru
     */
    public $start;

    /**
     * @var int $end Unix vremenski pečat koji označava vreme kraja ovog ulaza u kalendaru
     */
    public $end;

    /**
     * @var string $backgroundColor Boja ovog ulaza u kalendaru
     */
    public $backgroundColor;

    /**
     * @var array $extendedProps Dodatni podaci potrebni za vršenje interakcija nad ulazom u kalendaru
     */
    public $extendedProps;

    /**
     * Konstruktor ovog KalendarUlaz objekta
     *
     * @param string $title Naslov ovog ulaza u kalendaru
     * @param int $start Unix vremenski pečat koji označava vreme početka ovog ulaza u kalendaru
     * @param int $end Unix vremenski pečat koji označava vreme kraja ovog ulaza u kalendaru
     * @param string $backgroundColor Boja ovog ulaza u kalendaru
     * @param array $extendedProps Dodatni podaci potrebni za vršenje interakcija nad ulazom u kalendaru
     *
     * @return void
     */
    public function __construct(string $title, int $start, int $end, string $backgroundColor, array $extendedProps) {
        $this->title = $title;

        $this->start = $start;
        $this->end = $end;

        $this->backgroundColor = $backgroundColor;

        $this->extendedProps = $extendedProps;
    }
}
