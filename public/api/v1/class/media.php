<?php
$app->get('/medias', function ($req, $res, $args) {

    ## create database connection
    $con = new Database();

    ## create SQL and query
    $sql = "SELECT * FROM media WHERE disabled != 1";
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
            $response = ["status" => 200,  "message" => "get medias data successfully.", "data" => $data];
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

$app->get('/media/{id}', function ($req, $res, $args) {

    ## create database connection
    $con = new Database();

    $id = $args['id'];

    ## create SQL and query
    $sql = "SELECT * FROM media WHERE id = '$id' AND disabled != 1";
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
            $response = ["status" => 200,  "message" => "get media data successfully.", "data" => $data];
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


$app->get('/medias/{lessonID}', function ($req, $res, $args) {

    ## create database connection
    $con = new Database();

    $lessonID = $args['lessonID'];

    ## create SQL and query
    $sql = "SELECT * FROM media WHERE lessonID = '$lessonID' AND disabled != 1 ORDER BY orderNumber ASC";
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
            $response = ["status" => 200,  "message" => "get media data successfully.", "data" => $data];
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

$app->get('/media/progress/{userID}', function ($req, $res, $args) {

    ## create database connection
    $con = new Database();

    $userID = $args['userID'];

    ## select user data
    $sql = "SELECT * FROM user WHERE id = '$userID'";
    $result = mysqli_query($con->connection, $sql);

    ## check query result
    if ($result) {
        if (mysqli_num_rows($result) > 0) {

            ## found data
            $userRow = mysqli_fetch_assoc($result);
            $userData = $userRow;
        } else {
            $response = ["status" => "error", "code" => 404,  "message" => "data not found.", "data" => []];
            $json = json_encode($response, JSON_UNESCAPED_UNICODE);
            return $json;
        }
    } else {
        $response = ["status" => "error", "code" => 500,  "message" => "can not get user data.", "error" => mysqli_error($con->connection)];
        $json = json_encode($response, JSON_UNESCAPED_UNICODE);
        return $json;
    }

    $company = $userData['company'];

    ## get media progress
    $sql = "SELECT * FROM media_enroll WHERE userID = '$userID' AND company = '$company' AND completed = 1 AND disabled != 1";
    $result = mysqli_query($con->connection, $sql);

    if ($result) {
        if (mysqli_num_rows($result) > 0) {

            $data = [];
            while ($row = mysqli_fetch_assoc($result)) {
                $data[] = $row;
            }

            $response = ["status" => "ok", "code" => 200,  "message" => "get media progress successfully.", "data" => $data];
            $json = json_encode($response, JSON_UNESCAPED_UNICODE);
            return $json;
        } else {

            $response = ["status" => "error", "code" => 404,  "message" => "data not found.", "data" => null];
            $json = json_encode($response, JSON_UNESCAPED_UNICODE);
            return $json;
        }
    } else {
        $response = ["status" => "error", "code" => 500,  "message" => "get user progress failed.", "error" => mysqli_error($con->connection)];
        $json = json_encode($response, JSON_UNESCAPED_UNICODE);
        return $json;
    }
});


$app->post('/media/update', function ($req, $res, $args) {

    ## create database connection
    $con = new Database();

    $request = $req->getParsedBody();
    $media = $request['media'];


    $mediaID = $media['id'];
    $videoLenso  = $media['videoLenso'];
    $videoSenior = $media['videoSenior'];


    ## update media data
    $sql = "UPDATE media SET videoLenso = '$videoLenso', videoSenior = '$videoSenior' WHERE id = '$mediaID'";
    $result = mysqli_query($con->connection, $sql);

    ## check query result
    if ($result) {
        $response = ["status" => "ok", "code" => 200,  "message" => "update media data successfully.", "data" => $media];
        $json = json_encode($response, JSON_UNESCAPED_UNICODE);
        return $json;
    } else {
        $response = ["status" => "error", "code" => 500,  "message" => "update media data failed.", "error" => mysqli_error($con->connection)];
        $json = json_encode($response, JSON_UNESCAPED_UNICODE);
        return $json;
    }
});


$app->post('/media/enroll', function ($req, $res, $args) {

    ## create database connection
    $con = new Database();

    $request = $req->getParsedBody();

    $user = $request['user'];
    $media = $request['media'];


    $userID = $user['id'];
    $mediaID = $media['id'];

    $created = date("Y-m-d H:i:s", time());

    ## select user data
    $sql = "SELECT * FROM user WHERE id = '$userID'";
    $result = mysqli_query($con->connection, $sql);

    ## check query result
    if ($result) {
        if (mysqli_num_rows($result) > 0) {

            ## found data
            $userRow = mysqli_fetch_assoc($result);
            $userData = $userRow;
        } else {
            $response = ["status" => "error", "code" => 404,  "message" => "data not found.", "data" => []];
            $json = json_encode($response, JSON_UNESCAPED_UNICODE);
            return $json;
        }
    } else {
        $response = ["status" => "error", "code" => 500,  "message" => "can not get user data.", "error" => mysqli_error($con->connection)];
        $json = json_encode($response, JSON_UNESCAPED_UNICODE);
        return $json;
    }

    $company = $userData['company'];

    ## check enroll exist data
    $select_user_complete_sql = "SELECT * FROM media_enroll WHERE userID = $userID AND mediaID = $mediaID AND company = '$company' AND disabled != 1";
    $select_user_complete_result = mysqli_query($con->connection, $select_user_complete_sql);

    if ($select_user_complete_result) {

        if (mysqli_num_rows($select_user_complete_result) == 0) {
            ## no data
            ## insert
            $completed = 0;
            $disabled = 0;

            ## insert media enroll sql
            $sql = "INSERT INTO media_enroll(userID,mediaID,company,completed,completedTime,disabled,created) VALUES($userID,$mediaID,'$company',$completed,null,$disabled,'$created')";
            $result = mysqli_query($con->connection, $sql);

            if ($result) {
                $response = ["status" => "ok", "code" => 200,  "message" => "update media enroll successfully.", "data" => $request];
                $json = json_encode($response, JSON_UNESCAPED_UNICODE);
                return $json;
            } else {
                $response = ["status" => "error", "code" => 500,  "message" => "update media enroll failed.", "error" => mysqli_error($con->connection)];
                $json = json_encode($response, JSON_UNESCAPED_UNICODE);
                return $json;
            }
        } else {

            ## already data
            $response = ["status" => "ok", "code" => 200,  "message" => "you are already enroll this media.", "data" => $request];
            $json = json_encode($response, JSON_UNESCAPED_UNICODE);
            return $json;
        }
    } else {
        $response = ["status" => "error", "code" => 500,  "message" => "check media enroll failed.", "error" => mysqli_error($con->connection),];
        $json = json_encode($response, JSON_UNESCAPED_UNICODE);
        return $json;
    }
});

$app->post('/media/complete', function ($req, $res, $args) {

    ## create database connection
    $con = new Database();

    $request = $req->getParsedBody();

    $user = $request['user'];
    $media = $request['media'];


    $userID = $user['id'];
    $mediaID = $media['id'];

    $created = date("Y-m-d H:i:s", time());

    ## select user data
    $sql = "SELECT * FROM user WHERE id = '$userID'";
    $result = mysqli_query($con->connection, $sql);

    ## check query result
    if ($result) {
        if (mysqli_num_rows($result) > 0) {

            ## found data
            $userRow = mysqli_fetch_assoc($result);
            $userData = $userRow;
        } else {
            $response = ["status" => "error", "code" => 404,  "message" => "data not found.", "data" => []];
            $json = json_encode($response, JSON_UNESCAPED_UNICODE);
            return $json;
        }
    } else {
        $response = ["status" => "error", "code" => 500,  "message" => "can not get user data.", "error" => mysqli_error($con->connection)];
        $json = json_encode($response, JSON_UNESCAPED_UNICODE);
        return $json;
    }

    $company = $userData['company'];

    ## check enroll exist data
    $select_user_complete_sql = "SELECT * FROM media_enroll WHERE userID = '$userID' AND mediaID = '$mediaID' AND company = '$company' AND disabled != 1";
    $select_user_complete_result = mysqli_query($con->connection, $select_user_complete_sql);

    if ($select_user_complete_result) {



        if (mysqli_num_rows($select_user_complete_result) == 0) {
            ## no data
            ## insert
            $completed = 1;
            $disabled = 0;

            ## insert media enroll sql
            $sql = "INSERT INTO media_enroll(userID,mediaID,company,completed,completedTime,disabled,created) VALUES($userID,$mediaID,'$company',1,'$created',0,'$created')";
            $result = mysqli_query($con->connection, $sql);

            if ($result) {
                $response = ["status" => "ok", "code" => 200,  "message" => "add media completed successfully.", "data" => $request];
                $json = json_encode($response, JSON_UNESCAPED_UNICODE);
                return $json;
            } else {
                $response = ["status" => "error", "code" => 500,  "message" => "add media completed failed.", "error" => mysqli_error($con->connection)];
                $json = json_encode($response, JSON_UNESCAPED_UNICODE);
                return $json;
            }
        } else {

            $enroll = mysqli_fetch_assoc($select_user_complete_result);
            $enrollID = $enroll['id'];

            ## update sql
            $sql = "UPDATE media_enroll SET completed = 1, completedTime = '$created' WHERE id = $enrollID";
            $result = mysqli_query($con->connection, $sql);

            if ($result) {
                $response = ["status" => "ok", "code" => 200,  "message" => "update complete for this media.", "data" => $request];
                $json = json_encode($response, JSON_UNESCAPED_UNICODE);
                return $json;
            } else {
                $response = ["status" => "error", "code" => 500,  "message" => "update media enroll failed.", "error" => mysqli_error($con->connection),];
                $json = json_encode($response, JSON_UNESCAPED_UNICODE);
                return $json;
            }
        }
    } else {
        $response = ["status" => "error", "code" => 500,  "message" => "check media enroll failed.", "error" => mysqli_error($con->connection),];
        $json = json_encode($response, JSON_UNESCAPED_UNICODE);
        return $json;
    }
});


$app->get('/media/download/{url}', function ($req, $res, $args) use ($vimeo) {

    ## create database connection
    $con = new Database();

    $url = $args['url'];

    $response = ["status" => "ok", "code" => 200,  "message" => "get media download link.", "data" => $url];
    $json = json_encode($response, JSON_UNESCAPED_UNICODE);
    return $json;
});

$app->post('/unity/media/complete', function ($req, $res, $args) {

    ## create database connection
    $con = new Database();

    $request = $req->getParsedBody();

    $user = json_decode($request['user'], true);
    $media = json_decode($request['media'], true);


    $userID = $user['id'];
    $mediaID = $media['id'];

    $created = date("Y-m-d H:i:s", time());

    ## select user data
    $sql = "SELECT * FROM user WHERE id = '$userID'";
    $result = mysqli_query($con->connection, $sql);

    ## check query result
    if ($result) {
        if (mysqli_num_rows($result) > 0) {

            ## found data
            $userRow = mysqli_fetch_assoc($result);
            $userData = $userRow;
        } else {
            $response = ["status" => "error", "code" => 404,  "message" => "data not found.", "data" => []];
            $json = json_encode($response, JSON_UNESCAPED_UNICODE);
            return $json;
        }
    } else {
        $response = ["status" => "error", "code" => 500,  "message" => "can not get user data.", "error" => mysqli_error($con->connection)];
        $json = json_encode($response, JSON_UNESCAPED_UNICODE);
        return $json;
    }

    $company = $userData['company'];

    ## check enroll exist data
    $select_user_complete_sql = "SELECT * FROM media_enroll WHERE userID = $userID AND mediaID = $mediaID AND company = '$company' AND disabled != 1";
    $select_user_complete_result = mysqli_query($con->connection, $select_user_complete_sql);

    if ($select_user_complete_result) {

        if (mysqli_num_rows($select_user_complete_result) == 0) {
            ## no data
            ## insert
            $completed = 1;
            $disabled = 0;

            ## insert media enroll sql
            $sql = "INSERT INTO media_enroll(userID,mediaID,company,completed,completedTime,disabled,created) VALUES($userID,$mediaID,'$company',$completed,'$created',$disabled,'$created')";
            $result = mysqli_query($con->connection, $sql);

            if ($result) {
                $response = ["status" => "ok", "code" => 200,  "message" => "update media completed successfully.", "data" => $request];
                $json = json_encode($response, JSON_UNESCAPED_UNICODE);
                return $json;
            } else {
                $response = ["status" => "error", "code" => 500,  "message" => "update media completed failed.", "error" => mysqli_error($con->connection)];
                $json = json_encode($response, JSON_UNESCAPED_UNICODE);
                return $json;
            }
        } else {

            ## update sql
            $sql = "UPDATE media_enroll SET completed = 1, completedTime = '$created'";
            $result = mysqli_query($con->connection, $sql);

            if ($result) {
                $response = ["status" => "ok", "code" => 200,  "message" => "update complete for this media.", "data" => $request];
                $json = json_encode($response, JSON_UNESCAPED_UNICODE);
                return $json;
            } else {
                $response = ["status" => "error", "code" => 500,  "message" => "update media enroll failed.", "error" => mysqli_error($con->connection),];
                $json = json_encode($response, JSON_UNESCAPED_UNICODE);
                return $json;
            }
        }
    } else {
        $response = ["status" => "error", "code" => 500,  "message" => "check media enroll failed.", "error" => mysqli_error($con->connection),];
        $json = json_encode($response, JSON_UNESCAPED_UNICODE);
        return $json;
    }
});

