<?php

$app->post('/question/update', function ($req, $res, $args) {

    ## create database connection
    $con = new Database();

    $request = $req->getParsedBody();

    $question = $request['question'];
    $questionID = $question['id'];
    $questionTH = mysqli_real_escape_string($con->connection, $question['questionTH']);
    $questionEN = mysqli_real_escape_string($con->connection, $question['questionEN']);
    $choiceTH_01 = mysqli_real_escape_string($con->connection, $question['choiceTH_01']);
    $choiceEN_01 = mysqli_real_escape_string($con->connection, $question['choiceEN_01']);
    $choiceTH_02 = mysqli_real_escape_string($con->connection, $question['choiceTH_02']);
    $choiceEN_02 = mysqli_real_escape_string($con->connection, $question['choiceEN_02']);
    $choiceTH_03 = mysqli_real_escape_string($con->connection, $question['choiceTH_03']);
    $choiceEN_03 = mysqli_real_escape_string($con->connection, $question['choiceEN_03']);
    $choiceTH_04 = mysqli_real_escape_string($con->connection, $question['choiceTH_04']);
    $choiceEN_04 = mysqli_real_escape_string($con->connection, $question['choiceEN_04']);
    $answer = $question['answer'];

    ## check question exist
    $sql = "SELECT * FROM question WHERE id = '$questionID'";
    $result = mysqli_query($con->connection, $sql);

    ## check query result
    if ($result) {
        if (mysqli_num_rows($result) == 0) {
            $response = ["status" => "error", "code" => 404, "message" => "data not found.", "data" => null];
            $json = json_encode($response, JSON_UNESCAPED_UNICODE);
            return $json;
        }
    } else {
        $response = ["status" => "error", "code" => 500, "message" => "check question failed.", "error" => mysqli_error($con->connection)];
        $json = json_encode($response, JSON_UNESCAPED_UNICODE);
        return $json;
    }

    ## update question
    $sql = "UPDATE question SET questionTH = '$questionTH', questionEN = '$questionEN', choiceTH_01 = '$choiceTH_01', choiceEN_01 = '$choiceEN_01', choiceTH_02 = '$choiceTH_02', choiceEN_02 = '$choiceEN_02', choiceTH_03 = '$choiceTH_03', choiceEN_03 = '$choiceEN_03', choiceTH_04 = '$choiceTH_04', choiceEN_04 = '$choiceEN_04', answer = $answer WHERE id = '$questionID'";
    $result = mysqli_query($con->connection, $sql);

    ## check query result
    if ($result) {
        $response = ["status" => "ok", "code" => 200, "message" => "update question successfully.", "data" => $question];
        $json = json_encode($response, JSON_UNESCAPED_UNICODE);
        return $json;
    } else {
        $response = ["status" => "error", "code" => 500, "message" => "update question failed.", "error" => mysqli_error($con->connection)];
        $json = json_encode($response, JSON_UNESCAPED_UNICODE);
        return $json;
    }
});
