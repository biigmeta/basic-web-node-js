<?php

$app->post('/activity/log', function ($req, $res, $args) {

    ## create database connection
    $con = new Database();

    $request = $req->getParsedBody();

    $user = $request['user'];
    $userID = $user['id'];
    $action = $request['action'];

    if (isset($request['dataID']))
        $dataID = $request['dataID'];
    else
        $dataID  = null;

    if (isset($request['description']))
        $description = $request['description'];
    else
        $description  = null;

    $created = date("Y-m-d H:i:s", time());

    ## add activity log
    $sql = "INSERT INTO activity_log(userID,action,dataID,description,created) VALUES($userID,'$action','$dataID','$description','$created')";
    $result =  mysqli_query($con->connection, $sql);

    if ($result) {
        $response = ["status" => "ok", "code" => 200,  "message" => "add activity log successfully.", "data" => $request];
        $json = json_encode($response, JSON_UNESCAPED_UNICODE);
        return $json;
    } else {
        $response = ["status" => "error", "code" => 500,  "message" => "can not add activity log.", "error" => mysqli_error($con->connection)];
        $json = json_encode($response, JSON_UNESCAPED_UNICODE);
        return $json;
    }
});


$app->get('/activity/logs/{action}/duration/{duration}', function ($req, $res, $args) {

    ## create database connection
    $con = new Database();

    $action = $args['action'];
    $duration =  $args['duration'];
    $arrayDuration = explode(",", $duration);
    $impDuration = implode('\',\'', $arrayDuration);

    $data = [];

    foreach ($arrayDuration as $date) {

        $sql = "SELECT * FROM activity_log WHERE action = '$action' AND DATE(created) = '$date' AND disabled != 1";
        $result = mysqli_query($con->connection, $sql);

        if ($result) {
            if (mysqli_num_rows($result) > 0) {

                $logs = [];
                while ($row = mysqli_fetch_assoc($result)) {
                    $logs[] = $row;
                }

                $data[$date] = $logs;
            } else {
                $data[$date] = [];
            }
        } else {
            // error
        }
    }

    $response = ["status" => "ok", "code" => 200,  "message" => "activity log not found.", "data" => $data];
    $json = json_encode($response, JSON_UNESCAPED_UNICODE);
    return $json;
});


$app->get('/activity/log/last/{userID}', function ($req, $res, $args) {

    ## create database connection
    $con = new Database();

    $userID = $args['userID'];
    $sql = "SELECT * FROM activity_log WHERE userID = '$userID' AND disabled != 1 ORDER BY created DESC LIMIT 1";
    $result = mysqli_query($con->connection, $sql);

    if($result)
    {
        if(mysqli_num_rows($result) > 0)
        {
            $log = mysqli_fetch_assoc($result);
            $response = ["status" => "ok", "code" => 200,  "message" => "activity log not found.", "data" => $log];
            $json = json_encode($response, JSON_UNESCAPED_UNICODE);
            return $json;
        }
        else
        {
            $response = ["status" => "error", "code" => 404,  "message" => "activity log not found.", "data" => []];
            $json = json_encode($response, JSON_UNESCAPED_UNICODE);
            return $json;
        }
    }else{
        $response = ["status" => "error", "code" => 500,  "message" => "can not get last activity log.", "error" => mysqli_error($con->connection)];
        $json = json_encode($response, JSON_UNESCAPED_UNICODE);
        return $json;
    }
    $data = [];

    $response = ["status" => "ok", "code" => 200,  "message" => "activity log not found.", "data" => $data];
    $json = json_encode($response, JSON_UNESCAPED_UNICODE);
    return $json;
});
