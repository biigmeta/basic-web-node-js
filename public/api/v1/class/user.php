<?php

$app->post('/user/me', function ($req, $res, $args) {

    ## create database connection
    $con = new Database();

    ## assign request

    $request = $req->getParsedBody();
    $token = $request['token'];

    $payload = jwtDecode($token);
    $user = $payload['data'];

    $id = $user->id;

    ## create SQL and query
    $sql = "SELECT * FROM user WHERE id = '$id' AND disabled != 1";
    $result = mysqli_query($con->connection, $sql);

    ## check query result
    if ($result) {
        if (mysqli_num_rows($result) > 0) {

            ## found data

            $data = [];

            while ($row = mysqli_fetch_assoc($result)) {

                unset($row['password']);

                ## get last activity
                $sql = "SELECT * FROM activity_log WHERE userID = '$id' AND disabled != 1 ORDER BY created DESC LIMIT 1";
                $lastact_result = mysqli_query($con->connection, $sql);

                if ($lastact_result) {
                    if (mysqli_num_rows($lastact_result) > 0) {
                        $lastact_row = mysqli_fetch_assoc($lastact_result);
                        $row['lastActivity'] = $lastact_row['created'];
                    } else {
                        $row['lastActivity'] = $row['created'];
                    }
                } else {
                    $row['lastActivity'] = $row['created'];
                }

                $data[] = $row;
            }

            $response = ["status" => "ok", "code" => 200,  "message" => "get user data successfully.", "data" => $data];
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


$app->get('/user/{id}', function ($req, $res, $args) {

    ## create database connection
    $con = new Database();

    ## assign request
    $id = $args['id'];

    ## create SQL and query
    $sql = "SELECT * FROM user WHERE id = '$id' AND disabled != 1";
    $result = mysqli_query($con->connection, $sql);

    ## check query result
    if ($result) {
        if (mysqli_num_rows($result) > 0) {

            ## found data

            $data = [];

            while ($row = mysqli_fetch_assoc($result)) {

                unset($row['password']);

                $data[] = $row;
            }

            $response = ["status" => "ok", "code" => 200,  "message" => "get user data successfully.", "data" => $data];
            $json = json_encode($response, JSON_UNESCAPED_UNICODE);
            return $json;
        } else {

            ## not found data
            $response = ["status" => "error", "code" => 404,  "message" => "data not found.", "error" => null];
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

######################################
##     GET ข้อมูล User เฉพาะ User     ##
######################################
$app->get('/users', function ($req, $res, $args) {

    ## create database connection
    $con = new Database();

    ## create SQL and query
    $sql = "SELECT * FROM user WHERE role = 'user' AND disabled != 1";
    $result = mysqli_query($con->connection, $sql);

    ## check query result
    if ($result) {
        if (mysqli_num_rows($result) > 0) {

            ## found data

            $data = [];

            while ($row = mysqli_fetch_assoc($result)) {
                unset($row['password']);
                $data[] = $row;
            }

            $response = ["status" => "ok", "code" => 200,  "message" => "get users data successfully.", "data" => $data];
            $json = json_encode($response, JSON_UNESCAPED_UNICODE);
            return $json;
        } else {

            ## not found data
            $response = ["status" => "error", "code" => 404,  "message" => "data not found.", "error" => null];
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

######################################
##       GET ข้อมูล User ทั้งหมด       ##
######################################
$app->get('/users/all', function ($req, $res, $args) {

    ## create database connection
    $con = new Database();

    ## create SQL and query
    $sql = "SELECT * FROM user";
    $result = mysqli_query($con->connection, $sql);

    ## check query result
    if ($result) {
        if (mysqli_num_rows($result) > 0) {

            ## found data

            $data = [];

            while ($row = mysqli_fetch_assoc($result)) {

                unset($row['password']);

                $userID = $row['id'];

                ## get last activity
                $sql = "SELECT * FROM activity_log WHERE userID = '$userID' AND disabled != 1 ORDER BY created DESC LIMIT 1";
                $lastact_result = mysqli_query($con->connection, $sql);

                if ($lastact_result) {
                    if (mysqli_num_rows($lastact_result) > 0) {
                        $lastact_row = mysqli_fetch_assoc($lastact_result);
                        $row['lastActivity'] = $lastact_row['created'];
                    } else {
                        $row['lastActivity'] = $row['created'];
                    }
                } else {
                    $row['lastActivity'] = $row['created'];
                }

                $data[] = $row;
            }

            $response = ["status" => "ok", "code" => 200,  "message" => "get users data successfully.", "data" => $data];
            $json = json_encode($response, JSON_UNESCAPED_UNICODE);
            return $json;
        } else {

            ## not found data
            $response = ["status" => "error", "code" => 404,  "message" => "data not found.", "error" => null];
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

######################################
##########     ลงทะเบียน      #########
######################################
$app->post('/user', function ($req, $res, $args) {

    ## create database connection
    $con = new Database();

    ## assign request
    if (!isset($req->getParsedBody()['user'])) {
        $response = ["status" => "error", "code" => 403,  "message" => "no user data for add to database.", "error" => "method not allowed."];
        $json = json_encode($response, JSON_UNESCAPED_UNICODE);
        return $json;
    }

    $request = $req->getParsedBody();
    $user = $request['user'];

    $firstname = $user['firstname'];
    $lastname = $user['lastname'];
    $genderCode = $user['genderCode'];
    $gender = $user['gender'];
    $birthyear = $user['birthyear'];

    // $educationID = $user['educationID'];
    // $education = $user['education'];
    // $educationLevel = $user['educationLevel'];
    $educationID = $user['educationID'];
    $educationNameTH = $user['educationNameTH'];
    $educationNameEN = $user['educationNameEN'];
    $educationLevelTH = $user['educationLevelTH'];
    $educationLevelEN = $user['educationLevelEN'];

    $gpax = $user['gpax'];
    $majorID = $user['majorID'];
    $major = $user['major'];
    $institutionID = $user['institutionID'];
    $institution = $user['institution'];
    $internExp = $user['internExp'];
    $workExp = $user['workExp'];
    $machineManual = $user['machineManual'];
    $machineComputer = $user['machineComputer'];
    $language = $user['language'];
    $company = $user['company'];
    $introHololens = 0;
    $role = "user";
    $disabled = 0;
    $created = date('Y-m-d H:i:s', time());

    $sql = "INSERT INTO user(firstname,lastname,password,email,tel,genderCode,gender,birthyear,educationID,edu_nameTH,edu_nameEN,edu_levelTH,edu_levelEN,gpax,majorID,major,institutionID,institution,internExp,workExp,machineManual,machineComputer,language,company,introHololens,role,disabled,created) 
    VALUES ('$firstname','$lastname',?,?,?,'$genderCode','$gender','$birthyear',$educationID,'$educationNameTH','$educationNameEN','$educationLevelTH','$educationLevelEN',$gpax,$majorID,'$major',$institutionID,'$institution',$internExp,$workExp,$machineManual,$machineComputer,'$language','$company',$introHololens,'$role',$disabled,'$created')";

    $stmt = mysqli_prepare($con->connection, $sql);
    mysqli_stmt_bind_param($stmt, 'sss', $hashPassword, $email, $tel);

    $password = $user['password'];
    $hashPassword = password_hash($password, PASSWORD_DEFAULT);
    $email = $user['email'];
    $tel = $user['tel'];

    $result = mysqli_stmt_execute($stmt);

    if ($result) {
        $response = ["status" => "ok", "code" => 200,  "message" => "register successfully.", "data" => $request];
        $json = json_encode($response, JSON_UNESCAPED_UNICODE);
        return $json;
    } else {
        $response = ["status" => "error", "code" => 500,  "message" => "register failed.", "error" => mysqli_error($con->connection)];
        $json = json_encode($response, JSON_UNESCAPED_UNICODE);
        return $json;
    }
});

$app->post('/user/update', function ($req, $res, $args) {

    ## create database connection
    $con = new Database();

    ## assign request
    if (!isset($req->getParsedBody()['user'])) {
        $response = ["status" => "error", "code" => 403,  "message" => "no user data for add to database.", "error" => "method not allowed."];
        $json = json_encode($response, JSON_UNESCAPED_UNICODE);
        return $json;
    }

    $request = $req->getParsedBody();
    $user = $request['user'];
    $id = $user['id'];
    $firstname = mysqli_real_escape_string($con->connection, $user['firstname']);
    $lastname = mysqli_real_escape_string($con->connection, $user['lastname']);
    $tel = mysqli_real_escape_string($con->connection, $user['tel']);
    $birthyear = $user['birthyear'];
    $genderCode = $user['genderCode'];
    $gender = mysqli_real_escape_string($con->connection, $user['gender']);
    $educationID = $user['educationID'];
    $education =  mysqli_real_escape_string($con->connection, $user['education']);
    $majorID = $user['majorID'];
    $major = mysqli_real_escape_string($con->connection, $user['major']);
    $institution = mysqli_real_escape_string($con->connection, $user['institution']);
    $company = mysqli_real_escape_string($con->connection, $user['company']);
    $disabled = $user['disabled'];
    $language = $user['language'];

    $sql = "UPDATE user SET firstname = '$firstname', lastname = '$lastname', tel = '$tel', birthyear = $birthyear, genderCode = '$genderCode', gender = '$gender',  
    educationID = $educationID, education = '$education', majorID = $majorID , major = '$major', institution = '$institution', company = '$company',language = '$language', `disabled` = '$disabled' WHERE id = $id";
    $result = mysqli_query($con->connection, $sql);

    if ($result) {
        $response = ["status" => "ok", "code" => 200,  "message" => "update user data successfully.", "data" => $user];
        $json = json_encode($response, JSON_UNESCAPED_UNICODE);
        return $json;
    } else {
        $response = ["status" => "error", "code" => 500,  "message" => "update data failed.", "data" => mysqli_error($con->connection), "user" => $user];
        $json = json_encode($response, JSON_UNESCAPED_UNICODE);
        return $json;
    }
});


$app->post('/user/login', function ($req, $res, $args) {

    ## create database connection
    $con = new Database();

    ## assign request
    $request = $req->getParsedBody();

    $email = $request['email'];
    $password = $request['password'];

    if ($email == "" && $password == "") {
        $loginForm = json_decode($request['loginForm'], true);
        $email = $loginForm['email'];
        $password = $loginForm['password'];
    }

    $sql = "SELECT * FROM user WHERE email = '$email'";
    $result = mysqli_query($con->connection, $sql);

    if ($result) {
        if (mysqli_num_rows($result) > 0) {
            $row = mysqli_fetch_assoc($result);
            if (verifyHashPassword($password, $row['password'])) {

                if ($row['disabled'] == true) {
                    $response = ["status" => "error", "code" => 400,  "message" => "account banned.", "error" => "account banned."];
                    $json = json_encode($response, JSON_UNESCAPED_UNICODE);
                    return $json;
                } else {
                    $data = [];
                    unset($row['password']);
                    $data[] = $row;

                    $token = jwtEncode($row);

                    $response = ["status" => "ok", "code" => 200,  "message" => "login successfully.", "data" => $data, "token" => $token];
                    $json = json_encode($response, JSON_UNESCAPED_UNICODE);
                    return $json;
                }
            } else {
                $response = ["status" => "error", "code" => 400,  "message" => "password incorrect.", "error" => "password incorrect."];
                $json = json_encode($response, JSON_UNESCAPED_UNICODE);
                return $json;
            }
        } else {

            $response = ["status" => "error", "code" => 404, "message" => "data not found.", "data" => null];
            $json = json_encode($response, JSON_UNESCAPED_UNICODE);
            return $json;
        }
    } else {
        $response = ["status" => "error", "code" => 500,  "message" => "log in failed.", "error" => mysqli_error($con->connection)];
        $json = json_encode($response, JSON_UNESCAPED_UNICODE);
        return $json;
    }
});

$app->get('/user/progress/{userID}', function ($req, $res, $args) {

    $response = ["status" => "error", "code" => 400,  "message" => "this api deprecated.", "data" => null];
    $json = json_encode($response, JSON_UNESCAPED_UNICODE);
    return $json;


    ## create database connection
    $con = new Database();


    $userID = $args['userID'];

    ## get course progress
    $sql = "SELECT * FROM course_enroll WHERE userID = '$userID' AND completed = 1 AND disabled != 1";
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





##################### will be deprecate #########################
// $app->post('/user/course/enroll', function ($req, $res, $args) {

//     ## create database connection
//     $con = new Database();

//     $request = $req->getParsedBody();

//     $user = $request['user'];
//     $course = $request['course'];


//     $userID = $user['id'];
//     $courseID = $course['id'];

//     $created = date("Y-m-d H:i:s", time());

//     ## check enroll exist data
//     $select_user_complete_sql = "SELECT * FROM course_enroll WHERE userID = $userID AND courseID = $courseID AND disabled != 1";
//     $select_user_complete_result = mysqli_query($con->connection, $select_user_complete_sql);

//     if ($select_user_complete_result) {

//         if (mysqli_num_rows($select_user_complete_result) == 0) {
//             ## no data
//             ## insert
//             $completed = 0;
//             $disabled = 0;

//             ## insert course enroll sql
//             $sql = "INSERT INTO course_enroll(userID,courseID,completed,completedTime,disabled,created) VALUES($userID,$courseID,$completed,null,$disabled,'$created')";
//             $result = mysqli_query($con->connection, $sql);

//             if ($result) {
//                 $response = ["status" => "ok", "code" => 200,  "message" => "update course enroll successfully.", "data" => $request];
//                 $json = json_encode($response, JSON_UNESCAPED_UNICODE);
//                 return $json;
//             } else {
//                 $response = ["status" => "error", "code" => 500,  "message" => "update course enroll failed.", "error" => mysqli_error($con->connection)];
//                 $json = json_encode($response, JSON_UNESCAPED_UNICODE);
//                 return $json;
//             }
//         } else {

//             ## already data
//             $response = ["status" => "ok", "code" => 200,  "message" => "you are already enroll this course.", "data" => $request];
//             $json = json_encode($response, JSON_UNESCAPED_UNICODE);
//             return $json;
//         }
//     } else {
//         $response = ["status" => "error", "code" => 500,  "message" => "check course enroll failed.", "error" => mysqli_error($con->connection),];
//         $json = json_encode($response, JSON_UNESCAPED_UNICODE);
//         return $json;
//     }
// });

// $app->post('/user/course/complete', function ($req, $res, $args) {

//     ## create database connection
//     $con = new Database();

//     $request = $req->getParsedBody();

//     $user = $request['user'];
//     $course = $request['course'];


//     $userID = $user['id'];
//     $courseID = $course['id'];

//     $created = date("Y-m-d H:i:s", time());

//     ## check enroll exist data
//     $select_user_complete_sql = "SELECT * FROM course_enroll WHERE userID = $userID AND courseID = $courseID AND disabled != 1";
//     $select_user_complete_result = mysqli_query($con->connection, $select_user_complete_sql);

//     if ($select_user_complete_result) {

//         if (mysqli_num_rows($select_user_complete_result) == 0) {
//             ## no data
//             ## insert
//             $completed = 1;
//             $disabled = 0;

//             ## insert course enroll sql
//             $sql = "INSERT INTO course_enroll(userID,courseID,completed,completedTime,disabled,created) VALUES($userID,$courseID,$completed,'$created',$disabled,'$created')";
//             $result = mysqli_query($con->connection, $sql);

//             if ($result) {
//                 $response = ["status" => "ok", "code" => 200,  "message" => "update course completed successfully.", "data" => $request];
//                 $json = json_encode($response, JSON_UNESCAPED_UNICODE);
//                 return $json;
//             } else {
//                 $response = ["status" => "error", "code" => 500,  "message" => "update course completed failed.", "error" => mysqli_error($con->connection)];
//                 $json = json_encode($response, JSON_UNESCAPED_UNICODE);
//                 return $json;
//             }
//         } else {

//             ## update sql
//             $sql = "UPDATE course_enroll SET completed = 1, completedTime = '$created'";
//             $result = mysqli_query($con->connection, $sql);

//             if ($result) {
//                 $response = ["status" => "ok", "code" => 200,  "message" => "update complete for this course.", "data" => $request];
//                 $json = json_encode($response, JSON_UNESCAPED_UNICODE);
//                 return $json;
//             } else {
//                 $response = ["status" => "error", "code" => 500,  "message" => "update course enroll failed.", "error" => mysqli_error($con->connection),];
//                 $json = json_encode($response, JSON_UNESCAPED_UNICODE);
//                 return $json;
//             }
//         }
//     } else {
//         $response = ["status" => "error", "code" => 500,  "message" => "check course enroll failed.", "error" => mysqli_error($con->connection),];
//         $json = json_encode($response, JSON_UNESCAPED_UNICODE);
//         return $json;
//     }
// });

// $app->post('/unity/user/course/complete', function ($req, $res, $args) {

//     ## create database connection
//     $con = new Database();

//     $request = $req->getParsedBody();

//     $user = json_decode($request['user'], true);
//     $course = json_decode($request['course'], true);


//     $userID = $user['id'];
//     $courseID = $course['id'];

//     $created = date("Y-m-d H:i:s", time());

//     ## check enroll exist data
//     $select_user_complete_sql = "SELECT * FROM course_enroll WHERE userID = $userID AND courseID = $courseID AND disabled != 1";
//     $select_user_complete_result = mysqli_query($con->connection, $select_user_complete_sql);

//     if ($select_user_complete_result) {

//         if (mysqli_num_rows($select_user_complete_result) == 0) {
//             ## no data
//             ## insert
//             $completed = 1;
//             $disabled = 0;

//             ## insert course enroll sql
//             $sql = "INSERT INTO course_enroll(userID,courseID,completed,completedTime,disabled,created) VALUES($userID,$courseID,$completed,'$created',$disabled,'$created')";
//             $result = mysqli_query($con->connection, $sql);

//             if ($result) {
//                 $response = ["status" => "ok", "code" => 200,  "message" => "update course completed successfully.", "data" => $request];
//                 $json = json_encode($response, JSON_UNESCAPED_UNICODE);
//                 return $json;
//             } else {
//                 $response = ["status" => "error", "code" => 500,  "message" => "update course completed failed.", "error" => mysqli_error($con->connection)];
//                 $json = json_encode($response, JSON_UNESCAPED_UNICODE);
//                 return $json;
//             }
//         } else {
//             ## already data
//             $response = ["status" => "ok", "code" => 200,  "message" => "you are already complete this course.", "data" => $request];
//             $json = json_encode($response, JSON_UNESCAPED_UNICODE);
//             return $json;
//         }
//     } else {
//         $response = ["status" => "error", "code" => 500,  "message" => "check course enroll failed.", "error" => mysqli_error($con->connection),];
//         $json = json_encode($response, JSON_UNESCAPED_UNICODE);
//         return $json;
//     }
// });
