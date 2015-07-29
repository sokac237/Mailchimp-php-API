<?php
    include_once('class/connection.php');  
    include_once('class/MailChimp.php'); 
               
    // ovo je skripta koja dobiva JSON i sinkronizira bazu s MailChimpom
    
    $data = file_get_contents('php://input');
    $arr = json_decode($data,true);
               
             
    
    try 
    {     
        if($arr['firma_idfirma'] && $arr['fi_mailChimpAPIkey'])
        { 
            $array = array();
       
            //instanciranje mailchimp objekta
            $MailChimp = new MailChimp($arr['fi_mailChimpAPIkey']); 
            
            //instanciranje connection objekta    
            $conn = new Connection();   
            
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
           
 
            // za svakog korisnika u listi odradi sync
            
            foreach($liste['data'] as $b) {
                $arr['status'] = $b['status'];
                
                foreach($b['merges'] as $x => $x_value) {
                  
                    switch ($x) {
                        case 'EMAIL': 
                            $arr["email"] = $x_value;                                                          
                            break;
                        case 'FNAME':
                            $arr["ime"] = $x_value;
                            break;
                        case 'LNAME':
                            $arr["prezime"] = $x_value;
                            break;                         
                    }
                }                
               $a = spremi($arr, $conn); 

               array_push($array, array('id' => $a, 'status' => $b['status'])); 
            
            }                 
       
            foreach($array as $ar => $a)
            {
                napuniKotaktiListe($arr, $conn, $a['id'], $a['status']);
            }

            echo 'Uspjesno sinkronizirani podaci!';  
        }
        else
        {
            echo 'Neispravni JSON podatci!';
        }
        
    } catch (Exception $e) {
        
        echo 'Caught exception: ',  $e->getMessage(), "\n";
    }
    
    
    function spremi($arr, $con){   
        //prvo provjerava da li postoji kontakt  ako da onda update ako ne onda insert
        $c = $con->spajanje();
        
        $provjera = "Select crm.kontakti.* from kontakti where ko_email1 ='".$arr['email']. "' and firma_idfirma ='".$arr['firma_idfirma']."'"; 
        
        $res = $con->upit($c, $provjera); 
        

        if(!$res)
        {
             $sql = "INSERT INTO crm.kontakti (ko_ime,ko_prezime,ko_email1, firma_idfirma) VALUES ('".$arr['ime']."','".$arr['prezime']."','".$arr['email']."', ".$arr['firma_idfirma'].")";
             
             $con->upit($c, $sql); 

             //napuniKotaktiListe($arr, $con, $c->insert_id);
               
             return $c->insert_id;    
        }
        else
        {
            $sql =  "UPDATE crm.kontakti set ko_ime = '".$arr['ime']."', ko_prezime = '".$arr['prezime']."' where idkontakti ='".$res[0]['idkontakti']."'";    
            
            $con->upit($c, $sql);
            
            return  $res[0]['idkontakti'];
        }         
           
    }
       
    
    function napuniKotaktiListe($arr, $con, $id, $status){
        
        if($id && $status == 'subscribed')
        {   
            //za preuzete korisnike koji su clanovi odabarane liste dodaj ih u tablicu kontaktiliste  
            
            $c = $con->spajanje();   
            
            //provjeri da li postoji veza korisnik - lista - firma
            $sql = "Select crm.kontaktiliste.* from kontaktiliste where kontaktiliste.idMailChimpListe ='".$arr['idMailChimpListe']. "'  and kontaktiliste.firma_idfirma  ='".$arr['firma_idfirma']."' and kontaktiliste.kontakti_idkontakti='".$id."'"; 
     
            $res = $con->upit($c, $sql);     

            if(!$res)
            {
                //dodaj korisnika na listu
                //echo $id.'insert';
                $sql = "INSERT INTO crm.kontaktiliste (idMailChimpListe,kontakti_idkontakti,firma_idfirma) VALUES ('".$arr['idMailChimpListe']."','".$id."',".$arr['firma_idfirma'].")"; 
            }
                              
            $con->upit($c, $sql);
        } 
        else if($id && $status == 'unsubscribed')
        {

            //za clanove liste koji su se odjavili s liste makni ih iz tablice kontaktiliste ako postoje
            $c = $con->spajanje();   
            
            //provjeri da li postoji veza korisnik - lista - firma
            $sql = "Select crm.kontaktiliste.* from kontaktiliste where kontaktiliste.idMailChimpListe ='".$arr['idMailChimpListe']. "'  and kontaktiliste.firma_idfirma  ='".$arr['firma_idfirma']."' and kontaktiliste.kontakti_idkontakti='".$id."'"; 
     
            $res = $con->upit($c, $sql);     

            if($res)
            {
                //insert  
                //echo $id.'insert';
                $sql = "Delete from crm.kontaktiliste where  idMailChimpListe = '".$arr['idMailChimpListe']."' and kontakti_idkontakti = '".$id."' and firma_idfirma =".$arr['firma_idfirma'].""; 
            
            }
                              
            $con->upit($c, $sql);
            
        }
    } 

?>
