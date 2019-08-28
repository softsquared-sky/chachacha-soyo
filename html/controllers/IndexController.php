<?php
require 'function.php';

const JWT_SECRET_KEY = "TEST_KEYTEST_KEYTEST_KEYTEST_KEYTEST_KEYTEST_KEYTEST_KEYTEST_KEYTEST_KEYTEST_KEYTEST_KEYTEST_KEYTEST_KEY";

$res = (Object)Array();
header('Content-Type: json');
$req = json_decode(file_get_contents("php://input"));
try {
    addAccessLogs($accessLogs, $req);
    switch ($handler) {
        case "index":
            echo "API Server";
            break;
        case "ACCESS_LOGS":
            //            header('content-type text/html charset=utf-8');
            header('Content-Type: text/html; charset=UTF-8');
            getLogs("./logs/access.log");
            break;
        case "ERROR_LOGS":
            //            header('content-type text/html charset=utf-8');
            header('Content-Type: text/html; charset=UTF-8');
            getLogs("./logs/errors.log");
            break;
        /*
         * API No. 0
         * API Name : 테스트 API
         * 마지막 수정 날짜 : 19.04.29
         */


        case "signupGuest":

            $usernum = 0;
            $signuptime = date("Y-m-d H:i:s");
            $userid = $req->userid;
            $userpw = $req->userpw;
            $userpw2 = $req->userpw2;
            $name = $req->name;
            $gender = $req->gender; //성별 0-> 여, 1 -> 남
            $age = $req->age; // 나이 0 -> 20대, 1-> 30대, 2-> 40대 , 3-> 50대
            $email = $req->email;
//            $witing = $req->witing;
//            echo "$name";

            $patterPw = '/^.*(?=^.{8,15}$)(?=.*\d)(?=.*[a-zA-Z])(?=.*[!@#$%^&+=]).*$/'; //영대/소문자, 숫자 및 특수문자 조합 비밀번호 8자리이상 15자리 이하
            $patterEmail = "/^[_\.0-9a-zA-Z-]+@([0-9a-zA-Z][0-9a-zA-Z-]+\.)+[a-zA-Z]{2,6}$/i"; // 이메일 형식
            $patterName = "/([^가-힣\x20])/"; //한글이름
//            $patterName = "/[xA1-xFE][xA1-xFE]/"; //한글 이름

            if (strlen($userid) > 0 and strlen($userpw) > 0 and strlen($userpw2) > 0 and strlen($gender) > 0 and strlen($name) > 0 and strlen($age) > 0 and strlen($email) > 0) {
                $result = idcheck_guest($userid); // 아이디 중복 체크
//              echo "result: $result";

                if ($result === 1) {
                    $res->isSuccess = false;
                    $res->code = 101;
                    $res->message = "기존에 있는 아이디 입니다. 다른아이디를 입력해주세요";
                    echo json_encode($res, JSON_NUMERIC_CHECK);
                } else if ($result === 0) {

                    if (!preg_match($patterName, $name)) {
//                        echo "이름이 알맞게 입력되었습니다";
                        if ($userpw == $userpw2) {
//                            echo "비밀번호가 같습니다";

                            if (preg_match($patterPw, $userpw)) {
//                            echo "비밀번호가 알맞게 입력되었습니다";

                                if (preg_match($patterPw, $userpw2)) {

                                    if (preg_match($patterEmail, $email)) {
//                                echo "이메일 알맞게 입력되었습니다";

                                        http_response_code(200);
                                        guest($usernum, $userid, $userpw, $name, $age, $gender, $email, $signuptime);
                                        $res->isSuccess = TRUE;
                                        $res->code = 100;
                                        $res->message = "회원가입을 성공적으로 완료했습니다";
                                        echo json_encode($res, JSON_NUMERIC_CHECK);
                                    } else {
                                        $res->isSuccess = false;
                                        $res->code = 102;
                                        $res->message = "잘못된 이메일 형식입니다";
                                        echo json_encode($res, JSON_NUMERIC_CHECK);
                                    }
                                }

                            } else {
                                $res->isSuccess = false;
                                $res->code = 103;
                                $res->message = "영대/소문자,숫자 및 특수문자 조합 8자리이상 15자리 이하로 비밀번호를 입력해주세요";
                                echo json_encode($res, JSON_NUMERIC_CHECK);
                            }
                        } else {
                            $res->isSuccess = false;
                            $res->code = 104;
                            $res->message = "비밀번호 두개가 일치하지 않습니다";
                            echo json_encode($res, JSON_NUMERIC_CHECK);
                        }
                    } else {
                        $res->isSuccess = false;
                        $res->code = 105;
                        $res->message = "이름을 한글로 제대로 입력해쉐요";
                        echo json_encode($res, JSON_NUMERIC_CHECK);
                    }

                }

            } else if (strlen($userid) < 1) {
                $res->isSuccess = false;
                $res->code = 106;
                $res->message = "아이디를 엽력해주세요";
                echo json_encode($res, JSON_NUMERIC_CHECK);
            } else if (strlen($userpw) < 1) {
                $res->isSuccess = false;
                $res->code = 107;
                $res->message = "비밀번호를 엽력해주세요";
                echo json_encode($res, JSON_NUMERIC_CHECK);
            } else if (strlen($userpw2) < 1) {
                $res->isSuccess = false;
                $res->code = 108;
                $res->message = "비밀번호 반복을 엽력해주세요";
                echo json_encode($res, JSON_NUMERIC_CHECK);
            } else if (strlen($gender) < 1) {
                $res->isSuccess = false;
                $res->code = 109;
                $res->message = "성별을 선택해주세요";
                echo json_encode($res, JSON_NUMERIC_CHECK);
            } else if (strlen($name) < 1) {
                $res->isSuccess = false;
                $res->code = 110;
                $res->message = "이름을 입력해주세요";
                echo json_encode($res, JSON_NUMERIC_CHECK);
            } else if (strlen($age) < 1) {
                $res->isSuccess = false;
                $res->code = 111;
                $res->message = "나이를 입력해주세요";
                echo json_encode($res, JSON_NUMERIC_CHECK);
            } else if (strlen($email) < 1) {
                $res->isSuccess = false;
                $res->code = 112;
                $res->message = "이메일을 엽력해주세요";
                echo json_encode($res, JSON_NUMERIC_CHECK);
            }


            break;

        case "signupBoss":

            $usernum = 0;
            $signuptime = date("Y-m-d H:i:s");
            $userid = $req->userid;
            $userpw = $req->userpw;
            $userpw2 = $req->userpw2;
            $name = $req->name;
            $phone = $req->phone;  //113

            $patterPw = '/^.*(?=^.{8,15}$)(?=.*\d)(?=.*[a-zA-Z])(?=.*[!@#$%^&+=]).*$/'; //영대/소문자, 숫자 및 특수문자 조합 비밀번호 8자리이상 15자리 이하
            $pattenPhone = "/^01[0-9]{8,9}$/"; // 핸드폰번호 형식
            $patterName = "/([^가-힣\x20])/"; //한글이름
//            echo "test";
//
            if (strlen($userid) > 0 and strlen($userpw) > 0 and strlen($userpw2) > 0 and strlen($name) > 0 and strlen($phone) > 0)
            {
//                echo "t11est";
                $result = idcheck_boss($userid);
                if ($result === 1)
                {
                    $res->isSuccess = false;
                    $res->code = 101;
                    $res->message = "기존에 있는 아이디 입니다. 다른아이디를 입력해주세요";
                    echo json_encode($res, JSON_NUMERIC_CHECK);
                }
                else if ($result === 0)
                {
                    if(!preg_match($patterName, $name))
                    {
//                        echo "이름이 알맞게 입력되었습니다";
                        if ($userpw == $userpw2)
                        {
//                            echo "비밀번호가 같습니다";

                            if (preg_match($patterPw, $userpw))
                            {
//                                  echo "비밀번호가 알맞게 입력되었습니다";
//
                                if (preg_match($patterPw, $userpw2))
                                {

                                    if (preg_match($pattenPhone, $phone))
                                    {
                                        http_response_code(200);
                                        boss($usernum, $userid, $userpw, $name, $phone, $signuptime);
                                        $res->isSuccess = TRUE;
                                        $res->code = 100;
                                        $res->message = "회원가입을 성공적으로 완료했습니다";
                                        echo json_encode($res, JSON_NUMERIC_CHECK);
                                    }
                                    else
                                    {
                                        $res->isSuccess = false;
                                        $res->code = 113;
                                        $res->message = "핸드폰 번호 형식에 맞춰 입력해주세요";
                                        echo json_encode($res, JSON_NUMERIC_CHECK);
                                    }
                                }
                            }
                            else
                            {
                                $res->isSuccess = false;
                                $res->code = 103;
                                $res->message = "영대/소문자,숫자 및 특수문자 조합 8자리이상 15자리 이하로 비밀번호를 입력해주세요";
                                echo json_encode($res, JSON_NUMERIC_CHECK);
                            }
                        }
                        else
                        {

                            $res->isSuccess = false;
                            $res->code = 104;
                            $res->message = "비밀번호 두개가 일치하지 않습니다";
                            echo json_encode($res, JSON_NUMERIC_CHECK);
                        }
//
                    }
                    else
                    {
                        $res->isSuccess = false;
                        $res->code = 105;
                        $res->message = "이름을 한글로 제대로 입력해쉐요";
                        echo json_encode($res, JSON_NUMERIC_CHECK);
                    }
                }

            }
            else if (strlen($userid) < 1)
            {
                $res->isSuccess = false;
                $res->code = 106;
                $res->message = "아이디를 엽력해주세요";
                echo json_encode($res, JSON_NUMERIC_CHECK);
            }
            else if (strlen($userpw) < 1)
            {
                $res->isSuccess = false;
                $res->code = 107;
                $res->message = "비밀번호를 엽력해주세요";
                echo json_encode($res, JSON_NUMERIC_CHECK);
            }
            else if (strlen($userpw2) < 1)
            {
                $res->isSuccess = false;
                $res->code = 108;
                $res->message = "비밀번호 반복을 엽력해주세요";
                echo json_encode($res, JSON_NUMERIC_CHECK);
            }
            else if (strlen($name) < 1)
            {

                $res->isSuccess = false;
                $res->code = 110;
                $res->message = "이름을 입력해주세요";
                echo json_encode($res, JSON_NUMERIC_CHECK);
            }
            else if (strlen($phone) < 1)
            {
                $res->isSuccess = false;
                $res->code = 114;
                $res->message = "핸드폰 번호를 입력해주세요";
                echo json_encode($res, JSON_NUMERIC_CHECK);
            }
//
            break;

        case 'loginUser': // 로그인 API
            $userid = $req->userid;
            $userpw = $req->userpw;
            //                echo "$userid";
//                echo "$userpw";
            if (strlen($userid ) >  0 and strlen($userpw) > 0)
            {
                $reuslt = login($userid, $userpw);
                if($reuslt === 1)
                {
                    $jwt = getJWToken($userid, $userpw, JWT_SECRET_KEY);
                    $res->result->jwt = $jwt;  // 토큰 발행 api
                    $res->isSuccess = TRUE;
                    $res->code = 115;
                    $res->message = "로그인을 성공적으로 완료했습니다";
                    echo json_encode($res, JSON_NUMERIC_CHECK);
                }
                else if($reuslt === 0)
                {
                    $res->isSuccess = false;
                    $res->code = 116;
                    $res->message = "로그인이 실패하였습니다 아이디와 비밀번호를 알맞게 입력해주세요";
                    echo json_encode($res, JSON_NUMERIC_CHECK);
                }
            }
            else if (strlen($userid) <1)
            {
                $res->isSuccess = false;
                $res->code = 106;
                $res->message = "아이디를 엽력해주세요";
                echo json_encode($res, JSON_NUMERIC_CHECK);
            }
            else if (strlen($userpw) < 1)
            {
                $res->isSuccess = false;
                $res->code = 107;
                $res->message = "비밀번호를 엽력해주세요";
                echo json_encode($res, JSON_NUMERIC_CHECK);
            }


            break;

        case "test":
            http_response_code(200);
            $res->result = test();
            $res->isSuccess = TRUE;
            $res->code = 100;
            $res->message = "테스트 성공";
            echo json_encode($res, JSON_NUMERIC_CHECK);
            break;
        /*
         * API No. 0
         * API Name : 테스트 Path Variable API
         * 마지막 수정 날짜 : 19.04.29
         */
        case "testDetail":
            http_response_code(200);
            $res->result = testDetail($vars["testNo"]);
            $res->isSuccess = TRUE;
            $res->code = 100;
            $res->message = "테스트 성공";
            echo json_encode($res, JSON_NUMERIC_CHECK);
            break;
        /*
         * API No. 0
         * API Name : 테스트 Body & Insert API
         * 마지막 수정 날짜 : 19.04.29
         */
        case "testPost":
            http_response_code(200);
            $res->result = testPost($req->name);
            $res->isSuccess = TRUE;
            $res->code = 100;
            $res->message = "테스트 성공";
            echo json_encode($res, JSON_NUMERIC_CHECK);
            break;
    }
} catch (\Exception $e) {
    return getSQLErrorException($errorLogs, $e, $req);
}
