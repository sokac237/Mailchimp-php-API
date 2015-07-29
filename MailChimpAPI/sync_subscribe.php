<?php
    include_once('class/connection.php');  
    include_once('class/MailChimp.php'); 
    // ovo je skripta koja dobiva JSON i sinkronizira bazu s MailChimpom
    
    $data = file_get_contents('php://input');
    $arr=json_decode($data,true);
    $array = array();
    
    try {
        if($arr['firma_idfirma'] && $arr['fi_mailChimpAPIkey'] && $arr['id_liste'])
        {   
            //instanciranje mailchimp objekta
            $MailChimp = new MailChimp($arr['fi_mailChimpAPIkey']); 
        
            //instanciranje connection objekta    
            $conn = new Connection();  

            //dohvati sve koirisnike iz baze koji su dodani na odabranu listu

            $korisnici =  korisnici($arr,$conn);   

            //dohvati èlanove lista / usere subscribed i unsubscribed 
            $liste = $MailChimp->call('lists/members',array(
                'id'                => $arr['id_liste'],
                'status'            => 'subscribed'   
            ));

            $liste2 = $MailChimp->call('lists/members',array(
                'id'                => $arr['id_liste'] ,
                 'status'            => 'unsubscribed'  
            ));  
            
            //strpaj sve u jednu listu
            foreach($liste2['data'] as $list2)
            {
                 array_push($liste['data'], $list2);   
            }

            //dodaj sve mailove s liste mailchimp
            foreach($liste['data'] as $b) {
               array_push($array, $b['email']);             
            }   

            //provjeri za svakog korisnika iz baze da li je vec dodan na mailchimp listu

            foreach($korisnici as $korisnik)
            {
               
                if (in_array($korisnik['email'], $array)) {
                    //ne radi nista jer korisnik vec postoji na listi
                }
                else
                {
                    //ako nije dodan subscribe ga na listu
                    $MailChimp->call('lists/subscribe', array(
                        'id'                => $arr['id_liste'],
                        'email'             => array('email'=> $korisnik['email']),
                        'merge_vars'        => array('FNAME'=>$korisnik['ime'], 'LNAME'=>$korisnik['prezime']),
                        'double_optin'      => false,
                        'update_existing'   => true,
                        'replace_interests' => false,
                        'send_welcome'      => false,
                    ));

                }  
                
            }
            echo 'Uspjesno prijavljivanje korisnika na Mailchimp listu!';   
         
        }
        else
        {
           echo 'Dogodila se greska!!';
        }
    } catch (Exception $e) {
        echo 'Greska: ',  $e->getMessage(), "\n";
    }    
       

    function korisnici($arr, $conn)
    {   
        $c = $conn->spajanje();
        $sql = "Select crm.kontaktiliste.*, kontakti.ko_ime as ime, kontakti.ko_prezime as prezime, kontakti.ko_email1 as email from kontaktiliste join mailchimpliste on mailchimpliste.idMailChimpListe =kontaktiliste.idMailChimpListe join kontakti on kontakti.idkontakti =kontaktiliste.kontakti_idkontakti where mailchimpliste.IDListe ='".$arr['id_liste']. "'  and kontakti.firma_idfirma  ='".$arr['firma_idfirma']."'"; 
         
        return $res = $conn->upit($c, $sql); 
        
    }



//echo('<pre>');
//print_r($arr2);
//echo('</pre>'); 

?>
