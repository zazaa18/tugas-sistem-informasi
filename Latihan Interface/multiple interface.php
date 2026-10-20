<?php
interface Mesin {
    public function hidupkan();
}

interface Kendaraan {
    public function berjalan();
}

class Motor implements Mesin, Kendaraan {
    public function hidupkan() {
        return "Mesin hidup";
    }

    public function berjalan() {
        return "Motor berjalan";
    }
}
?>