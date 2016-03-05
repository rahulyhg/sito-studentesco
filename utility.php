<?php
session_cache_limiter(false);
session_start();

require_once __DIR__ . '/parameters.php';
require_once __DIR__ . '/vendor/autoload.php';
require_once __DIR__ . '/vendor/setasign/fpdf/fpdf.php';
require_once __DIR__ . '/vendor/ircmaxell/password-compat/lib/password.php';

// Impostazioni
// Accesso di default al database
// $mysql = new mysqli('localhost', $username, $password);
// if ($mysql->connect_error) {
//     die('Connection failed: ' . $mysql->connect_error);
// }
// $mysql->query('CREATE DATABASE IF NOT EXISTS ' . $tabella);


// Accesso alla tabella del progetto
require_once __DIR__ . '/templates/shared/default.php';

$dati = array (
    'info' => array ('path' => $indirizzo . '/templates/', 'root' => $indirizzo . '/', 'email' => 'email@gmail.com', 
        'sito' => 'Sito studentesco'), 

    'opzioni' => array ('snow' => false, 'time' => false, 'cookie-policy' => false, 'percorso' => false), 
    
    'sezioni' => array ('corsi' => true, 'citazioni' => true, 'aule' => false, 'forum' => true), 
    
    'database' => database($username, $password, $tipo, $tabella), 'debug' => false);

if (isUserAutenticate()) {
    $dati['user'] = id($dati['database']);
    $dati['first'] = first($dati['database'], $dati['user']);
    $dati['autogestione'] = $dati['database']->max('autogestioni', 'id');
}

if ($dati['opzioni']['time']) {
    $mtime = microtime();
    $mtime = explode(' ', $mtime);
    $mtime = $mtime[1] + $mtime[0];
    $starttime = $mtime;
}

unset($tipo);
unset($tabella);
unset($username);
unset($password);

// CREAZIONE TABELLE NECESSARIE


// Identificazione e funzionamento
// $dati['database']->query('CREATE TABLE IF NOT EXISTS admins (id int)');
// $dati['database']->query(
//         'CREATE TABLE IF NOT EXISTS persone (id int AUTO_INCREMENT PRIMARY KEY, username varchar(255), nome varchar(255), password varchar(255), email varchar(255), stato int, verificata int, random int, inviata int)');
// $dati['database']->query(
//         'CREATE TABLE IF NOT EXISTS sessioni (tipo_browser varchar(255), indirizzo varchar(255), data datetime)');
// $dati['database']->query(
//         'CREATE TABLE IF NOT EXISTS accessi (id int, tipo_browser varchar(255), indirizzo varchar(255), data datetime)');
// $dati['database']->query('CREATE TABLE IF NOT EXISTS scuole (id int AUTO_INCREMENT PRIMARY KEY, nome varchar(255))');
// $dati['database']->query(
//         'CREATE TABLE IF NOT EXISTS autogestioni (id int AUTO_INCREMENT PRIMARY KEY, nome varchar(255), data date)');
// $dati['database']->query(
//         'CREATE TABLE IF NOT EXISTS classi (id int AUTO_INCREMENT PRIMARY KEY, scuola int, nome varchar(255))');
// $dati['database']->query('CREATE TABLE IF NOT EXISTS studenti (id int, classe int, persona int)');
// $dati['database']->query('CREATE TABLE IF NOT EXISTS ean (persona int, ean varchar(20))');
// $dati['database']->query(
//         'CREATE TABLE IF NOT EXISTS news (id int AUTO_INCREMENT PRIMARY KEY, titolo varchar(255), classe int, contenuto varchar(2500), creatore int, stato int(1), da int, data datetime)');


