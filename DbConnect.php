
<?php
class DbConnect{
    private $servername = "localhost";
    private $port_no = 3306;
    private $username = "ankur";
    private $password = "ankur";
    private $myDB = "lms";
    
    public function connect(){
        try{
            $conn = new PDO("mysql:host={$this->$servername};port={$this->$port_no}; dbname={$this->$myDB}" ,$this->$username,$this->$password);
            $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            return $conn;
        }catch(PDOException $e){
            echo "Connection failed: ".$e->getMessage();
            return null;
        }
    }
};
?>
