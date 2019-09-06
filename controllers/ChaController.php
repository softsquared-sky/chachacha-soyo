<?php
require 'function.php';

const JWT_SECRET_KEY = "TEST_KEYTEST_KEYTEST_KEYTEST_KEYTEST_KEYTEST_KEYTEST_KEYTEST_KEYTEST_KEYTEST_KEYTEST_KEYTEST_KEYTEST_KEY";


$res = (Object)Array();
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

        case "index":
            echo "되는거니";
            break;

        case "myCha":
            echo "ha";

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
