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
            $name = $req->name;
            $gender = $req->gender; //성별 0-> 여, 1 -> 남
//        echo "$gender";
            $age = $req->age; // 나이 0 -> 20대, 1-> 30대, 2-> 40대 , 3-> 50대
            $email = $req->email;
            $phone = $req->phone;
//            $witing = $req->witing;
//            echo "$name";

            $patternId = "/^[a-z0-9_]{4,10}$/"; // 4자 이상 10자 이하 영소문자/숫자/_ 허용
            $patternPhone = "/^01[0-9]{8,9}$/"; // 핸드폰번호 형식
            $patternPw = '/^.*(?=^.{8,15}$)(?=.*\d)(?=.*[a-zA-Z])(?=.*[!@#$%^&+=]).*$/'; //영대/소문자, 숫자 및 특수문자 조합 비밀번호 8자리이상 15자리 이하
            $patternEmail = "/^[_\.0-9a-zA-Z-]+@([0-9a-zA-Z][0-9a-zA-Z-]+\.)+[a-zA-Z]{2,6}$/i"; // 이메일 형식
            $patternName = "/([^가-힣\x20])/"; //한글이름 오타  정규 표현식 오류
//            $patternName = "/[xA1-xFE][xA1-xFE]/"; //한글 이름

            if (strlen($userid) > 0 and strlen($userpw) > 0 and strlen($gender) > 0 and strlen($name) > 0 and strlen($age) > 0 and strlen($email) > 0 and strlen($phone) > 0)
            {
                $isresult = idcheckGuest($userid); // 아이디 중복 체크
                $isresult2 = idcheckBoss($userid); //레몬꺼 보고 오기 이메일 중복체크 만들기
                $isalreadyEmail  =  emailcheckGuest($email); // 이메일 중복 검사

                if ($isresult === 1 or $isresult2 === 1)
                {
                    $res->isSuccess = false;
                    $res->code = 101;
                    $res->message = "기존에 있는 아이디 입니다. 다른 아이디를 입력해주세요";
                    echo json_encode($res, JSON_NUMERIC_CHECK);
                    return;
                }
                else if ($isresult === 0 and $isresult2 === 0)
                {
//                    echo "아이디가 중복되지 않습니다";
                }

                if ($isalreadyEmail === 1)
                {
                    $res->isSuccess = false;
                    $res->code = 102;
                    $res->message = "기존에 있는 이메일 입니다. 다른 이메일을 입력해주세요";
                    echo json_encode($res, JSON_NUMERIC_CHECK);
                    return;
                }
                else if ($isalreadyEmail === 0)
                {
//                    echo "이메일이 중복되지 않습니다";
                }

                if (preg_match($patternId, $userid))
                {
                    echo "아이디가 알맞게 입력되었습니다";
                }
                else {

                    $res->isSuccess = false;
                    $res->code = 110;
                    $res->message = "영/소문자,숫자 조합 4자리 이상 10자리 이하로 아이디를 입력하세요";
                    echo json_encode($res, JSON_NUMERIC_CHECK);
                    return;
                }

                if (!preg_match($patternName, $name))
                {
//                      echo "이름이 알맞게 입력되었습니다";
                }
                else {

                    $res->isSuccess = false;
                    $res->code = 103;
                    $res->message = "이름을 한글로 제대로 입력하세요";
                    echo json_encode($res, JSON_NUMERIC_CHECK);
                    return;
                }

                if (preg_match($patternPw, $userpw))
                {
//                       echo "비밀번호가 알맞게 입력되었습니다";
                }
                else
                    {
                    $res->isSuccess = false;
                    $res->code = 104;
                    $res->message = "영대/소문자,숫자 및 특수문자 조합 8자리이상 15자리 이하로 비밀번호를 입력해주세요";
                    echo json_encode($res, JSON_NUMERIC_CHECK);
                    return;
                }

                if (preg_match($patternEmail, $email))
                {
//                       echo "이메일 알맞게 입력되었습니다";
                }
                else {
                    $res->isSuccess = false;
                    $res->code = 105;
                    $res->message = "잘못된 이메일 형식입니다";
                    echo json_encode($res, JSON_NUMERIC_CHECK);
                    return;
                }

                if (preg_match($patternPhone, $phone))
                {
//                      echo "핸드폰 형식에 알맞게 입력되었습니다";
                    $phone = addHyphen($phone);
                }
                else{
                    $res->isSuccess = false;
                    $res->code = 106;
                    $res->message = "번호 형식에 맞춰 입력해주세요";
                    echo json_encode($res, JSON_NUMERIC_CHECK);
                    return;
                }

               if ($age < 0 )
                {
//                    echo "$age";
                   $res->isSuccess = false;
                   $res->code = 107;
                   $res->message = "올바른 나이 형식에 맞춰 입력해주세요";
                   echo json_encode($res, JSON_NUMERIC_CHECK);
                   return;
                }
                else  if ($age <= 3)
                {
//                    echo "나이 형식에 알맞게 입력되었습니다";
                }
                else
                {
//                    echo "$age";
                    $res->isSuccess = false;
                    $res->code = 107;
                    $res->message = "올바른 나이 형식에 맞춰 입력해주세요";
                    echo json_encode($res, JSON_NUMERIC_CHECK);
                    return;
                }

                if ($gender < 0)
                {
//                    echo "$gender";
                    $res->isSuccess = false;
                    $res->code = 108;
                    $res->message = "성별 형식에 맞춰 입력해주세요";
                    echo json_encode($res, JSON_NUMERIC_CHECK);
                    return;
                }
                else if ($gender <= 1)
                {
//                      echo "전화번호가 알맞게 입력되었습니다";

//                    echo "$phone";
                    http_response_code(200);
                    guest($usernum, $userid, $userpw, $name, $age, $gender, $email, $phone, $signuptime);
                    $res->isSuccess = TRUE;
                    $res->code = 100;
                    $res->message = "회원가입을 성공적으로 완료했습니다";
                    echo json_encode($res, JSON_NUMERIC_CHECK);
                }
                else {
//                    echo "$gender";
                    $res->isSuccess = false;
                    $res->code = 108;
                    $res->message = "성별 형식에 맞춰 입력해주세요";
                    echo json_encode($res, JSON_NUMERIC_CHECK);
                    return;
                }
            }
            else if (strlen($userid) < 1 or strlen($userpw) < 1 or strlen($name) < 1 or strlen($gender) < 1 or strlen($age) < 1 or strlen($email) <1 or strlen($phone) <1 )
            {
                $res->isSuccess = false;
                $res->code = 109;
                $res->message = "모든 항목을 완전히 입력해주세요";
                echo json_encode($res, JSON_NUMERIC_CHECK);
                return;
            }


            break;

        case "signupBoss":

            $usernum = 0;
            $signuptime = date("Y-m-d H:i:s");

            $userid = $req->userid;
            $userpw = $req->userpw;
            $name = $req->name;
            $phone = $req->phone;  //113

            $patternId = "/^[a-z0-9_]{4,10}$/";
            $patternPhone = "/^01[0-9]{8,9}$/"; // 핸드폰번호 형식
            $patternPw = '/^.*(?=^.{8,15}$)(?=.*\d)(?=.*[a-zA-Z])(?=.*[!@#$%^&+=]).*$/'; //영대/소문자, 숫자 및 특수문자 조합 비밀번호 8자리이상 15자리 이하
            $patternName = "/([^가-힣\x20])/"; //한글이름

            if (strlen($userid) > 0 and strlen($userpw) > 0 and strlen($name)  > 0 and strlen($phone) > 0)
            {
                $isresult = idcheckGuest($userid); // 아이디 중복 체크
                $isresult2 = idcheckBoss($userid); //레몬꺼 보고 오기 이메일 중복체크 만들기

                if($isresult === 1 or $isresult2 === 1)
                {
                    $res->isSuccess = false;
                    $res->code = 101;
                    $res->message = "기존에 있는 아이디 입니다. 다른 아이디를 입력해주세요";
                    echo json_encode($res, JSON_NUMERIC_CHECK);
                    return;
                }
                else if ($isresult === 0 and $isresult2 === 0)
                {
//                    echo "아이디가 중복되지 않습니다";
                }

                if (preg_match($patternId, $userid))
                {
//                    echo "아이디가 알맞게 입력되었습니다";
                }
                else {

                    $res->isSuccess = false;
                    $res->code = 110;
                    $res->message = "영/소문자,숫자 조합 4자리 이상 10자리 이하로 아이디를 입력하세요";
                    echo json_encode($res, JSON_NUMERIC_CHECK);
                    return;
                }

                if (!preg_match($patternName, $name))
                {
//                      echo "이름이 알맞게 입력되었습니다";
                }
                else {

                    $res->isSuccess = false;
                    $res->code = 103;
                    $res->message = "이름을 한글로 제대로 입력하세요";
                    echo json_encode($res, JSON_NUMERIC_CHECK);
                    return;
                }

                if (preg_match($patternPw, $userpw))
                {
//                      echo "비밀번호가 알맞게 입력되었습니다";

                }
                else
                {
                    $res->isSuccess = false;
                    $res->code = 104;
                    $res->message = "영대/소문자,숫자 및 특수문자 조합 8자리이상 15자리 이하로 비밀번호를 입력해주세요";
                    echo json_encode($res, JSON_NUMERIC_CHECK);
                    return;
                }

                if (preg_match($patternPhone, $phone))
                {
//                      echo "핸드폰 형식에 알맞게 입력되었습니다";
                    $phone = addHyphen($phone);
//                    echo "$phone";
                    http_response_code(200);
                    boss($usernum, $userid, $userpw, $name, $phone, $signuptime);
                    $res->isSuccess = TRUE;
                    $res->code = 100;
                    $res->message = "회원가입을 성공적으로 완료했습니다";
                    echo json_encode($res, JSON_NUMERIC_CHECK);
                }
                else{
                    $res->isSuccess = false;
                    $res->code = 106;
                    $res->message = "번호 형식에 맞춰 입력해주세요";
                    echo json_encode($res, JSON_NUMERIC_CHECK);
                    return;
                }
            }
            else
            {
                $res->isSuccess = false;
                $res->code = 109;
                $res->message = "모든 항목을 완전히 입력해주세요";
                echo json_encode($res, JSON_NUMERIC_CHECK);
                return;
            }

            break;

        case 'loginUser': // 로그인 API

