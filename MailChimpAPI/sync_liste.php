<?php
    
    include_once('class/connection.php');  
    include_once('class/MailChimp.php'); 
           
                                    
    // ovo je skripta koja dobiva JSON i sinkronizira bazu s MailChimpom
    
    $data = file_get_contents('php://input');
    $arr=json_decode($data,true);
     
    try 
    { 
         if($arr['firma_idfirma'] && $arr['fi_mailChimpAPIkey'])
        {   
            //instanciranje mailchimp objekta
            $MailChimp = new MailChimp($arr['fi_mailChimpAPIkey']); 
            
            //instanciranje connection objekta    
            $conn = new Connection();   
            
             //dohvati liste iz mailchimpa i spremi u bazu           

            $liste = $MailChimp->call('lists/list');

            foreach($liste['data'] as $i => $c) {
                $c['firmaID'] = $arr['firma_idfirma'];
                
                spremi($c, $conn);

            }
            echo  'Uspjesno povlacenje listi s MailChimp racuna!';                                                  
         
        }
        else
        {
            echo 'Dogodila se greska!!';                                                  
        }
        
    } catch (Exception $e) {
        echo 'Caught exception: ',  $e->getMessage(), "\n";
    }
        
   



    
    function spremi($item, $con)
    {   
        $c = $con->spajanje();
        
        $provjera = "Select crm.mailchimpliste.* from mailchimpliste where IDListe ='".$item['id']. "' and firma_idfirma ='".$item['firmaID']."'"; 
        
        $res = $con->upit($c, $provjera); 
        
        if(!$res)
        {
            $sql = "INSERT INTO crm.mailchimpliste (Listname,IDListe,firma_idfirma) VALUES ('".$item['name']."','".$item['id']."',".$item['firmaID'].")"; 
        }
        else
        {
            $sql = "UPDATE crm.mailchimpliste set Listname = '".$item['name']."' where IDListe ='".$item['id']."'";
        } 
        
                
        $con->upit($c, $sql);
    }



?>
