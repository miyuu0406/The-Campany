<?php

class Database
{
    private $server_name  = "localhost";
    private $username     = "root";
    private $password     = "root"; // $password = "" ; for XAMPP users, // $password = "root"; for MAMP USERS
    private $db_name      = "the_company";
    protected $conn;

    public function __construct()
    {                  //mysql橋みたいな繋ぐ役割
        $this->conn = new mysqli($this->server_name,$this->username,$this->password,$this->db_name);
        // msqli = Represent a connection between php and a mysql database.
        // $this->conn is now the object the class mysql
        // $this->conn holds the connection to the db

        if($this->conn->connect_error){
            die("Unable to connect to the database: " . $this->conn->connect_error);
        }
    }
}

?>