//            echo json_encode($_GET);

            $userid = $req->userid;
            $userpw = $req->userpw;

            if (strlen($userid) >  0 and strlen($userpw) > 0)
            {
                $isreuslt = login($userid, $userpw);
                if($isreuslt === 1)
                {
                    $jwt = getJWToken($userid, $userpw, JWT_SECRET_KEY);
                    $res->result->jwt = $jwt;  // 토큰 발행 api
                    $res->isSuccess = TRUE;
                    $res->code = 113;
                    $res->message = "로그인을 성공적으로 완료했습니다";
                    echo json_encode($res, JSON_NUMERIC_CHECK);
                }
                else if($isreuslt === 0)
                {
                    $res->isSuccess = false;
                    $res->code = 114;
                    $res->message = "로그인이 실패하였습니다 아이디와 비밀번호를 알맞게 입력해주세요";
                    echo json_encode($res, JSON_NUMERIC_CHECK);
                    return;
                }
            }
            else if (strlen($userid) <1)
            {
                $res->isSuccess = false;
                $res->code = 111;
                $res->message = "아이디를 엽력해주세요";
                echo json_encode($res, JSON_NUMERIC_CHECK);
                return;
            }
            else if (strlen($userpw) < 1)
            {
                $res->isSuccess = false;
                $res->code = 112;
                $res->message = "비밀번호를 엽력해주세요";
                echo json_encode($res, JSON_NUMERIC_CHECK);
                return;
            }


            break;

        case "test":

            echo json_encode($_GET);

            http_response_code(200);
//            $get = $_GET["userid"];
//            echo json_encode($get);
//            echo "$get";
//
//             if (is_int($get) == 1)
//             {
//                 echo "정수";
//
//             }
//             else if(is_int($get) == 0)
//             {
//                 echo "정수아님";
//             }
//            echo "$result";
//            echo "hi! " . $get;
//            echo json_encode($_GET);
//            $res->result = test();
//            $res->isSuccess = TRUE;
//            $res->code = 100;
//            $res->message = "테스트 성공";
//            echo json_encode($res, JSON_NUMERIC_CHECK);
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
