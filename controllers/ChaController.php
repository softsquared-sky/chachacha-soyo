<?php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require "PHPMailer-master/src/PHPMailer.php";
require "PHPMailer-master/src/SMTP.php";
require "PHPMailer-master/src/Exception.php";
require 'function.php';

const JWT_SECRET_KEY = "TEST_KEYTEST_KEYTEST_KEYTEST_KEYTEST_KEYTEST_KEYTEST_KEYTEST_KEYTEST_KEYTEST_KEYTEST_KEYTEST_KEYTEST_KEY";


$res = (Object)Array();
$mail = new PHPMailer(true);
header('Content-Type: json');
$req = json_decode(file_get_contents("php://input"));
try {
    addAccessLogs($accessLogs, $req);
    switch ($handler) {
        /*
         * API No. 0
         * API Name : JWT 유효성 검사 테스트 API
         * 마지막 수정 날짜 : 19.04.25
         */
        case "myCha":
//            echo "ha";

            $jwt = $_SERVER["HTTP_X_ACCESS_TOKEN"];
//            echo "$jwt";
            // jwt 유효성 검사
            $patternNum = "/^[0-9]+$/";
            $patternHun = "/([^가-힣\x20#])/"; // 한글 띄어쓰기 특수문자
            $result = isValidHeader($jwt, JWT_SECRET_KEY);
            $isintval = $result['intval'];
            $userid = $result['userid'];
            $storenum = $vars["storeNum"];
            $chatime =  date("Y-m-d H:i:s");

            $chanum = 0;
            $storenum = $req->storenum;
            $delete = 0;


            if ($isintval === 0) //토큰 검증 여부
            {
                $res->isSuccess = FALSE;
                $res->code = 201;
                $res->message = "유효하지 않은 토큰입니다";
                echo json_encode($res, JSON_NUMERIC_CHECK);
//                addErrorLogs($errorLogs, $res, $req); //에러로그 오류
                return;
            }
            else if($isintval === 1)
            {
                if(strlen($storenum) > 0)
                {
                    $testId = $vars["userId"];

                    if ($testId == $userid)
                    {
                        $isIdexist= isIdexist($testId);

                        if($isIdexist == 0)
                        {
                            $res->isSuccess = FALSE;
                            $res->code = 399;
                            $res->message = "유효하지 않은 아이디입니다";
                            echo json_encode($res, JSON_NUMERIC_CHECK);
                            return;
                        }
                    }
                    else
                    {
                        $res->isSuccess = FALSE;
                        $res->code = 399;
                        $res->message = "유효하지 않은 아이디입니다";
                        echo json_encode($res, JSON_NUMERIC_CHECK);
                        return;
                    }

                    $usernum = convert_to_num($testId);

//                echo "$storenum";
                    if (!preg_match($patternNum, $storenum))
                    {

                        $res->isSuccess = false;
                        $res->code = 216;
                        $res->message = "숫자 형식에 맞게 가게 번호를 입력해주세요";
                        echo json_encode($res, JSON_NUMERIC_CHECK);
                        return;
                    }

                    $isNotexiststore = isStoreexist($storenum);

                    if($isNotexiststore == 0)
                    {
                        $res->isSuccess = false;
                        $res->code = 499;
                        $res->message = "유효한 가게번호가 아닙니다";
                        echo json_encode($res, JSON_NUMERIC_CHECK);
                        return;
                    }

                    $isNotdelete = chaCheck($storenum, $usernum);

                    if($isNotdelete == 0)
                    {
                        mychachacha($chanum, $storenum, $usernum, $delete, $chatime);
                        $res->isSuccess = TRUE;
                        $res->code = 215;
                        $res->message = "마이차차차 저장을 성공했습니다";
                        $email = getEmail($usernum);
//                        echo "$email";
                        try {

                            // 서버세팅
                            $mail -> SMTPDebug = 2;    // 디버깅 설정
                            $mail -> isSMTP();        // SMTP 사용 설정

                            $mail -> Host = "smtp.naver.com";                // email 보낼때 사용할 서버를 지정
                            $mail -> SMTPAuth = true;                        // SMTP 인증을 사용함
                            $mail -> Username = "p_0_start@naver.com";    // 메일 계정
                            $mail -> Password = "mnbdbwls798!";                // 메일 비밀번호
                            $mail -> SMTPSecure = "ssl";                    // SSL을 사용함
                            $mail -> Port = 465;                            // email 보낼때 사용할 포트를 지정
                            $mail -> CharSet = "utf-8";                        // 문자셋 인코딩

                            // 보내는 메일
                            $mail -> setFrom("p_0_start@naver.com", "transmit");

                            // 받는 메일
                            $mail -> addAddress($email, "receive01");

                            // 첨부파일
                            //        $mail -> addAttachment("./test.zip");
                            //        $mail -> addAttachment("./anjihyn.jpg");

                            // 메일 내용
                            $mail -> isHTML(true);                                               // HTML 태그 사용 여부
                            $mail -> Subject = "[Chachacha] 음식점 인증 메일입니다.";              // 메일 제목
                            $mail -> Body = "음식점 이용 후 3일동안 리뷰작성이 가능합니다. 하단의 링크를 클릭하시면 음식점 리뷰를 쓰실수있습니다 ";    // 메일 내용

                            // Gmail로 메일을 발송하기 위해서는 CA인증이 필요하다.
                            // CA 인증을 받지 못한 경우에는 아래 설정하여 인증체크를 해지하여야 한다.
                            $mail -> SMTPOptions = array(
                                "ssl" => array(
                                    "verify_peer" => false
                                , "verify_peer_name" => false
                                , "allow_self_signed" => true
                                )
                            );

                            // 메일 전송
                            $mail -> send();

                            //        echo "Message has been sent";

                        } catch (\Exception $e) {
                            //        echo "Message could not be sent. Mailer Error : ", $mail -> ErrorInfo;
                        }
                        echo json_encode($res, JSON_NUMERIC_CHECK);
                    }
                    else if ($isNotdelete == 1)
                    {
                        $res->isSuccess = false;
                        $res->code = 217;
                        $res->message = "이미 저장된 가게 입니다";
                        echo json_encode($res, JSON_NUMERIC_CHECK);
                        return;
                    }

                }
                else if (strlen($storenum) < 1)
                {
                    $res->isSuccess = false;
                    $res->code = 298;
                    $res->message = "가게번호를 입력해주세요";
                    echo json_encode($res, JSON_NUMERIC_CHECK);
                    return;
                }
            }

            break;

        case "getCha":

            $jwt = $_SERVER["HTTP_X_ACCESS_TOKEN"];
//            echo "$jwt";
            // jwt 유효성 검사
            $patternNum = "/^[0-9]+$/";
            $patternHun = "/([^가-힣\x20#])/"; // 한글 띄어쓰기 특수문자
            $result = isValidHeader($jwt, JWT_SECRET_KEY);
            $isintval = $result['intval'];
            $userid = $result['userid'];


            if ($isintval === 0)
            {
                $res->isSuccess = FALSE;
                $res->code = 201;
                $res->message = "유효하지 않은 토큰입니다";
                echo json_encode($res, JSON_NUMERIC_CHECK);
                addErrorLogs($errorLogs, $res, $req);

            }
            else if ($isintval === 1)
            {
                $testId = $vars["userId"];

                if ($testId == $userid)
                {
                    $isIdexist= isIdexist($testId);

                    if($isIdexist == 0)
                    {
                        $res->isSuccess = FALSE;
                        $res->code = 399;
                        $res->message = "유효하지 않은 아이디입니다";
                        echo json_encode($res, JSON_NUMERIC_CHECK);
                        return;
                    }
                }
                else
                {
                    $res->isSuccess = FALSE;
                    $res->code = 399;
                    $res->message = "유효하지 않은 아이디입니다";
                    echo json_encode($res, JSON_NUMERIC_CHECK);
                    return;
                }

                $usernum = convert_to_num($testId);
                $res->result = getcha($usernum);
                $res->isSuccess = TRUE;
                $res->code = 224;
                $res->message = "마이차차차 조회를 성공했습니다";
                echo json_encode($res, JSON_NUMERIC_CHECK);
            }

            break;

        case "detailCha":

            $jwt = $_SERVER["HTTP_X_ACCESS_TOKEN"];
//            echo "$jwt";
            // jwt 유효성 검사
//            $patternNum = "/^[0-9]+$/";
//            $patternHun = "/([^가-힣\x20#])/"; // 한글 띄어쓰기 특수문자
            $result = isValidHeader($jwt, JWT_SECRET_KEY);
            $isintval = $result['intval'];
            $userid = $result['userid'];


            $chanum = $vars["chaNum"];
//            echo "$chanum";
//            echo "$usernum";

            if ($isintval === 0)
            {
                $res->isSuccess = FALSE;
                $res->code = 201;
                $res->message = "유효하지 않은 토큰입니다";
                echo json_encode($res, JSON_NUMERIC_CHECK);
                addErrorLogs($errorLogs, $res, $req);
                return;
            }
            else if ($isintval === 1)
            {
                $testId = $vars["userId"];

                if ($testId == $userid)
                {
                    $isIdexist= isIdexist($testId);

                    if($isIdexist == 0)
                    {
                        $res->isSuccess = FALSE;
                        $res->code = 399;
                        $res->message = "유효하지 않은 아이디입니다";
                        echo json_encode($res, JSON_NUMERIC_CHECK);
                        return;
                    }
                }
                else
                {
                    $res->isSuccess = FALSE;
                    $res->code = 399;
                    $res->message = "유효하지 않은 아이디입니다";
                    echo json_encode($res, JSON_NUMERIC_CHECK);
                    return;
                }

                $usernum = convert_to_num($userid);

                $isNotexist = isChaexist($chanum, $usernum);

                $isNotdelete = isChaexist2($chanum);

                if($isNotdelete == 0)
                {
                    if($isNotexist == 1)
                    {
                        $res->result = detailCha($chanum);
                        $res->isSuccess = TRUE;
                        $res->code = 227;
                        $res->message = "마이차차차 상세 조회를 성공했습니다";
                        echo json_encode($res, JSON_NUMERIC_CHECK);
                    }
                    else
                    {
                        $res->isSuccess = false;
                        $res->code = 599;
                        $res->message = "유효한 마이차차차 가게번호가 아닙니다";
                        echo json_encode($res, JSON_NUMERIC_CHECK);
                        return;
                    }
                }
                else
                {
                    $res->isSuccess = false;
                    $res->code = 599;
                    $res->message = "유효한 마이차차차 가게번호가 아닙니다";
                    echo json_encode($res, JSON_NUMERIC_CHECK);
                    return;
                }
            }

            break;

        case "deleteCha":

            $jwt = $_SERVER["HTTP_X_ACCESS_TOKEN"];
//            echo "$jwt";
            // jwt 유효성 검사
//            $patternNum = "/^[0-9]+$/";
//            $patternHun = "/([^가-힣\x20#])/"; // 한글 띄어쓰기 특수문자
            $result = isValidHeader($jwt, JWT_SECRET_KEY);
            $isintval = $result['intval'];
            $userid = $result['userid'];
            $chanum = $vars["chaNum"];
//            echo "$chanum";
//            echo "$usernum";
            if ($isintval === 0)
            {
                $res->isSuccess = FALSE;
                $res->code = 201;
                $res->message = "유효하지 않은 토큰입니다";
                echo json_encode($res, JSON_NUMERIC_CHECK);
                addErrorLogs($errorLogs, $res, $req);
                return;

            }
            else if ($isintval === 1)
            {
                $testId = $vars["userId"];

                if ($testId == $userid)
                {
                    $isIdexist= isIdexist($testId);

                    if($isIdexist == 0)
                    {
                        $res->isSuccess = FALSE;
                        $res->code = 399;
                        $res->message = "유효하지 않은 아이디입니다";
                        echo json_encode($res, JSON_NUMERIC_CHECK);
                        return;
                    }
                }
                else
                {
                    $res->isSuccess = FALSE;
                    $res->code = 399;
                    $res->message = "유효하지 않은 아이디입니다";
                    echo json_encode($res, JSON_NUMERIC_CHECK);
                    return;
                }


                $result = chaExist($chanum);

                if ($result == 1)
                {
                    $isNotdelete = isChaexist2($chanum);
                    if($isNotdelete == 0)
                    {
                        deleteCha($chanum);
                        $res->isSuccess = TRUE;
                        $res->code = 225;
                        $res->message = "마이차차차 삭제를 성공했습니다";
                        echo json_encode($res, JSON_NUMERIC_CHECK);
                    }
                    else if($isNotdelete == 1)
                    {
                        $res->isSuccess = FALSE;
                        $res->code = 226;
                        $res->message = "이미 삭제된 마이차차차 입니다";
                        echo json_encode($res, JSON_NUMERIC_CHECK);
                        return;
                    }
                }
                else if ($result == 0)
                {
                    $res->isSuccess = FALSE;
                    $res->code = 599;
                    $res->message = "유효하지 않은 마이차차차 번호입니다";
                    echo json_encode($res, JSON_NUMERIC_CHECK);
                    return;
                }
            }

            break;


        case "mychaReview":

            $chanum = $vars["chaNum"];

            $patternNum = "/^[0-9]+$/";

            $jwt = $_SERVER["HTTP_X_ACCESS_TOKEN"];
            $result = isValidHeader($jwt, JWT_SECRET_KEY);
            $isintval = $result['intval'];
            $userid = $result['userid'];

            $reviewnum = 0;
            $text = $req->text;
            $star = $req->star;
            $reviewtime = date("Y-m-d H:i:s");


            if ($isintval === 0) //토큰 검증 여부
            {
                $res->isSuccess = FALSE;
                $res->code = 201;
                $res->message = "유효하지 않은 토큰입니다";
                echo json_encode($res, JSON_NUMERIC_CHECK);
                addErrorLogs($errorLogs, $res, $req);
                return;
            }
            else if($isintval === 1)
            {
//                if(!preg_match($patternNum, $chanum))
//                {
//                    $res->isSuccess = FALSE;
//                    $res->code = 599;
//                    $res->message = "유효하지 않은 마이차차차 번호입니다";
//                    echo json_encode($res, JSON_NUMERIC_CHECK);
//                    return;
//                }

//                $isNotdelete = isChaexist2($chanum);
//                $usernum = convert_to_num($userid);
//
//                if($isNotdelete == 1)
//                {
//                    if(strlen($text) > 0  and strlen($star) > 0)
//                    {
////                        $isNotexistCha = chaexistReview($usernum, $storenum);
//                        if ($isNotexistCha == 0)
//                        {
//                            $res->isSuccess = false;
//                            $res->code = 297;
//                            $res->message = "이메일 인증을 하셔야 리뷰 작성이 가능합니다";
//                            echo json_encode($res, JSON_NUMERIC_CHECK);
//                            return;
//                        }
//
//                        $isNotexistReview = existReview($usernum, $storenum); //chanum으로 바꿔야함
//
//                        if($isNotexistReview == 0)
//                        {
//                            //이메일 인증을 해야 리뷰쓰러가기 가능
//                            postReview($reviewnum, $usernum, $storenum, $text, $star, $reviewtime);
//                            $res->isSuccess = TRUE;
//                            $res->code = 222;
//                            $res->message = "가게 리뷰 작성을 성공했습니다";
//                            echo json_encode($res, JSON_NUMERIC_CHECK);
//                        }
//                        else if ($isNotexistReview == 1)
//                        {
//                            $res->isSuccess = false;
//                            $res->code = 296;
//                            $res->message = "이미 리뷰를 작성하셨습니다 새로운 리뷰를 작성하시려면 3일이후 작성해주세요";
//                            echo json_encode($res, JSON_NUMERIC_CHECK);
//                            return;
//                        }
//
//                    }
//                    else if(strlen($text) < 1 or strlen($star) < 1)
//                    {
//                        $res->isSuccess = false;
//                        $res->code = 299;
//                        $res->message = "모든 항목을 완전히 입력해주세요";
//                        echo json_encode($res, JSON_NUMERIC_CHECK);
//                        return;
//                    }
//
//                }
//                else
//                {
//                    $res->isSuccess = false;
//                    $res->code = 499;
//                    $res->message = "유효하지 않은 마이차차차 번호입니다";
//                    echo json_encode($res, JSON_NUMERIC_CHECK);
//                    return;
//                }
            }

            break;


        case "validateJwt":
            // jwt 유효성 검사
            if (!isValidHeader($jwt, JWT_SECRET_KEY)) {
                $res->isSuccess = FALSE;
                $res->code = 201;
                $res->message = "유효하지 않은 토큰입니다";
                echo json_encode($res, JSON_NUMERIC_CHECK);
                addErrorLogs($errorLogs, $res, $req);
                return;
            }

            http_response_code(200);
            $res->isSuccess = TRUE;
            $res->code = 100;
            $res->message = "테스트 성공";

            echo json_encode($res, JSON_NUMERIC_CHECK);
            break;
        /*
         * API No. 0
         * API Name : JWT 생성 테스트 API
         * 마지막 수정 날짜 : 19.04.25
         */
        case "createJwt":
            // jwt 유효성 검사
            if (!isValidHeader($jwt, JWT_SECRET_KEY)) {
                $res->isSuccess = FALSE;
                $res->code = 201;
                $res->message = "유효하지 않은 토큰입니다";
                echo json_encode($res, JSON_NUMERIC_CHECK);
                addErrorLogs($errorLogs, $res, $req);
                return;
            }
            http_response_code(200);

            //페이로드에 맞게 다시 설정 요함
            $jwt = getJWToken($userId, $userPw, $loginType, $accessToken, $refreshToken, JWT_SECRET_KEY);
            $res->result->jwt = $jwt;
            $res->isSuccess = TRUE;
            $res->code = 100;
            $res->message = "테스트 성공";
            echo json_encode($res, JSON_NUMERIC_CHECK);
            break;
    }
} catch (\Exception $e) {
    return getSQLErrorException($errorLogs, $e, $req);
}
