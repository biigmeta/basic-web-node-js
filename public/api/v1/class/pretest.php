<?php

$app->get('/pretest/ar', function ($req, $res, $args) {

    ## create database connection
    $con = new Database();

    ## create SQL and query
    $sql = "SELECT * FROM pretest WHERE mode = 'ar' ORDER BY id DESC LIMIT 1";
    $result = mysqli_query($con->connection, $sql);

    ## check query result
    if ($result) {
        if (mysqli_num_rows($result) > 0) {

            ## found data
            $data = [];

            while ($row = mysqli_fetch_assoc($result)) {

                ## get question order from db
                $row['questions'] = json_decode($row['questions']);
                ## assign order to some var
                $questionOrder = $row['questions'];
                ## clear order
                $row['questions'] = [];

                foreach ($questionOrder as $questionID) {

                    ## get question by question id
                    $get_question_sql = "SELECT * FROM question WHERE id = '$questionID'";
                    $get_question_result = mysqli_query($con->connection, $get_question_sql);

                    if ($get_question_result) {

                        $questions = mysqli_fetch_assoc($get_question_result);

                        ## assign question
                        $row['questions'][] = $questions;
                    } else {
                        ## no question assign empty array
                        $row['questions'] = [];
                    }
                }

                $data[] = $row;
            }

            $response = ["status" => "ok",  "code" => 200, "message" => "get pre-test data successfully.", "data" => $data];
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

        http_response_code(400);
        $response = ["status" => "error", "code" => 500,  "message" => "get pre-test data failed.", "error" => mysqli_error($con->connection)];
        $json = json_encode($response, JSON_UNESCAPED_UNICODE);
        return $json;
    }
});

$app->get('/pretest/traditional', function ($req, $res, $args) {

    ## create database connection
    $con = new Database();

    ## create SQL and query
    $sql = "SELECT * FROM pretest WHERE mode = 'traditional' ORDER BY id DESC LIMIT 1";
    $result = mysqli_query($con->connection, $sql);

    ## check query result
    if ($result) {
        if (mysqli_num_rows($result) > 0) {

            ## found data
            $data = [];

            while ($row = mysqli_fetch_assoc($result)) {

                ## get question order from db
                $row['questions'] = json_decode($row['questions']);
                ## assign order to some var
                $questionOrder = $row['questions'];
                ## clear order
                $row['questions'] = [];

                foreach ($questionOrder as $questionID) {

                    ## get question by question id
                    $get_question_sql = "SELECT * FROM question WHERE id = '$questionID'";
                    $get_question_result = mysqli_query($con->connection, $get_question_sql);

                    if ($get_question_result) {

                        $questions = mysqli_fetch_assoc($get_question_result);

                        ## assign question
                        $row['questions'][] = $questions;
                    } else {
                        ## no question assign empty array
                        $row['questions'] = [];
                    }
                }

                $data[] = $row;
            }

            $response = ["status" => "ok", "code" => 200,  "message" => "get pre-test data successfully.", "data" => $data];
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
        $response = ["status" => "error", "code" => 500,  "message" => "get pre-test data failed.", "error" => mysqli_error($con->connection)];
        $json = json_encode($response, JSON_UNESCAPED_UNICODE);
        return $json;
    }
});

$app->get('/pretest/avg', function ($req, $res, $args) {

    ## create database connection
    $con = new Database();

    ## create SQL and query
    $sql = "SELECT AVG(score) as scoreAvg,AVG(usedTime) as usedTimeAvg,COUNT(*) as qty FROM pretest_log";
    $result = mysqli_query($con->connection, $sql);

    ## check query result
    if ($result) {
        $row = mysqli_fetch_assoc($result);
        $response = ["status" => "ok", "code" => 200,  "message" => "get pretest avg successfully.", "data" => $row];
        $json = json_encode($response, JSON_UNESCAPED_UNICODE);
        return $json;
    } else {
        $response = ["status" => "error", "code" => 500, "message" => "get pretest avg failed.", "error" => mysqli_error($con->connection)];
        $json = json_encode($response, JSON_UNESCAPED_UNICODE);
        return $json;
    }
});



$app->post('/pretest', function ($req, $res, $args) {

    ## create database connection
    $con = new Database();

    ## add pre test just add question id to `questions`

    $request = $req->getParsedBody();
    $questions = json_encode($request['questions']);
    $disabled = 0;
    $created = date('Y-m-d H:i:s', time());

    ## create SQL and query
    $sql = "INSERT INTO pretest(questions,disabled,created) VALUES('$questions','$disabled','$created')";
    $result = mysqli_query($con->connection, $sql);

    ## check query result
    if ($result) {
        $response = ["status" => "ok", "code" => 200,  "message" => "add pretest successfully.", "data" => $request];
        $json = json_encode($response, JSON_UNESCAPED_UNICODE);
        return $json;
    } else {
        $response = ["status" => "error", "code" => 500, "message" => "add pretest failed.", "error" => mysqli_error($con->connection)];
        $json = json_encode($response, JSON_UNESCAPED_UNICODE);
        return $json;
    }
});


$app->post('/pretest/log', function ($req, $res, $args) {

    ## create database connection
    $con = new Database();

    $request = $req->getParsedBody();

    $user = $request['user'];

    $userID = $user['id'];
    $created = date("Y-m-d H:i:s", time());

    ## assign test data
    $usedTime = $request['usedTime'];
    $test = $request['test'];

    $testID = $test['id'];
    $mode = $test['mode'];
    $company = $user['company'];
    $tempQuestionAnswers = [];

    foreach ($test['questions'] as $value) {
        $tempQuestionAnswers[] = intval($value['answer']);
    }

    $questionAnswers = json_encode($tempQuestionAnswers);
    $userAnswers = json_encode($user['answers']);

    $score = $request['score'];

    ## insert test log
    $sql = "INSERT INTO pretest_log(userID,testID,questionAnswers,userAnswers,score,usedTime,mode,company,created) VALUES('$userID','$testID','$questionAnswers','$userAnswers',$score,$usedTime,'$mode','$company','$created')";
    $result = mysqli_query($con->connection, $sql);

    if ($result) {
        $response = ["status" => "ok", "code" => 200,  "message" => "insert test log to pretest_log success.", "data" => $request];
        $json = json_encode($response, JSON_UNESCAPED_UNICODE);
        return $json;
    } else {
        $response = ["status" => "error", "code" => 500,  "message" => "insert test log to pretest_log failed.", "data" => $request];
        $json = json_encode($response, JSON_UNESCAPED_UNICODE);
        return $json;
    }
});

$app->post('/unity/pretest/log', function ($req, $res, $args) {

    ## create database connection
    $con = new Database();

    $request = $req->getParsedBody();

    $user = json_decode($request['user'], true);
    $userID = $user['id'];
    $created = date("Y-m-d H:i:s", time());

    ## assign test data
    $usedTime = $request['usedTime'];
    $test = json_decode($request['test'], true);

    $testID = $test['id'];
    $tempQuestionAnswers = [];

    foreach ($test['questions'] as $value) {
        $tempQuestionAnswers[] = intval($value['answer']);
    }

    $questionAnswers = json_encode($tempQuestionAnswers);
    $userAnswers = json_encode($user['answers']);

    $score = $request['score'];

    ## insert test log
    $sql = "INSERT INTO pretest_log(userID,testID,questionAnswers,userAnswers,score,usedTime,created) VALUES('$userID','$testID','$questionAnswers','$userAnswers',$score,$usedTime,'$created')";
    $result = mysqli_query($con->connection, $sql);

    if ($result) {
        $response = ["status" => "ok", "code" => 200,  "message" => "insert test log to pretest_log success.", "data" => $request];
        $json = json_encode($response, JSON_UNESCAPED_UNICODE);
        return $json;
    } else {
        $response = ["status" => "error", "code" => 500,  "message" => "insert test log to pretest_log failed.", "data" => $request];
        $json = json_encode($response, JSON_UNESCAPED_UNICODE);
        return $json;
    }
});
