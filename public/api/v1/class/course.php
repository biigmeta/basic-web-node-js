<?php

$app->get('/courses', function ($req, $res, $args) {

    ## create database connection
    $con = new Database();

    ## create SQL and query
    $sql = "SELECT * FROM course WHERE disabled != 1";
    $result = mysqli_query($con->connection, $sql);

    ## check query result
    if ($result) {
        if (mysqli_num_rows($result) > 0) {

            ## found data

            $data = [];

            while ($row = mysqli_fetch_assoc($result)) {

                $data[] = $row;
            }

            $response = ["status" => "ok", "code" => 200, "message" => "get courses data successfully.", "data" => $data];
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

$app->get('/course/{id}', function ($req, $res, $args) {

    ## create database connection
    $con = new Database();
    $id = $args['id'];

    ## create SQL and query
    $sql = "SELECT * FROM course WHERE id = '$id' AND disabled != 1";
    $result = mysqli_query($con->connection, $sql);

    ## check query result
    if ($result) {
        if (mysqli_num_rows($result) > 0) {

            ## found data

            $data = [];

            while ($row = mysqli_fetch_assoc($result)) {

                $data[] = $row;
            }

            $response = ["status" => "ok", "code" => 200, "message" => "get course data successfully.", "data" => $data];
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

$app->get('/course/progress/{userID}', function ($req, $res, $args) {

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

    ## get course progress
    $sql = "SELECT * FROM course_enroll WHERE userID = '$userID' AND company = '$company' AND completed = 1 AND disabled != 1";
    $result = mysqli_query($con->connection, $sql);

    if ($result) {
        if (mysqli_num_rows($result) > 0) {

            $data = [];
            while ($row = mysqli_fetch_assoc($result)) {
                $data[] = $row;
            }

            $response = ["status" => "ok", "code" => 200,  "message" => "get course progress successfully.", "data" => $data];
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

$app->post('/course/update', function ($req, $res, $args) {

    ## create database connection
    $con = new Database();

    $request = $req->getParsedBody();

    $course = $request['course'];

    $created = date("Y-m-d H:i:s", time());

    $courseID = $course['id'];
    $courseTitleTH = trim($course['titleTH']);
    $courseTitleEN = trim($course['titleEN']);
    $courseNameTH = trim($course['nameTH']);
    $courseNameEN = trim($course['nameEN']);
    $courseDescriptionTH = trim($course['descriptionTH']);
    $courseDescriptionEN = trim($course['descriptionEN']);
    $courseThumbnail = trim($course['thumbnail']);

    ## select user data
    $sql = "UPDATE course SET titleTH = '$courseTitleTH', titleEN = '$courseTitleEN', nameTH = '$courseNameTH', nameEN = '$courseNameEN', descriptionTH = '$courseDescriptionTH', descriptionEN = '$courseDescriptionEN', thumbnail = '$courseThumbnail' WHERE id = '$courseID'";
    $result = mysqli_query($con->connection, $sql);

    ## check query result
    if ($result) {
        $response = ["status" => "ok", "code" => 200,  "message" => "update course data successfully.", "data" => $course];
        $json = json_encode($response, JSON_UNESCAPED_UNICODE);
        return $json;
    } else {
        $response = ["status" => "error", "code" => 500,  "message" => "can not update course data.", "error" => mysqli_error($con->connection)];
        $json = json_encode($response, JSON_UNESCAPED_UNICODE);
        return $json;
    }
});


$app->post('/course/enroll', function ($req, $res, $args) {

    ## create database connection
    $con = new Database();

    $request = $req->getParsedBody();

    $user = $request['user'];
    $course = $request['course'];


    $userID = $user['id'];
    $courseID = $course['id'];

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
    $select_user_complete_sql = "SELECT * FROM course_enroll WHERE userID = $userID AND courseID = $courseID AND company = '$company' AND disabled != 1";
    $select_user_complete_result = mysqli_query($con->connection, $select_user_complete_sql);

    if ($select_user_complete_result) {

        if (mysqli_num_rows($select_user_complete_result) == 0) {
            ## no data
            ## insert
            $completed = 0;
            $disabled = 0;

            ## insert course enroll sql
            $sql = "INSERT INTO course_enroll(userID,courseID,company,completed,completedTime,disabled,created) VALUES($userID,$courseID,'$company',$completed,null,$disabled,'$created')";
            $result = mysqli_query($con->connection, $sql);

            if ($result) {
                $response = ["status" => "ok", "code" => 200,  "message" => "update course enroll successfully.", "data" => $request];
                $json = json_encode($response, JSON_UNESCAPED_UNICODE);
                return $json;
            } else {
                $response = ["status" => "error", "code" => 500,  "message" => "update course enroll failed.", "error" => mysqli_error($con->connection)];
                $json = json_encode($response, JSON_UNESCAPED_UNICODE);
                return $json;
            }
        } else {

            ## already data
            $response = ["status" => "ok", "code" => 200,  "message" => "you are already enroll this course.", "data" => $request];
            $json = json_encode($response, JSON_UNESCAPED_UNICODE);
            return $json;
        }
    } else {
        $response = ["status" => "error", "code" => 500,  "message" => "check course enroll failed.", "error" => mysqli_error($con->connection),];
        $json = json_encode($response, JSON_UNESCAPED_UNICODE);
        return $json;
    }
});

$app->post('/course/complete', function ($req, $res, $args) {

    ## create database connection
    $con = new Database();

    $request = $req->getParsedBody();

    $user = $request['user'];
    $course = $request['course'];

    $userID = $user['id'];
    $courseID = $course['id'];

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
    $select_user_complete_sql = "SELECT * FROM course_enroll WHERE userID = $userID AND courseID = $courseID AND company = '$company' AND disabled != 1";
    $select_user_complete_result = mysqli_query($con->connection, $select_user_complete_sql);

    if ($select_user_complete_result) {

        if (mysqli_num_rows($select_user_complete_result) == 0) {
            ## no data
            ## insert
            $completed = 1;
            $disabled = 0;

            ## insert course enroll sql
            $sql = "INSERT INTO course_enroll(userID,courseID,company,completed,completedTime,disabled,created) VALUES($userID,$courseID,'$company',$completed,'$created',$disabled,'$created')";
            $result = mysqli_query($con->connection, $sql);

            if ($result) {
                $response = ["status" => "ok", "code" => 200,  "message" => "update course completed successfully.", "data" => $request];
                $json = json_encode($response, JSON_UNESCAPED_UNICODE);
                return $json;
            } else {
                $response = ["status" => "error", "code" => 500,  "message" => "update course completed failed.", "error" => mysqli_error($con->connection)];
                $json = json_encode($response, JSON_UNESCAPED_UNICODE);
                return $json;
            }
        } else {

            $enroll = mysqli_fetch_assoc($select_user_complete_result);
            $enrollID = $enroll['id'];
            ## update sql
            $sql = "UPDATE course_enroll SET completed = 1, completedTime = '$created' WHERE id =  $enrollID";
            $result = mysqli_query($con->connection, $sql);

            if ($result) {
                $response = ["status" => "ok", "code" => 200,  "message" => "update complete for this course.", "data" => $request];
                $json = json_encode($response, JSON_UNESCAPED_UNICODE);
                return $json;
            } else {
                $response = ["status" => "error", "code" => 500,  "message" => "update course enroll failed.", "error" => mysqli_error($con->connection),];
                $json = json_encode($response, JSON_UNESCAPED_UNICODE);
                return $json;
            }
        }
    } else {
        $response = ["status" => "error", "code" => 500,  "message" => "check course enroll failed.", "error" => mysqli_error($con->connection),];
        $json = json_encode($response, JSON_UNESCAPED_UNICODE);
        return $json;
    }
});


$app->post('/course/incomplete', function ($req, $res, $args) {

    ## create database connection
    $con = new Database();

    $request = $req->getParsedBody();

    $user = $request['user'];
    $course = $request['course'];

    $userID = $user['id'];
    $courseID = $course['id'];

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
    $select_user_complete_sql = "SELECT * FROM course_enroll WHERE userID = $userID AND courseID = $courseID AND company = '$company' AND disabled != 1";
    $select_user_complete_result = mysqli_query($con->connection, $select_user_complete_sql);

    if ($select_user_complete_result) {

        if (mysqli_num_rows($select_user_complete_result) == 0) {
            ## no data
            $response = ["status" => "ok", "code" => 200,  "message" => "already incomplete for this course.", "data" => $request];
            $json = json_encode($response, JSON_UNESCAPED_UNICODE);
            return $json;
        } else {

            $enroll = mysqli_fetch_assoc($select_user_complete_result);
            $enrollID = $enroll['id'];
            ## update sql
            $sql = "UPDATE course_enroll SET completed = 0, completedTime = '$created' WHERE id =  $enrollID";
            $result = mysqli_query($con->connection, $sql);

            if ($result) {
                $response = ["status" => "ok", "code" => 200,  "message" => "update incomplete for this course.", "data" => $request];
                $json = json_encode($response, JSON_UNESCAPED_UNICODE);
                return $json;
            } else {
                $response = ["status" => "error", "code" => 500,  "message" => "update course incomplete failed.", "error" => mysqli_error($con->connection),];
                $json = json_encode($response, JSON_UNESCAPED_UNICODE);
                return $json;
            }
        }
    } else {
        $response = ["status" => "error", "code" => 500,  "message" => "check course enroll failed.", "error" => mysqli_error($con->connection),];
        $json = json_encode($response, JSON_UNESCAPED_UNICODE);
        return $json;
    }
});

$app->post('/unity/course/complete', function ($req, $res, $args) {

    ## create database connection
    $con = new Database();

    $request = $req->getParsedBody();

    $user = json_decode($request['user'], true);
    $course = json_decode($request['course'], true);


    $userID = $user['id'];
    $courseID = $course['id'];

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
    $select_user_complete_sql = "SELECT * FROM course_enroll WHERE userID = $userID AND courseID = $courseID AND company = '$company' AND disabled != 1";
    $select_user_complete_result = mysqli_query($con->connection, $select_user_complete_sql);

    if ($select_user_complete_result) {

        if (mysqli_num_rows($select_user_complete_result) == 0) {
            ## no data
            ## insert
            $completed = 1;
            $disabled = 0;

            ## insert course enroll sql
            $sql = "INSERT INTO course_enroll(userID,courseID,company,completed,completedTime,disabled,created) VALUES($userID,$courseID,'$company',$completed,'$created',$disabled,'$created')";
            $result = mysqli_query($con->connection, $sql);

            if ($result) {
                $response = ["status" => "ok", "code" => 200,  "message" => "update course completed successfully.", "data" => $request];
                $json = json_encode($response, JSON_UNESCAPED_UNICODE);
                return $json;
            } else {
                $response = ["status" => "error", "code" => 500,  "message" => "update course completed failed.", "error" => mysqli_error($con->connection)];
                $json = json_encode($response, JSON_UNESCAPED_UNICODE);
                return $json;
            }
        } else {
            ## already data
            $response = ["status" => "ok", "code" => 200,  "message" => "you are already complete this course.", "data" => $request];
            $json = json_encode($response, JSON_UNESCAPED_UNICODE);
            return $json;
        }
    } else {
        $response = ["status" => "error", "code" => 500,  "message" => "check course enroll failed.", "error" => mysqli_error($con->connection),];
        $json = json_encode($response, JSON_UNESCAPED_UNICODE);
        return $json;
    }
});
