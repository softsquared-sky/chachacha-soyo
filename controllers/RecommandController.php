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

        case "searchingStore": //차차차 마이차차차했던 가게들은 검색 x


            $jwt = $_SERVER["HTTP_X_ACCESS_TOKEN"];
//            echo "$jwt";
            // jwt 유효성 검사
            $result = isValidHeader($jwt, JWT_SECRET_KEY);
            $isintval = $result['intval'];
            $userid = $result['userid'];


            $usernum = convert_to_num($userid);

            if ($isintval === 0) //토큰 검증 여부
            {
                $res->isSuccess = FALSE;
                $res->code = 201;
                $res->message = "유효하지 않은 토큰입니다";
                echo json_encode($res, JSON_NUMERIC_CHECK);
                addErrorLogs($errorLogs, $res, $req);
                return;
            } else if ($isintval === 1) {

                $people = $req->people;
                $kind = $req->kind;
                $mode = $req->mode;
                $page = $req->page;
                $size = $req->size;

                $patternHun = "/([^가-힣\x20])/"; //한글 띄어쓰기 /^[가-힣\s]+$/
                $patternHun2 = "/([^가-힣\x20#])/"; //한글 띄어쓰기 /^[가-힣\s]+$/ 한글 특수문자 통과
                $patternMode = "/(?:#)[^\s\t\n\r]+/"; //# 뒤에 문자열
                $pattermKind = '/[^\x{1100}-\x{11FF}\x{3130}-\x{318F}\x{AC00}-\x{D7AF}0-9a-zA-Z_ -]/u';
                $patternNum =  "/^[0-9]+$/";
                $pattenstr = "상관없음";
                $isNotstr = 0;


                if ($people < 1 or $people > 6) {
                    $res->isSuccess = false;
                    $res->code = 250;
                    $res->message = "인원 형식에 맞춰 입력해주세요";
                    echo json_encode($res, JSON_NUMERIC_CHECK);
                    return;
                }

                if (preg_match($pattermKind, $kind)) {
                    $res->isSuccess = false;
                    $res->code = 251;
                    $res->message = "가게 종류는 한글과 영어만 입력해주세요";
                    echo json_encode($res, JSON_NUMERIC_CHECK);
                    return;
                } else {
                    $strKind = explode(" ", $kind);
                    $countKind = count($strKind);
//                    echo json_encode($strKind);
                }

                if (preg_match($patternMode, $mode)) {
                    if (preg_match($patternHun2, $mode)) {
                        $res->isSuccess = false;
                        $res->code = 252;
                        $res->message = "무드는 #태그를 붙혀 한글만 입력해주세요";
                        echo json_encode($res, JSON_NUMERIC_CHECK);
                        return;
                    } else {
                        $mode = str_replace("#", "", $mode);
//                        echo "$mode";
                        $r = '%';
                        $mode = $r.$mode.$r;
                    }

                }
                else if (!preg_match($patternMode, $mode)) // 상관없음 필터
                {
                    if (strpos($mode, $pattenstr) !== false) {
                        $isNotstr = 1;
                    } else {
                        $isNotstr = 0;
                    }

                    if ($isNotstr == 0) {
                        $res->isSuccess = false;
                        $res->code = 252;
                        $res->message = "무드는 #태그를 붙혀 한글만 입력해주세요";
                        echo json_encode($res, JSON_NUMERIC_CHECK);
                        return;
                    }
                }

                if(!preg_match($patternNum, $page))
                {
                    $res->isSuccess = false;
                    $res->code = 253;
                    $res->message = "현재 사용자의 조회를 숫자로 입력해주세요";
                    echo json_encode($res, JSON_NUMERIC_CHECK);
                    return;
                }

                if(!preg_match($patternNum, $size))
                {
                    $res->isSuccess = false;
                    $res->code = 254;
                    $res->message = "출력정도의 양을 숫자로 입력해주세요";
                    echo json_encode($res, JSON_NUMERIC_CHECK);
                    return;
                }


                if (strlen($people) > 0 and strlen($kind) > 0 and strlen($mode) > 0 and strlen($page) > 0 and strlen($size) > 0)
                {
                    if($isNotstr == 0)
                    {
                        $res->result = recommendStore($people, $strKind, $usernum, $mode, $page, $size);
                        $res->isSuccess = true;
                        $res->code = 206;
                        $res->message = "가게 추천 검색 조회를 성공하였습니다";
                        echo json_encode($res, JSON_NUMERIC_CHECK);
                    }
                    else if($isNotstr == 1)
                    {
                        $res->result = withoutMode_recommendStore($people, $strKind, $usernum, $page, $size);
                        $res->isSuccess = true;
                        $res->code = 206;
                        $res->message = "가게 추천 검색 조회를 성공하였습니다";
                        echo json_encode($res, JSON_NUMERIC_CHECK);
                    }
                }
                else if (strlen($people) < 1 or strlen($kind) < 1 or  strlen($mode) < 1 or strlen($page) < 1 or strlen($size) < 1)
                {
                    $res->isSuccess = false;
                    $res->code = 109;
                    $res->message = "모든 항목을 완전히 입력해주세요";
                    echo json_encode($res, JSON_NUMERIC_CHECK);
                    return;
                }
            }

            break;

        case "storeDetail":

            $storenum = $vars["storeNum"];

            $patternNum = "/^[0-9]+$/";

            $jwt = $_SERVER["HTTP_X_ACCESS_TOKEN"];
