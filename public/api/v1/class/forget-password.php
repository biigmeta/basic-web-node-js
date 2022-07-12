<?php

$app->get("/forgetpassword/checklog/{code}", function ($req, $res, $args) {
    ## create database connection
    $con = new Database();

    $code = $args["code"];

    ## check code in database
    $sql = "SELECT * FROM `forget_password_log` WHERE `code` = '$code'";
    $result = mysqli_query($con->connection, $sql);

    if ($result) {
        if (mysqli_num_rows($result) > 0) {
            $row = mysqli_fetch_assoc($result);

            $expire_time = strtotime($row["expired"]);
            $now = strtotime('now');

            ## check expire time
            if ($now <= $expire_time) {
                $response = ["status" => "ok", "code" => 200,  "message" => "get forget password log successfully.", "data" => $row];
                $json = json_encode($response, JSON_UNESCAPED_UNICODE);
                return $json;
            } else {
                ## code expired
                $response = ["status" => "error", "code" => 404,  "message" => "code expired.", "data" => null];
                $json = json_encode($response, JSON_UNESCAPED_UNICODE);
                return $json;
            }
        } else {
            ## data not found
            $response = ["status" => "error", "code" => 404,  "message" => "data not found.", "data" => null];
            $json = json_encode($response, JSON_UNESCAPED_UNICODE);
            return $json;
        }
    } else {
        ## mysqli error
        $response = ["status" => "error", "code" => 500,  "message" => "can not check log data by code.", "error" => mysqli_error($con->connection)];
        $json = json_encode($response, JSON_UNESCAPED_UNICODE);
        return $json;
    }
});

$app->post("/forgetpassword/reset", function ($req, $res, $args) {
    ## create database connection
    $con = new Database();

    $request = $req->getParsedBody();
    $email = $request["email"];
    $password = $request["password"];

    ## update password in database in hash format
    $sql = "UPDATE `user` SET `password` = '" . password_hash($password, PASSWORD_DEFAULT) . "' WHERE `email` = '$email'";
    $result = mysqli_query($con->connection, $sql);

    if ($result) {
        ## update password successfully
        $response = ["status" => "ok", "code" => 200,  "message" => "update password successfully.", "data" => null];
        $json = json_encode($response, JSON_UNESCAPED_UNICODE);
        return $json;
    } else {
        ## mysqli error
        $response = ["status" => "error", "code" => 500,  "message" => "can not update password.", "error" => mysqli_error($con->connection)];
        $json = json_encode($response, JSON_UNESCAPED_UNICODE);
        return $json;
    }

    
});

$app->post("/forgetpassword/request", function ($req, $res, $args) {

    ## create database connection
    $con = new Database();

    $request = $req->getParsedBody();
    $email = $request["email"];

    ## check email exist
    $sql = "SELECT * FROM user WHERE email = '$email'";
    $result = mysqli_query($con->connection, $sql);

    ## check query result
    if ($result) {
        if (mysqli_num_rows($result) > 0) {
            $user_row = mysqli_fetch_assoc($result);
        } else {
            $response = ["status" => "error", "code" => 404,  "message" => "user data not found.", "data" => null];
            $json = json_encode($response, JSON_UNESCAPED_UNICODE);
            return $json;
        }
    } else {
        ## mysqli error
        $response = ["status" => "error", "code" => 500,  "message" => "can not check user data by email.", "error" => mysqli_error($con->connection)];
        $json = json_encode($response, JSON_UNESCAPED_UNICODE);
        return $json;
    }

    ## check email in forget password log table
    $userID = $user_row["id"];
    $sql = "SELECT * FROM forget_password_log WHERE email = '$email'";

    $result = mysqli_query($con->connection, $sql);

    ## check query result
    if ($result) {
        if (mysqli_num_rows($result) > 0) {

            $forget_log_row = mysqli_fetch_assoc($result);

            ## check expire time
            $expire_time = strtotime($forget_log_row["expired"]);
            $now = strtotime('now');

            if ($now > $expire_time) {
                ## expired time
                ## generate new code and insert new log
                $code = generateRandomString(25);
                $addlog = true;
            } else {
                ## not expired time
                ## get code
                $code = $forget_log_row["code"];
                $addlog = false;
            }
        } else {
            ## generate new code and insert new log
            $code = generateRandomString(25);
            $addlog = true;
        }
    } else {
        ## mysqli error
        $response = ["status" => "error", "code" => 500,  "message" => "can not check forget password log data by email.", "error" => mysqli_error($con->connection)];
        $json = json_encode($response, JSON_UNESCAPED_UNICODE);
        return $json;
    }

    if ($addlog == true) {
        ## expired time = now + 15 min
        $expired = date("Y-m-d H:i:s", strtotime('+15 minute'));

        $sql = "INSERT INTO forget_password_log (email, code, expired) VALUES ('$email', '$code', '$expired')";
        $result = mysqli_query($con->connection, $sql);

        ## check query result
        if ($result) {
        } else {
            ## mysqli error
            $response = ["status" => "error", "code" => 500,  "message" => "can not insert new code to database.", "error" => mysqli_error($con->connection)];
            $json = json_encode($response, JSON_UNESCAPED_UNICODE);
            return $json;
        }
    }

    ## send reset password email
    $htmlBody = resetPasswordHTMLForm($code);
    $send = sendEmail($email, "Aerospace: Reset Password:", $htmlBody);

    if ($send == true) {
        $response = ["status" => "ok", "code" => 200,  "message" => "send email successfully.", "data" => $email, "body" => $htmlBody];
        $json = json_encode($response, JSON_UNESCAPED_UNICODE);
        return $json;
    } else {
        $response = ["status" => "error", "code" => 500,  "message" => "can not send email.", "error" => $send];
        $json = json_encode($response, JSON_UNESCAPED_UNICODE);
        return $json;
    }
});

