<?php
    function dbconnect()
    {
        static $connect = null;
        if ($connect === null) {
            $connect = mysqli_connect('localhost', 'ETU004259', 'S9Ullo17', 'db_s2_ETU004259');
            if (!$connect) {
                die('Erreur de connexion à la base de données : ' . mysqli_connect_error());
            }
            mysqli_set_charset($connect, 'utf8mb4');
        }
        return $connect;
    }
?>
