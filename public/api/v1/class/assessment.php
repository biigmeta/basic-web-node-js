<?php




$app->get('/assessment/{mode}', function ($req, $res, $args) {

    ## create database connection
    $con = new Database();
    $mode = $args['mode'];

    ## create SQL and query
    $sql = "SELECT * FROM assessment WHERE mode = '$mode' AND disabled != 1 ORDER BY id DESC LIMIT 1";
    $result = mysqli_query($con->connection, $sql);

    ## check query result
    if ($result) {
        if (mysqli_num_rows($result) > 0) {

            ## found data
            $data = [];

            while ($row = mysqli_fetch_assoc($result)) {

                $row['groupOrder'] = json_decode($row['groupOrder'], true);

                if (count($row['groupOrder']) > 0) {

                    $groups = [];
                    ## get groups data from groupOrder
                    $impGroup = implode(',', $row['groupOrder']);

                    $sql = "SELECT * FROM assessment_group WHERE id IN ($impGroup)";
                    $resultGroup = mysqli_query($con->connection, $sql);

                    if ($resultGroup) {
                        if (mysqli_num_rows($resultGroup) > 0) {
                            while ($rowGroup = mysqli_fetch_assoc($resultGroup)) {

                                $rowGroup['questionOrder'] = json_decode($rowGroup['questionOrder'], true);
                                $impQuestion = implode(',', $rowGroup['questionOrder']);

                                ## get questions data from questionOrder
                                $sql = "SELECT * FROM assessment_question WHERE id IN ($impQuestion)";
                                $resultQuestion = mysqli_query($con->connection, $sql);

                                if ($resultQuestion) {
                                    if (mysqli_num_rows($resultQuestion) > 0) {
                                        while ($rowQuestion = mysqli_fetch_assoc($resultQuestion)) {
                                            $rowGroup['questions'][] = $rowQuestion;
                                        }
                                    } else {
                                        $rowGroup['questions'] = [];
                                    }
                                } else {
                                    $response = ["status" => "error", "code" => 500,  "message" => "can not get questions.", "error" => mysqli_error($con->connection), "sql" => $sql];
                                    $json = json_encode($response, JSON_UNESCAPED_UNICODE);
                                    return $json;
                                }

                                $groups[] = $rowGroup;
                            }
                        } else {
                            $groups = [];
                        }
                    } else {
                        $response = ["status" => "error", "code" => 500,  "message" => "can not get assessement groups.", "error" => mysqli_error($con->connection)];
                        $json = json_encode($response, JSON_UNESCAPED_UNICODE);
                        return $json;
                    }

                    $row['groups'] =  $groups;
                } else {
                    $row['groups'] = [];
                }

                $data[] = $row;
            }

            $response = ["status" => "ok", "code" => 200,  "message" => "get assessment data successfully.", "data" => $data];
            $json = json_encode($response, JSON_UNESCAPED_UNICODE);
            return $json;
        } else {

            ## not found data
            $response = ["status" => "error", "code" => 404,  "message" => "data not found.", "data" => null];
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

$app->post('/assessment/log', function ($req, $res, $args) {

    ## create database connection
    $con = new Database();

    $request = $req->getParsedBody();
    $answers =  json_encode($request['answers'], JSON_UNESCAPED_UNICODE);

    $user = $request['user'];
    $assessment = $request['assessment'];

    $userID = $user['id'];
    $company = $user['company'];
    $assessmentID = $assessment['id'];
    $mode = $assessment['mode'];
    $type = $assessment['type'];

    $created = date("Y-m-d H:i:s", time());

    ## create SQL and query
    $sql = "INSERT INTO assessment_log(userID,assessmentID,answers,mode,type,company,created) VALUES($userID,$assessmentID,'$answers','$mode','$type','$company','$created')";
    $result = mysqli_query($con->connection, $sql);

    ## check query result
    if ($result) {
        $response = ["status" => "ok", "code" => 200,  "message" => "add survey data successfully.", "data" => $request];
        $json = json_encode($response, JSON_UNESCAPED_UNICODE);
        return $json;
    } else {

        ## mysqli error
        $response = ["status" => "error", "code" => 500,  "message" => "can not connect database.", "error" => mysqli_error($con->connection)];
        $json = json_encode($response, JSON_UNESCAPED_UNICODE);
        return $json;
    }
});


$app->get('/assessments/log', function ($req, $res, $args) {

    ## create database connection
    $con = new Database();


    $userID = $args['userID'];

    ## select assessment log
    $sql = "SELECT * FROM assessment_log WHERE disabled != 1 ORDER BY id DESC";
    $result = mysqli_query($con->connection, $sql);

    ## check query result
    if ($result) {
        if (mysqli_num_rows($result) > 0) {

            ## found data
            $data = [];

            while ($row = mysqli_fetch_assoc($result)) {

                $row['answers'] = json_decode($row['answers'], true);

                $data[] = $row;
            }

            $response = ["status" => "ok", "code" => 200,  "message" => "get assessment log data successfully.", "data" => $data];
            $json = json_encode($response, JSON_UNESCAPED_UNICODE);
            return $json;
        } else {

            ## not found data
            $response = ["status" => "error", "code" => 404,  "message" => "data not found.", "data" => null];
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


$app->get('/assessment/log/{userID}', function ($req, $res, $args) {

    ## create database connection
    $con = new Database();

    $userID = $args['userID'];

    ## get user data
    $sql = "SELECT * FROM user WHERE id = $userID";
    $result = mysqli_query($con->connection, $sql);

    ## check query result
    if ($result) {
        if (mysqli_num_rows($result) > 0) {
            $user_row = mysqli_fetch_assoc($result);
            $company = $user_row['company'];
        } else {
            $response = ["status" => "error", "code" => 404,  "message" => "user not found.", "data" => null];
            $json = json_encode($response, JSON_UNESCAPED_UNICODE);
            return $json;
        }
    } else {
        $response = ["status" => "error", "code" => 500,  "message" => "can not get user data for query assessments.", "error" => mysqli_error($con->connection)];
        $json = json_encode($response, JSON_UNESCAPED_UNICODE);
        return $json;
    }

    ## select assessment log
    $sql = "SELECT * FROM assessment_log WHERE userID = $userID AND company = '$company' AND disabled != 1 ORDER BY id DESC";
    $result = mysqli_query($con->connection, $sql);

    ## check query result
    if ($result) {
        if (mysqli_num_rows($result) > 0) {

            ## found data
            $data = [];

            while ($row = mysqli_fetch_assoc($result)) {

                $row['answers'] = json_decode($row['answers'], true);

                $data[] = $row;
            }

            $response = ["status" => "ok", "code" => 200,  "message" => "get assessment log data successfully.", "data" => $data];
            $json = json_encode($response, JSON_UNESCAPED_UNICODE);
            return $json;
        } else {

            ## not found data
            $response = ["status" => "error", "code" => 404,  "message" => "data not found.", "data" => null];
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
