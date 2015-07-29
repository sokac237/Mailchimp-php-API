<?php
  
  class Connection
  {
       public function spajanje() {
        $con=mysqli_connect("localhost","root","","");

        mysqli_set_charset($con, 'utf8');

        
        // Check connection
        if (mysqli_connect_errno()) {
            echo "Failed to connect to MySQL: " . mysqli_connect_error();
        }
        else return $con;
    }


    public function upit($con, $sql) {
        $result = mysqli_query($con, $sql);
        if($result == false) {
            echo "'".$sql."' is not a legal SQL query!";
            return false;
        }
        else if(is_bool($result)) {
            //u sluèaju insert, delete, update itd.
            return true;
        }
        
        $rows = array();
        while($row = mysqli_fetch_array($result)) {
            $rows[] = $row;
        }
        return $rows;
    }
    
  }
?>
