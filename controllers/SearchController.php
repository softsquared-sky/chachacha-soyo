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


        case "storeName":

            $jwt = $_SERVER["HTTP_X_ACCESS_TOKEN"];
//            echo "$jwt";
            // jwt 유효성 검사
            $result = isValidHeader($jwt, JWT_SECRET_KEY);
            $isintval = $result['intval'];
            $storename = $req->storename;
//            $location = $req->location;
//            $patternLocation = "/([^가-힣\x20])/";
            $patternName = '/([\xEA-\xED][\x80-\xBF]{2}|[a-zA-Z0-9_ -])/';

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
                if(strlen($storename) < 1 or strlen($location) < 1)
                {
                    $res->isSuccess = false;
                    $res->code = 299;
                    $res->message = "모든 항목을 완전히 입력해주세요";
                    echo json_encode($res, JSON_NUMERIC_CHECK);
                    return;
                }

                if (preg_match_all($patternName, $storename, $match)) {
//                          preg_match_all($patternWriting, $writing, $match);
                    $storename = implode('', $match[0]);
                    echo "$storename";
                } else {
                    $res->isSuccess = false;
                    $res->code = 214;
                    $res->message = "가게 이름은 한글과 영어만 입력해주세요";
                    echo json_encode($res, JSON_NUMERIC_CHECK);
                    return;
                }

//                if(preg_match($patternLocation, $location))
//                {
//                    $res->isSuccess = false;
//                    $res->code = 214;
//                    $res->message = "가게 위치는 한글로만 입력해주세요";
//                    echo json_encode($res, JSON_NUMERIC_CHECK);
//                    return;
//                }

                $result = searchstore($storename, $location);
                $res->isSuccess = TRUE;
                $res->code = 213;
                $res->message = "가게 검색을 성공했습니다";
                echo json_encode($res, JSON_NUMERIC_CHECK);
            }

            break;


        case "mychaReview":

            $storenum = $vars["storeNum"];

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
                if(!preg_match($patternNum, $storenum))
                {
                    $res->isSuccess = false;
                    $res->code = 223;
                    $res->message = "숫자 형식에 맞게 가게 번호를 입력해주세요";
                    echo json_encode($res, JSON_NUMERIC_CHECK);
                    return;
                }

                $isNotexist = isStoreexist($storenum);
                $usernum = convert_to_num($userid);

                if($isNotexist == 1)
                {
                    if(strlen($text) > 0  and strlen($star) > 0)
                    {
                        $isNotexistCha = chaexistReview($usernum, $storenum);
                        if ($isNotexistCha == 0)
                        {
                            $res->isSuccess = false;
                            $res->code = 297;
                            $res->message = "가게를 이용하셔야 리뷰 작성이 가능합니다";
                            echo json_encode($res, JSON_NUMERIC_CHECK);
                            return;
                        }

                        $isNotexistReview = existReview($usernum, $storenum);

                        if($isNotexistReview == 0)
                        {
                            postReview($reviewnum, $usernum, $storenum, $text, $star, $reviewtime);
                            $res->isSuccess = TRUE;
                            $res->code = 222;
                            $res->message = "가게 리뷰 작성을 성공했습니다";
                            echo json_encode($res, JSON_NUMERIC_CHECK);
                        }
                        else if ($isNotexistReview == 1)
                        {
                            $res->isSuccess = false;
                            $res->code = 296;
                            $res->message = "이미 리뷰를 작성하셨습니다 새로운 리뷰를 작성하시려면 3일이후 작성해주세요";
                            echo json_encode($res, JSON_NUMERIC_CHECK);
                            return;
                        }

                    }
                    else if(strlen($text) < 1 or strlen($star) < 1)
                    {
                        $res->isSuccess = false;
                        $res->code = 299;
                        $res->message = "모든 항목을 완전히 입력해주세요";
                        echo json_encode($res, JSON_NUMERIC_CHECK);
                        return;
                    }

                }
                else
                    {
                    $res->isSuccess = false;
                    $res->code = 499;
                    $res->message = "유효한 가게번호가 아닙니다";
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
