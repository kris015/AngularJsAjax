<?php

require_once('baza.php');
require_once('biblioteka.php');

if (!isset($_POST['komanda'])) {
  greskaURadu('Poziv nije izvršen na regularan način!');
} else {
  $komanda = $_POST['komanda'];
}

switch ($komanda) {
  case 'citanje':
    $izlazniXML = new SimpleXMLElement('<?xml version="1.0" encoding="utf-8"?><ljudi></ljudi>');

    $rezultat = $konekcija->prepare("SELECT id, imePrezime, telefon FROM spisak ORDER BY imePrezime");
    $rezultat->execute();

    while ($red = $rezultat->fetch(PDO::FETCH_ASSOC)) {
      $covek = $izlazniXML->addChild('covek');
      $covek->addChild('id', $red['id']);
      $covek->addChild('imePrezime', $red['imePrezime']);
      $covek->addChild('telefon', $red['telefon']);
    }
    break;

  case 'upis':
    if (!(isset($_POST['imePrezime']) && isset($_POST['telefon']))) {
        greskaURadu('Poziv nije izvršen na regularan način!');
    }

    $imePrezime = $_POST['imePrezime'];
    $telefon = $_POST['telefon'];

    $rezultat = $konekcija->prepare("INSERT INTO spisak (imePrezime, telefon) VALUES (?, ?)");
    $rezultat->execute([$imePrezime, $telefon]);

    $izlazniXML = new SimpleXMLElement('<?xml version="1.0" encoding="utf-8"?><unos></unos>');
    $izlazniXML->addChild('poruka', 'Unos je uspešno obavljen.');
    break;


    case 'brisanje':
      if (!isset($_POST['id'])) {
          greskaURadu('Poziv nije izvršen na regularan način!');
      }
  
      $id = $_POST['id'];
      $imePrezime = $_POST['imePrezime'];
      $telefon = $_POST['telefon'];
  
      $rezultat = $konekcija->prepare("DELETE FROM spisak WHERE id = ?");
      $rezultat->execute([$id]);

      if ($rezultat) {
          $izlazniXML = new SimpleXMLElement('<?xml version="1.0" encoding="utf-8"?><brisanje></brisanje>');
          $izlazniXML->addChild('poruka', 'Brisanje je uspješno obavljeno.');
      } else {
          $izlazniXML = new SimpleXMLElement('<?xml version="1.0" encoding="utf-8"?><brisanje></brisanje>');
          $izlazniXML->addChild('poruka', 'Došlo je do greške prilikom brisanja stavke.');
      }

      break;

}

Header('Content-type: text/xml');
echo $izlazniXML->asXML();

?>


