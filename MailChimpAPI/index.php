<?php
  include_once('class/sinkronizacija.php');
  
  $sync = new Sinkronizacija();
  
  /*
  //sinkronizacija lista s mailchimp racuna - dohvati liste i spremi ih u bazu / ažurira
  $jsonData = array(
        'firma_idfirma'      => 3,
        'fi_mailChimpAPIkey' => 'a2abd60dd8c7436efc985d8684e4c05e-us10'    
  ); 
   
  $sync->dohvatiJSON($jsonData, 'liste');   
  */
  
  
  
  
  // provjeri za svakog korisnika iz baze (kontaktiliste) da li je vec dodan na mailchimp listu ako nije dodan subscribe ga na listu  
  /*  
  //The JSON data.
  $jsonData = array(
        'firma_idfirma'      => 3,
        'fi_mailChimpAPIkey' => 'a2abd60dd8c7436efc985d8684e4c05e-us10',
        'id_liste'           => '01788b1185'        
  );   
  $sync->dohvatiJSON($jsonData, 'subscribe');
  
  
  */
  
  
  
  
  //sinkronizacija clanova neke liste, dohvati sve clanove i sprema ih u kontakti
  //ako je kosrisnik subscribed dodaje / azurira ga u kontaktiliste,  ako je na mailchimpu unsubscribed makne ga iz tablice kontaktiliste
  /*
  $jsonData = array(
        'firma_idfirma'      => 3,
        'fi_mailChimpAPIkey' => 'a2abd60dd8c7436efc985d8684e4c05e-us10',
        // ovo je ID liste s mailchim racuna
        'id_liste'           => '01788b1185',
        //ovo je id liste kod nas u bazi
        'idMailChimpListe'   => 10
                
  );        
  $sync->dohvatiJSON($jsonData, 'members');
  */
           
?> 