function resetPasswordHTMLForm($code)
{
    $html = "<!DOCTYPE html>\n\n<html lang=\"en\" xmlns:o=\"urn:schemas-microsoft-com:office:office\" xmlns:v=\"urn:schemas-microsoft-com:vml\">\n<head>\n<title></title>\n<meta content=\"text/html; charset=utf-8\" http-equiv=\"Content-Type\"/>\n<meta content=\"width=device-width, initial-scale=1.0\" name=\"viewport\"/>\n<!--[if mso]><xml><o:OfficeDocumentSettings><o:PixelsPerInch>96</o:PixelsPerInch><o:AllowPNG/></o:OfficeDocumentSettings></xml><![endif]-->\n<style>\n\t\t* {\n\t\t\tbox-sizing: border-box;\n\t\t}\n\n\t\tbody {\n\t\t\tmargin: 0;\n\t\t\tpadding: 0;\n\t\t}\n\n\t\ta[x-apple-data-detectors] {\n\t\t\tcolor: inherit !important;\n\t\t\ttext-decoration: inherit !important;\n\t\t}\n\n\t\t#MessageViewBody a {\n\t\t\tcolor: inherit;\n\t\t\ttext-decoration: none;\n\t\t}\n\n\t\tp {\n\t\t\tline-height: inherit\n\t\t}\n\n\t\t.desktop_hide,\n\t\t.desktop_hide table {\n\t\t\tmso-hide: all;\n\t\t\tdisplay: none;\n\t\t\tmax-height: 0px;\n\t\t\toverflow: hidden;\n\t\t}\n\n\t\t@media (max-width:520px) {\n\t\t\t.desktop_hide table.icons-inner {\n\t\t\t\tdisplay: inline-block !important;\n\t\t\t}\n\n\t\t\t.icons-inner {\n\t\t\t\ttext-align: center;\n\t\t\t}\n\n\t\t\t.icons-inner td {\n\t\t\t\tmargin: 0 auto;\n\t\t\t}\n\n\t\t\t.image_block img.big,\n\t\t\t.row-content {\n\t\t\t\twidth: 100% !important;\n\t\t\t}\n\n\t\t\t.mobile_hide {\n\t\t\t\tdisplay: none;\n\t\t\t}\n\n\t\t\t.stack .column {\n\t\t\t\twidth: 100%;\n\t\t\t\tdisplay: block;\n\t\t\t}\n\n\t\t\t.mobile_hide {\n\t\t\t\tmin-height: 0;\n\t\t\t\tmax-height: 0;\n\t\t\t\tmax-width: 0;\n\t\t\t\toverflow: hidden;\n\t\t\t\tfont-size: 0px;\n\t\t\t}\n\n\t\t\t.desktop_hide,\n\t\t\t.desktop_hide table {\n\t\t\t\tdisplay: table !important;\n\t\t\t\tmax-height: none !important;\n\t\t\t}\n\t\t}\n\t</style>\n</head>\n<body style=\"background-color: #FFFFFF; margin: 0; padding: 0; -webkit-text-size-adjust: none; text-size-adjust: none;\">\n<table border=\"0\" cellpadding=\"0\" cellspacing=\"0\" class=\"nl-container\" role=\"presentation\" style=\"mso-table-lspace: 0pt; mso-table-rspace: 0pt; background-color: #FFFFFF;\" width=\"100%\">\n<tbody>\n<tr>\n<td>\n<table align=\"center\" border=\"0\" cellpadding=\"0\" cellspacing=\"0\" class=\"row row-1\" role=\"presentation\" style=\"mso-table-lspace: 0pt; mso-table-rspace: 0pt;\" width=\"100%\">\n<tbody>\n<tr>\n<td>\n<table align=\"center\" border=\"0\" cellpadding=\"0\" cellspacing=\"0\" class=\"row-content stack\" role=\"presentation\" style=\"mso-table-lspace: 0pt; mso-table-rspace: 0pt; color: #000000; width: 500px;\" width=\"500\">\n<tbody>\n<tr>\n<td class=\"column column-1\" style=\"mso-table-lspace: 0pt; mso-table-rspace: 0pt; font-weight: 400; text-align: left; vertical-align: top; padding-top: 5px; padding-bottom: 5px; border-top: 0px; border-right: 0px; border-bottom: 0px; border-left: 0px;\" width=\"100%\">\n<table border=\"0\" cellpadding=\"0\" cellspacing=\"0\" class=\"image_block\" role=\"presentation\" style=\"mso-table-lspace: 0pt; mso-table-rspace: 0pt;\" width=\"100%\">\n<tr>\n<td style=\"width:100%;padding-right:0px;padding-left:0px;\">\n<div align=\"center\" style=\"line-height:10px\"><img alt=\"heading\" class=\"big\" src=\"https://www.gforcesolution.com/app/aerospacehololens/images/heading.png\" style=\"display: block; height: auto; border: 0; width: 500px; max-width: 100%;\" title=\"heading\" width=\"500\"/></div>\n</td>\n</tr>\n</table>\n</td>\n</tr>\n</tbody>\n</table>\n</td>\n</tr>\n</tbody>\n</table>\n<table align=\"center\" border=\"0\" cellpadding=\"0\" cellspacing=\"0\" class=\"row row-2\" role=\"presentation\" style=\"mso-table-lspace: 0pt; mso-table-rspace: 0pt;\" width=\"100%\">\n<tbody>\n<tr>\n<td>\n<table align=\"center\" border=\"0\" cellpadding=\"0\" cellspacing=\"0\" class=\"row-content stack\" role=\"presentation\" style=\"mso-table-lspace: 0pt; mso-table-rspace: 0pt; color: #000000; width: 500px;\" width=\"500\">\n<tbody>\n<tr>\n<td class=\"column column-1\" style=\"mso-table-lspace: 0pt; mso-table-rspace: 0pt; font-weight: 400; text-align: left; vertical-align: top; padding-left: 15px; padding-right: 15px; padding-top: 5px; padding-bottom: 5px; border-top: 0px; border-right: 0px; border-bottom: 0px; border-left: 0px;\" width=\"100%\">\n<table border=\"0\" cellpadding=\"0\" cellspacing=\"0\" class=\"image_block\" role=\"presentation\" style=\"mso-table-lspace: 0pt; mso-table-rspace: 0pt;\" width=\"100%\">\n<tr>\n<td style=\"width:100%;padding-right:0px;padding-left:0px;\">\n<div align=\"center\" style=\"line-height:10px\"><img alt=\"gisda_nxpo_logo\" class=\"big\" src=\"https://www.gforcesolution.com/app/aerospacehololens/images/gistda-nxpo-combine.png\" style=\"display: block; height: auto; border: 0; width: 470px; max-width: 100%;\" title=\"gisda_nxpo_logo\" width=\"470\"/></div>\n</td>\n</tr>\n</table>\n</td>\n</tr>\n</tbody>\n</table>\n</td>\n</tr>\n</tbody>\n</table>\n<table align=\"center\" border=\"0\" cellpadding=\"0\" cellspacing=\"0\" class=\"row row-3\" role=\"presentation\" style=\"mso-table-lspace: 0pt; mso-table-rspace: 0pt;\" width=\"100%\">\n<tbody>\n<tr>\n<td>\n<table align=\"center\" border=\"0\" cellpadding=\"0\" cellspacing=\"0\" class=\"row-content stack\" role=\"presentation\" style=\"mso-table-lspace: 0pt; mso-table-rspace: 0pt; color: #000000; width: 500px;\" width=\"500\">\n<tbody>\n<tr>\n<td class=\"column column-1\" style=\"mso-table-lspace: 0pt; mso-table-rspace: 0pt; font-weight: 400; text-align: left; vertical-align: top; padding-top: 5px; padding-bottom: 5px; border-top: 0px; border-right: 0px; border-bottom: 0px; border-left: 0px;\" width=\"100%\">\n<table border=\"0\" cellpadding=\"10\" cellspacing=\"0\" class=\"paragraph_block\" role=\"presentation\" style=\"mso-table-lspace: 0pt; mso-table-rspace: 0pt; word-break: break-word;\" width=\"100%\">\n<tr>\n<td>\n<div style=\"color:#000000;font-size:16px;font-family:Arial, Helvetica Neue, Helvetica, sans-serif;font-weight:400;line-height:120%;text-align:center;direction:ltr;letter-spacing:0px;mso-line-height-alt:19.2px;\">\n<p style=\"margin: 0; margin-bottom: 16px;\">คุณสามารถเปลี่ยนรหัสผ่านได้จากลิ้งค์ด้านล่าง</p>\n<p style=\"margin: 0;\"><a href=\"https://www.gforcesolution.com/app/aerospacehololens/reset-password.php?code=$code\" rel=\"noopener\" style=\"text-decoration: underline; color: #0068a5;\" target=\"_blank\">Reset Password</a></p>\n</div>\n</td>\n</tr>\n</table>\n</td>\n</tr>\n</tbody>\n</table>\n</td>\n</tr>\n</tbody>\n</table>\n<table align=\"center\" border=\"0\" cellpadding=\"0\" cellspacing=\"0\" class=\"row row-4\" role=\"presentation\" style=\"mso-table-lspace: 0pt; mso-table-rspace: 0pt;\" width=\"100%\">\n<tbody>\n<tr>\n<td>\n<table align=\"center\" border=\"0\" cellpadding=\"0\" cellspacing=\"0\" class=\"row-content stack\" role=\"presentation\" style=\"mso-table-lspace: 0pt; mso-table-rspace: 0pt; color: #000000; width: 500px;\" width=\"500\">\n<tbody>\n<tr>\n<td class=\"column column-1\" style=\"mso-table-lspace: 0pt; mso-table-rspace: 0pt; font-weight: 400; text-align: left; vertical-align: top; padding-top: 5px; padding-bottom: 5px; border-top: 0px; border-right: 0px; border-bottom: 0px; border-left: 0px;\" width=\"100%\">\n<table border=\"0\" cellpadding=\"10\" cellspacing=\"0\" class=\"paragraph_block\" role=\"presentation\" style=\"mso-table-lspace: 0pt; mso-table-rspace: 0pt; word-break: break-word;\" width=\"100%\">\n<tr>\n<td>\n<div style=\"color:#000000;font-size:14px;font-family:Arial, Helvetica Neue, Helvetica, sans-serif;font-weight:400;line-height:120%;text-align:center;direction:ltr;letter-spacing:0px;mso-line-height-alt:16.8px;\">\n<p style=\"margin: 0; margin-bottom: 16px;\">หมายเหตุ: ลิ้งค์ที่แนบมาจะหมดอายุภายใน 15 นาที</p>\n<p style=\"margin: 0;\">กรุณาอย่าตอบกลับอีเมลฉบับนี้</p>\n</div>\n</td>\n</tr>\n</table>\n</td>\n</tr>\n</tbody>\n</table>\n</td>\n</tr>\n</tbody>\n</table>\n</td>\n</tr>\n</tbody>\n</table><!-- End -->\n</body>\n</html>";


    return $html;
}
