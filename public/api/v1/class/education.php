<?php

$app->get('/educations', function ($req, $res, $args) {
    ## create database connection
    $con = new Database();

    ## create SQL and query
    $sql = "SELECT * FROM education";
    $result = mysqli_query($con->connection, $sql);

    ## check query result
    if ($result) {
        if (mysqli_num_rows($result) > 0) {

            ## found data

            $data = [];

            while ($row = mysqli_fetch_assoc($result)) {
                $data[] = $row;
            }

            $response = ["status" => "ok", "code" => 200,  "message" => "get educations data successfully.", "data" => $data];
            $json = json_encode($response, JSON_UNESCAPED_UNICODE);
            return $json;
        } else {

            ## not found data

            $response = ["status" => "error", "code" => 404,  "message" => "data not found.", "data" => []];
            $json = json_encode($response, JSON_UNESCAPED_UNICODE);
            return $json;
        }
    } else {

        ## mysqli error
        $response = ["status" => "error", "code" => 500,  "message" => "can not connect database.", "error" => mysqli_error($con->connection)];
        $json = json_encode($response, JSON_UNESCAPED_UNICODE);
        return $json;
    }
});


$app->get('/majors', function ($req, $res, $args) {
    ## create database connection
    $con = new Database();

    ## create SQL and query
    $sql = "SELECT * FROM major WHERE disabled != 1";
    $result = mysqli_query($con->connection, $sql);

    ## check query result
    if ($result) {
        if (mysqli_num_rows($result) > 0) {

            ## found data

            $data = [];

            while ($row = mysqli_fetch_assoc($result)) {
                $data[] = $row;
            }

            $response = ["status" => "ok", "code" => 200,  "message" => "get majors data successfully.", "data" => $data];
            $json = json_encode($response, JSON_UNESCAPED_UNICODE);
            return $json;
        } else {

            ## not found data

            $response = ["status" => "error", "code" => 404,  "message" => "data not found.", "data" => []];
            $json = json_encode($response, JSON_UNESCAPED_UNICODE);
            return $json;
        }
    } else {

        ## mysqli error
        $response = ["status" => "error", "code" => 500,  "message" => "can not connect database.", "error" => mysqli_error($con->connection)];
        $json = json_encode($response, JSON_UNESCAPED_UNICODE);
        return $json;
    }
});


$app->get('/institutions', function ($req, $res, $args) {
    ## create database connection
    $con = new Database();

    ## create SQL and query
    $sql = "SELECT * FROM institution WHERE disabled != 1";
    $result = mysqli_query($con->connection, $sql);

    ## check query result
    if ($result) {
        if (mysqli_num_rows($result) > 0) {

            ## found data

            $data = [];

            while ($row = mysqli_fetch_assoc($result)) {
                $data[] = $row;
            }

            $response = ["status" => "ok", "code" => 200,  "message" => "get institutions data successfully.", "data" => $data];
            $json = json_encode($response, JSON_UNESCAPED_UNICODE);
            return $json;
        } else {

            ## not found data

            $response = ["status" => "error", "code" => 404,  "message" => "data not found.", "data" => []];
            $json = json_encode($response, JSON_UNESCAPED_UNICODE);
            return $json;
        }
    } else {

        ## mysqli error
        $response = ["status" => "error", "code" => 500,  "message" => "can not connect database.", "error" => mysqli_error($con->connection)];
        $json = json_encode($response, JSON_UNESCAPED_UNICODE);
        return $json;
    }
});

$app->post('/institution', function ($req, $res, $args) {
    ## create database connection
    $con = new Database();

    $request = $req->getParsedBody();
    $institution = $request['institution'];
    $nameTH = $institution['nameTH'];
    $nameEN = $institution['nameEN'];

    ## check name already exist
    $sql = "SELECT * FROM institution WHERE nameTH = '$nameTH' OR nameEN = '$nameEN'";
    $result = mysqli_query($con->connection, $sql);

    ## check query result
    if ($result) {
        if (mysqli_num_rows($result) > 0) {
            $response = ["status" => "error", "code" => 400,  "message" => "institution already exist.", "data" => []];
            $json = json_encode($response, JSON_UNESCAPED_UNICODE);
            return $json;
        }
    } else {
        ## mysqli error
        $response = ["status" => "error", "code" => 500,  "message" => "check institution failed.", "error" => mysqli_error($con->connection)];
        $json = json_encode($response, JSON_UNESCAPED_UNICODE);
        return $json;
    }


    ## create SQL and query
    $sql = "INSERT INTO institution (nameTH, nameEN) VALUES ('$nameTH', '$nameEN')";
    $result = mysqli_query($con->connection, $sql);

    ## check query result
    if ($result) {
        ## insert data success
        $response = ["status" => "ok", "code" => 200,  "message" => "insert institution data successfully.", "data" => $institution];
        $json = json_encode($response, JSON_UNESCAPED_UNICODE);
        return $json;
    } else {
        ## mysqli error
        $response = ["status" => "error", "code" => 500,  "message" => "can not connect database.", "error" => mysqli_error($con->connection)];
        $json = json_encode($response, JSON_UNESCAPED_UNICODE);
        return $json;
    }
});

$app->delete('/institution/{id}', function ($req, $res, $args) {
    ## create database connection
    $con = new Database();
    $institutionID = $args['id'];

    ## create SQL and query
    $sql = "UPDATE institution SET disabled = 1 WHERE id = $institutionID";
    $result = mysqli_query($con->connection, $sql);

    ## check query result
    if ($result) {
        $response = ["status" => "ok", "code" => 200,  "message" => "delete(disabled) institutions data successfully.", "data" => $institutionID];
        $json = json_encode($response, JSON_UNESCAPED_UNICODE);
        return $json;
    } else {

        ## mysqli error
        $response = ["status" => "error", "code" => 500,  "message" => "can not delete institution data.", "error" => mysqli_error($con->connection)];
        $json = json_encode($response, JSON_UNESCAPED_UNICODE);
        return $json;
    }
});