//            echo "$jwt";
            // jwt 유효성 검사
            $result = isValidHeader($jwt, JWT_SECRET_KEY);
            $isintval = $result['intval'];
            $userid = $result['userid'];

            $usernum = convert_to_num($userid);


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
                    $res->code = 208;
                    $res->message = "숫자 형식에 맞게 가게 번호를 입력해주세요";
                    echo json_encode($res, JSON_NUMERIC_CHECK);
                    return;
                }

                $isNotexist = isStoreexist($storenum); //여기부터

                if($isNotexist == 1)
                {
                    $res->result =  storeDetail($usernum, $storenum);
                    $res->isSuccess = TRUE;
                    $res->code = 207;
                    $res->message = "가게 상세 조회를 성공했습니다";
                    echo json_encode($res, JSON_NUMERIC_CHECK);
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

        case "storeReview":


            $storenum = $vars["storeNum"];

            $patternNum = "/^[0-9]+$/";

            $jwt = $_SERVER["HTTP_X_ACCESS_TOKEN"];
//            echo "$jwt";
            // jwt 유효성 검사
            $result = isValidHeader($jwt, JWT_SECRET_KEY);
            $isintval = $result['intval'];

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
                    $res->code = 210;
                    $res->message = "숫자 형식에 맞게 가게 번호를 입력해주세요";
                    echo json_encode($res, JSON_NUMERIC_CHECK);
                    return;
                }

                $isNotexist = isStoreexist($storenum);
//                $res =  storeReview($storenum);
                if($isNotexist == 1)
                {
                    $res->result = storeReview($storenum);
                    $res->isSuccess = TRUE;
                    $res->code = 209;
                    $res->message = "가게 리뷰 조회를 성공했습니다";
                    echo json_encode($res, JSON_NUMERIC_CHECK);
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

        case "storeMenu":

            $storenum = $vars["storeNum"];

            $patternNum = "/^[0-9]+$/";

            $jwt = $_SERVER["HTTP_X_ACCESS_TOKEN"];
//            echo "$jwt";
            // jwt 유효성 검사
            $result = isValidHeader($jwt, JWT_SECRET_KEY);
            $isintval = $result['intval'];

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
                    $res->code = 212;
                    $res->message = "숫자 형식에 맞게 가게 번호를 입력해주세요";
                    echo json_encode($res, JSON_NUMERIC_CHECK);
                    return;
                }

                $isNotexist = isStoreexist($storenum);

                if($isNotexist == 1)
                {
                    $res->result = storeMenu($storenum);
                    $res->isSuccess = TRUE;
                    $res->code = 211;
                    $res->message = "가게 메뉴 조회를 성공했습니다";
                    echo json_encode($res, JSON_NUMERIC_CHECK);
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

        case "storeBookmark":

            $storenum = $vars["storeNum"];

            $patternNum = "/^[0-9]+$/";

            $jwt = $_SERVER["HTTP_X_ACCESS_TOKEN"];
//            echo "$jwt";
            // jwt 유효성 검사
            $result = isValidHeader($jwt, JWT_SECRET_KEY);
            $isintval = $result['intval'];
            $userid = $result['userid'];
//            echo "$userid";
            $usernum = convert_to_num($userid);
//            echo "$usernum";

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
                    $res->code = 330;
                    $res->message = "숫자 형식에 맞게 가게 번호를 입력해주세요";
                    echo json_encode($res, JSON_NUMERIC_CHECK);
                    return;
                }

                $isNotexist = isStoreexist($storenum);

                if($isNotexist == 1)
                {
                    $isnotexistMark = checkMark($usernum, $storenum); //저장되어있는지를 확인
                    $deletenum = getDeletenum($usernum, $storenum);

                    if ($isnotexistMark == 0)
                    {
                        postBookmark($usernum, $storenum);
                        $res->isSuccess = TRUE;
                        $res->code = 331;
                        $res->message = "가게 즐겨찾기 저장을 성공했습니다";
                        echo json_encode($res, JSON_NUMERIC_CHECK);
                    }

                    if ($isnotexistMark == 1)
                    {
                        if($deletenum == 0) //마이차차차 존재할경우
                        {
                            deleteMark($usernum, $storenum);
                            $res->isSuccess = TRUE;
                            $res->code = 332;
                            $res->message = "가게 즐겨찾기 저장 해제를 성공했습니다";
                            echo json_encode($res, JSON_NUMERIC_CHECK);
                        }
                        else if($deletenum == 1) //마이차차차 존재하지 않을 경우
                        {
                            resetBookmark($usernum, $storenum);
                            $res->isSuccess = TRUE;
                            $res->code = 331;
                            $res->message = "가게 즐겨찾기 저장을 성공했습니다";
                            echo json_encode($res, JSON_NUMERIC_CHECK);
                        }
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
