<?php

define("MAX_FILE_SIZE", 5000000);
define("DIR_COURSE_THUMBNAIL", "assets/thumbnail/");

$app->post('/upload/course/thumbnail', function ($req, $res, $args) {

    ## check file attachment ##
    if (!isset($_FILES['file'])) {
        $response = ["status" => "error", "code" => 403,  "message" => "no file.", "data" => null];
        $json = json_encode($response, JSON_UNESCAPED_UNICODE);
        return $json;
    }

    $request = $req->getParsedBody();

    $file = $_FILES['file'];
    $filesize = $file['size'];
    $extension = explode("/", $file['type'])[1];
    $filename = $request['name'] . "_" . time() . "." . $extension;


    if ($filesize > MAX_FILE_SIZE) {
        $response = ["status" => "error", "code" => 403,  "message" => "file size is too large.", "data" => null];
        $json = json_encode($response, JSON_UNESCAPED_UNICODE);
        return $json;
    }

    if (!$extension || $extension == "") {
        $response = ["status" => "error", "code" => 403,  "message" => "file extension not found.", "data" => null];
        $json = json_encode($response, JSON_UNESCAPED_UNICODE);
        return $json;
    }

    $rootDir = '../../';

    $directory = DIR_COURSE_THUMBNAIL;
    $target_directory = $rootDir . DIR_COURSE_THUMBNAIL;

    ## check directory exist ##
    if (!is_dir($target_directory)) {
        if (!mkdir($target_directory, 0777, true)) {
            $response = array("status" => "error", "message" => "create directory failed.");
            $json = json_encode($response, JSON_UNESCAPED_UNICODE);
            return $json;
        }
    }

    $target_upload = $target_directory . $filename;
    $image_url = HOST . $directory . $filename;

    ## upload file ##
    if (move_uploaded_file($file["tmp_name"], $target_upload)) {

        $fileData = [
            "name" => $filename,
            "url" => $image_url,
            "size" => $filesize,
            "extension" => $extension
        ];

        $response = ["status" => "ok", "code" => 200,  "message" => "upload success.", "data" => $fileData];
        $json = json_encode($response, JSON_UNESCAPED_UNICODE);
        return $json;
    } else {
        $response = ["status" => "error", "code" => 403,  "message" => "upload failed.", "data" => null];
        $json = json_encode($response, JSON_UNESCAPED_UNICODE);
        return $json;
    }
});
