<?php

$app->get('/lessons', function ($req, $res, $args) {

    ## create database connection
    $con = new Database();

    ## create SQL and query
    $sql = "SELECT * FROM lesson WHERE disabled != 1";
    $result = mysqli_query($con->connection, $sql);

    ## check query result
    if ($result) {
        if (mysqli_num_rows($result) > 0) {

            ## found data

            $data = [];

            while ($row = mysqli_fetch_assoc($result)) {

                $data[] = $row;
            }

            http_response_code(200);
            $response = ["status" => 200,  "message" => "get lessons data successfully.", "data" => $data];
            $json = json_encode($response, JSON_UNESCAPED_UNICODE);
            return $json;
        } else {

            ## not found data

            http_response_code(404);
            $response = ["status" => 404,  "message" => "data not found.", "data" => []];
            $json = json_encode($response, JSON_UNESCAPED_UNICODE);
            return $json;
        }
    } else {

        ## mysqli error

        http_response_code(400);
        $response = ["status" => 400,  "message" => "can not connect database.", "error" => mysqli_error($con->connection)];
        $json = json_encode($response, JSON_UNESCAPED_UNICODE);
        return $json;
    }
});


$app->get('/lesson/{id}', function ($req, $res, $args) {

    ## create database connection
    $con = new Database();

    $id = $args['id'];

    ## create SQL and query
    $sql = "SELECT * FROM lesson WHERE id = '$id' AND disabled != 1";
    $result = mysqli_query($con->connection, $sql);

    ## check query result
    if ($result) {
        if (mysqli_num_rows($result) > 0) {

            ## found data

            $data = [];

            while ($row = mysqli_fetch_assoc($result)) {

                $data[] = $row;
            }

            http_response_code(200);
            $response = ["status" => 200,  "message" => "get lessons data successfully.", "data" => $data];
            $json = json_encode($response, JSON_UNESCAPED_UNICODE);
            return $json;
        } else {

            ## not found data

            http_response_code(404);
            $response = ["status" => 404,  "message" => "data not found.", "data" => []];
            $json = json_encode($response, JSON_UNESCAPED_UNICODE);
            return $json;
        }
    } else {

        ## mysqli error

        http_response_code(400);
        $response = ["status" => 400,  "message" => "can not connect database.", "error" => mysqli_error($con->connection)];
        $json = json_encode($response, JSON_UNESCAPED_UNICODE);
        return $json;
    }
});


$app->get('/lessons/{courseID}', function ($req, $res, $args) {

    ## create database connection
    $con = new Database();

    $courseID = $args['courseID'];

    ## create SQL and query
    $sql = "SELECT * FROM lesson WHERE courseID = '$courseID' AND disabled != 1 ORDER BY orderNumber ASC";
    $result = mysqli_query($con->connection, $sql);

    ## check query result
    if ($result) {
        if (mysqli_num_rows($result) > 0) {

            ## found data

            $data = [];

            while ($row = mysqli_fetch_assoc($result)) {

                $data[] = $row;
            }

            http_response_code(200);
            $response = ["status" => 200,  "message" => "get lessons data successfully.", "data" => $data];
            $json = json_encode($response, JSON_UNESCAPED_UNICODE);
            return $json;
        } else {

            ## not found data

            http_response_code(404);
            $response = ["status" => 404,  "message" => "data not found.", "data" => []];
            $json = json_encode($response, JSON_UNESCAPED_UNICODE);
            return $json;
        }
    } else {

        ## mysqli error
        $response = ["status" => 500,  "message" => "can get lessons.", "error" => mysqli_error($con->connection)];
        $json = json_encode($response, JSON_UNESCAPED_UNICODE);
        return $json;
    }
});

$app->post('/lesson/update', function ($req, $res, $args) {

    ## create database connection
    $con = new Database();

    $request = $req->getParsedBody();
    $lesson = $request['lesson'];
    $lessonID = $lesson['id'];

    $nameLensoTH = trim($lesson['nameLensoTH']);
    $nameLensoEN = trim($lesson['nameLensoEN']);
    $nameSeniorTH = trim($lesson['nameSeniorTH']);
    $nameSeniorEN = trim($lesson['nameSeniorEN']);

    ## create SQL and query
    $sql = "UPDATE lesson SET nameLensoTH = '$nameLensoTH', nameLensoEN = '$nameLensoEN', nameSeniorTH = '$nameSeniorTH', nameSeniorEN = '$nameSeniorEN' WHERE id = '$lessonID'";
    $result = mysqli_query($con->connection, $sql);

    if ($result) {
        $response = ["status" => 200,  "message" => "update lesson successfully.", "data" => $lesson];
        $json = json_encode($response, JSON_UNESCAPED_UNICODE);
        return $json;
    } else {

        $response = ["status" => 400,  "message" => "can not update lesson.", "error" => mysqli_error($con->connection)];
        $json = json_encode($response, JSON_UNESCAPED_UNICODE);
        return $json;
    }
});
