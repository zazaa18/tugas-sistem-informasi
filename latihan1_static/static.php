<?php

class Counter {
    
    public static $jumlah = 0;

    public function tambah() {
        
        self::$jumlah++;
    }
}


$c1 = new Counter();

$c2 = new Counter();


$c1->tambah();

$c2->tambah();

echo Counter::$jumlah;

?>