// Corsi e proposte
// $dati['database']->query(
//         'CREATE TABLE IF NOT EXISTS corsi (id int AUTO_INCREMENT PRIMARY KEY, autogestione int, scuola int, nome varchar(255), descrizione varchar(2500), aule varchar(1000), quando varchar(255), max int, creatore int, stato int(1), da int, controllore int, data datetime)');
// $dati['database']->query('CREATE TABLE IF NOT EXISTS iscrizioni (persona int, corso int, stato int(1))');
// $dati['database']->query(
//         'CREATE TABLE IF NOT EXISTS squadre (id int AUTO_INCREMENT PRIMARY KEY, nome varchar(255), torneo int, by int)');
// $dati['database']->query('CREATE TABLE IF NOT EXISTS giocatori (squadra int, persona int)');
//
// $dati['database']->query('CREATE TABLE IF NOT EXISTS like (corso int, persona int)');
// $dati['database']->query('CREATE TABLE IF NOT EXISTS registro (corso int, persona int, da int)');
// $dati['database']->query('CREATE TABLE IF NOT EXISTS max (torneo int, max int)');


// Aule studio
// $dati['database']->query(
//         'CREATE TABLE IF NOT EXISTS aule (id int AUTO_INCREMENT PRIMARY KEY, nome varchar(255), descrizione varchar(2500), dove varchar(1000), quanto int, max int, creatore int, stato int(1), da int, data datetime)');
// $dati['database']->query('CREATE TABLE IF NOT EXISTS pomeriggio (persona int, aula int, stato int(1))');


// Citazioni
// $dati['database']->query(
//         'CREATE TABLE IF NOT EXISTS citazioni (id int AUTO_INCREMENT PRIMARY KEY, prof int, descrizione varchar(2500), creatore int, stato int(1), da int, data datetime)');
// $dati['database']->query(
//         'CREATE TABLE IF NOT EXISTS profs (id int AUTO_INCREMENT PRIMARY KEY, nome varchar(255), creatore int, stato int(1), da int)');
// $dati['database']->query('CREATE TABLE IF NOT EXISTS voti (persona int, citazione int, stato int(1))');


// Forum
// $dati['database']->query(
//         'CREATE TABLE IF NOT EXISTS posts (id int AUTO_INCREMENT PRIMARY KEY, articolo int, number int, answer int, content varchar(10000), da int, stato int(1), creatore int, data datetime)');
// $dati['database']->query(
//         'CREATE TABLE IF NOT EXISTS articoli (id int AUTO_INCREMENT PRIMARY KEY, categoria int, nome varchar(255), closed int, stato int(1), creatore int, da int, data datetime)');
// $dati['database']->query(
//         'CREATE TABLE IF NOT EXISTS categorie (id int AUTO_INCREMENT PRIMARY KEY, tipo int, nome varchar(255), stato int(1), creatore int, da int, data datetime)');
// $dati['database']->query(
//         'CREATE TABLE IF NOT EXISTS tipi (id int AUTO_INCREMENT PRIMARY KEY, nome varchar(255), stato int(1), creatore int, da int, data datetime)');


// Completamento tabelle di opzioni e codice a barre
// $results = $dati['database']->select('persone', '');
// $dati['database']->query(
//         'CREATE TABLE IF NOT EXISTS opzioni (id int, newsletter int(1), mode int(1), rap int(1), news int(1), style varchar(255))');
// if ($results != null) {
//     foreach ($results as $result) {
//         $dati['database']->insert('opzioni',
//                 array ('newsletter' => 1, 'mode' => 1, 'rap' => 1, 'news' => 1, 'style' => 'bootstrap', 'id' => $result['id']));
//     }
// }
// $results = $dati['database']->select('persone', '');
// $number = 123456789012;
// if ($results != null) {
//     foreach ($results as $result) {
//         $dati['database']->insert('ean', array ('persona' => $result['id'], 'ean' => $number));
//         $number ++;
//     }
// }


if (!isset($_SESSION['counter'])) {
    $dati['database']->insert('sessioni', 
            array ('tipo_browser' => getenv('HTTP_USER_AGENT'), 'indirizzo' => getenv('REMOTE_ADDR'), '#data' => 'NOW()'));
    $_SESSION['counter'] = 'ok';
}
?>