<?php                        

class Sinkronizacija 
{            
          
    public function dohvatiJSON($jsonData, $action)
    {
      
       //API Url
        $url = 'http://localhost/mailchimp/MailChimpAPI/sync_'.$action.'.php';
         
        //Initiate cURL.

        $ch = curl_init($url);
                         
        //Encode the array into JSON.
        $jsonDataEncoded = json_encode($jsonData);
         
        //Tell cURL that we want to send a POST request.
        curl_setopt($ch, CURLOPT_POST, 1);
         
        //Attach our encoded JSON string to the POST fields.
        curl_setopt($ch, CURLOPT_POSTFIELDS, $jsonDataEncoded);
         
        //Set the content type to application/json
        curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json')); 
         
        //Execute the request
        $result = curl_exec($ch);  

        $output = json_decode($result);
        
        if(curl_errno($ch)){
           //echo 'Request Error:' . curl_error($ch);
           $output = 0;
        }    
        
        curl_close($ch);   
        
        return $output; 
    }

}

?>
