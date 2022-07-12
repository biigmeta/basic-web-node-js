<?php
class Database
{
    public $connection;

    private $host = "localhost"; ## localhost:3306 
    private $username = "spaceholo";
    private $password = "Y8x1@ru4";
    private $dbname = "aerospacehololens";

    public function __construct()
    {
        try {
            $con = mysqli_connect($this->host, $this->username, $this->password, $this->dbname); ## สร้าง connection
            if ($con) {
                mysqli_query($con, "SET NAMES UTF8") or die(mysqli_error($con)); ## กำหนดชุดตัวอักษร
                date_default_timezone_set("Asia/Bangkok"); ## กำหนดโซนเวลา
                $this->connection = $con; ## กำหนดค่าให้ connection
            }else{
                echo "Connection Failed.";
            }
        } catch (Exception $e) {
            return "Message: " . $e->getMessage();
            exit;
        }
    }
}
