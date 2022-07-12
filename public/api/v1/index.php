<?php

session_start();

## set header content ##
// header('Content-Type: json/application; charset=utf-8');
// header('Content-Type: text/html; charset=utf-8');
// header('Access-Control-Allow-Origin:*');
// header('Access-Control-Allow-Methods: GET, POST');
// header('Access-Control-Allow-Headers: Content-Type');

header('Content-Type: text/html; charset=utf-8');
header('Access-Control-Allow-Origin:*');
header('Access-Control-Allow-Methods:GET, POST');
header('Access-Control-Allow-Headers: Content-Type');


## composer autoload ##
require 'vendor/autoload.php';
## database ##
require_once 'config/database.php';

## mailer ##
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require 'PHPMailer/src/Exception.php';
require 'PHPMailer/src/PHPMailer.php';
require 'PHPMailer/src/SMTP.php';


## เรียกใช้ Slim Library
use \Slim\App;

## เรียกใช้ Library สำหรับ JWT
include("jwt/JWT.php");

use \Firebase\JWT\JWT;
use Slim\Http\Request;
use Slim\Http\Response;

## vimeo API ##
use Vimeo\Vimeo;

$vimeo = new Vimeo("001b2428571dca77c23106c1124d55f6a05db666", "5iARSu+cxYHlB59nF4R+MvuW42IlrT34XxbVabpmx+eIzLPs/H55oJ5zSQacnKrPewA5hKWLoLxj1ehscESTJpFijFrJWuo0YiB12lmRMQ+f2z1saQxbW9u5/yS00yZN", "ccb81a7ab9e4b5bf9fad6cf74f155312");


define("VERSION", "0.1a");
define("LOCALHOST", "http://localhost/spaceholo/");
define("HOST", "https://www.gforcesolution.com/app/aerospacehololens/");

## -- java web token -- ##
define("SECRET_JWT_KEY", "gzIMH9EZ6dK2YQU2ykTO3lm5J");
define("SSL_KEY", "0WCIbWYSIO3t4eOtRnUSRfsBl");
define("IV_KEY", "T1PocosheG8bdXypsBXNSmk26");

## -- SMS -- ##
define("SMS_API_KEY", "2e778f93e3794aafbb189a081c6c214d");
define("SMS_API_SECRET", "a980ce0e6b7acb46457eb1bb346ae16a");
define("SMS_APP_KEY", "1710784282452592");
define("SMS_APP_SECRET", "749260b7e6fc6eb648757fce093c25ed");

$app = new App();

## require class here ##
require_once("class/gender.php");
require_once("class/education.php");
require_once("class/user.php");
require_once("class/course.php");
require_once("class/lesson.php");
require_once("class/media.php");
require_once("class/pretest.php");
require_once("class/posttest.php");
require_once("class/assessment.php");
require_once("class/activity.php");
require_once("class/upload.php");
require_once("class/question.php");
require_once("class/forget-password.php");
## ------------------ ##

$app->get('/', function ($req, $res, $args) {
    $response = ["status" => 200, "message" => "api connected.", "data" => ""];
    $json = json_encode($response, JSON_UNESCAPED_UNICODE);
    return $json;
});

$app->get('/connection', function ($req, $res, $args) {

    $con = new Database();

    if ($con->connection == null)
        return;

    if ($con->connection) {
        http_response_code(200);
        $response = ["status" => 200, "message" => "database connected.", "data" => ""];
        $json = json_encode($response, JSON_UNESCAPED_UNICODE);
        return $json;
    } else {
        http_response_code(400);
        $response = ["status" => 400,  "message" => "can not connect database."];
        $json = json_encode($response, JSON_UNESCAPED_UNICODE);
        return $json;
    }
});

$app->run();

function verifyHashPassword($password, $hash)
{
    if (password_verify($password, $hash)) {
        return true;
    } else {
        return false;
    }
}

