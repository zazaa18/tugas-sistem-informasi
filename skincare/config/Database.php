<?php
class Database {
    public static function connect() {
        return mysqli_connect("localhost", "root", "", "skincare");
    }
}