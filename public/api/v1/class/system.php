<?php

$app->get('/system', function ($req, $res, $args) {

    ## create database connection
    $con = new Database();

    ## get course progress
    $sql = "SELECT * FROM system_setting WHERE disabled != 1 ORDER BY id DESC LIMIT 1";
    $result = mysqli_query($con->connection, $sql);

    if ($result) {
        if (mysqli_num_rows($result) > 0) {

            $data = [];
            while ($row = mysqli_fetch_assoc($result)) {
                $data[] = $row;
            }

            $response = ["status" => "ok", "code" => 200,  "message" => "get system setting successfully.", "data" => $data];
            $json = json_encode($response, JSON_UNESCAPED_UNICODE);
            return $json;
        } else {

            $response = ["status" => "error", "code" => 404, "message" => "system data not found.", "data" => null];
            $json = json_encode($response, JSON_UNESCAPED_UNICODE);
            return $json;
        }
    } else {
        $response = ["status" => "error", "code" => 500,  "message" => "get system setting failed.", "error" => mysqli_error($con->connection)];
        $json = json_encode($response, JSON_UNESCAPED_UNICODE);
        return $json;
    }
});


$app->post('/system', function ($req, $res, $args) {

    ## create database connection
    $con = new Database();

    $request = $req->getParsedBody();
    $howToUseHololensAllowed = $request['howToUseHololensAllowed'];
    $pretestRepeatAllowed = $request['pretestRepeatAllowed'];
    $posttestScore = $request['posttestScore'];
    $created = date("Y-m-d H:i:s", time());


    ## get course progress
    $sql = "SELECT * FROM system_setting WHERE disabled != 1";
    $result = mysqli_query($con->connection, $sql);

    if ($result) {
        if (mysqli_num_rows($result) > 0) {

            $data = [];
            while ($row = mysqli_fetch_assoc($result)) {
                $data[] = $row;
            }

            $response = ["status" => "ok", "code" => 200,  "message" => "get user progress successfully.", "data" => $data];
            $json = json_encode($response, JSON_UNESCAPED_UNICODE);
            return $json;
        } else {

            $response = ["status" => "error", "code" => 404, "message" => "data not found.", "data" => null];
            $json = json_encode($response, JSON_UNESCAPED_UNICODE);
            return $json;
        }
    } else {
        $response = ["status" => "error", "code" => 500,  "message" => "get user progress failed.", "error" => mysqli_error($con->connection)];
        $json = json_encode($response, JSON_UNESCAPED_UNICODE);
        return $json;
    }
});