function generateRandomString($n)
{
    $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
    $randomString = '';

    for ($i = 0; $i < $n; $i++) {
        $index = rand(0, strlen($characters) - 1);
        $randomString .= $characters[$index];
    }
    return $randomString;
}

function generateRandomNumber($n)
{
    $characters = '0123456789';
    $randomString = '';

    for ($i = 0; $i < $n; $i++) {
        $index = rand(0, strlen($characters) - 1);
        $randomString .= $characters[$index];
    }
    return $randomString;
}

function compareSecondTime($d1, $d2)
{
    $timeDiff = abs((strtotime($d1) * 1000) - (strtotime($d2) * 1000)) / 1000;
    return $timeDiff;
}

## -- function for send mail -- ##

function sendEmail($toEmail, $subject, $htmlBody)
{
    $mail = new PHPMailer();

    $mail->CharSet = "utf-8";
    $mail->IsSMTP();
    $mail->SMTPDebug = 0;
    $mail->Host = 'ssl://smtp.gmail.com';
    $mail->SMTPAuth = true;
    $mail->Port = 465;
    $mail->IsHTML(true);
    $mail->Username = "ktaxaevent@agencyvr360.com"; // noreply@t2dminsulin.com
    $mail->Password = "3DPublic+";
    $mail->SetFrom("ktaxaevent@agencyvr360.com", "Aerospace HoloLens");
    $mail->Subject = $subject;
    $mail->addAddress($toEmail);
    $mail->MsgHTML($htmlBody);


    if (!$mail->Send()) {
        return "Mailer Error: " . $mail->ErrorInfo;
    } else {
        return true;
    }
}

## -- end of function for send mail -- ##

// -------------------- JWT ------------------------
//สร้าง function สำหรับอ่านข้อมูล User จาก JWT ( Token )
function jwtDecode($jwt)
{
    try {
        //ถอดรหัส token
        $jwt = encrypt_decrypt($jwt, "decrypt");
        //decode token ให้เป็นข้อมูล user
        $payload = JWT::decode($jwt, SECRET_JWT_KEY, array('HS256'));
    } catch (Exception $e) {   //กรณี Token ไม่ถูกต้องจะ return false
        return false;
    }

    //return ข้อมูล user กลับไป
    return  (array)$payload;
}

function jwtEncode($data)
{
    //สร้าง object ข้อมูลสำหรับทำ jwt
    $payload = array(
        "data" => $data,
        "expired" => strtotime('+5 minute'), //กำหนดวันเวลาที่หมดอายุ
        "created" => strtotime('now') //กำหนดวันเวลาที่สร้าง
    );

    //สร้าง JWT สำหรับ object ข้อมูล
    $jwt = JWT::encode($payload, SECRET_JWT_KEY);

    //เพื่อความปลอดภัยยิ่งขึ้นเมื่อได้ JWT แล้วควรเข้ารหัสอีกชั้นหนึ่ง
    $jwt = encrypt_decrypt($jwt, "encrypt");

    // return token ที่สร้าง
    return $jwt;
}

function encrypt_decrypt($str, $action)
{
    $openssl_key = SSL_KEY;
    $iv_key = IV_KEY;

    $method = "AES-256-CBC";

    $iv = substr(md5($iv_key), 0, 16);
    $output = "";

    if ($action == "encrypt") {
        $output = openssl_encrypt($str, $method, $openssl_key, 0, $iv);
    } else if ($action == "decrypt") {
        $output = openssl_decrypt($str, $method, $openssl_key, 0, $iv);
    }

    return $output;
}

function jwtMiddleware($token)
{
    $jwt = encrypt_decrypt($token, "decrypt");

    try {
        $payload = JWT::decode($jwt, SECRET_JWT_KEY, array('HS256'));
        $response = array("status" => "ok", "message" => "success.", "code" => 200, "data" => $payload);
        return $response;
    } catch (Exception $e) {
        $response = array("status" => "error", "message" => "unauthorized.", "code" => 401);
        return $response;
    }
}
