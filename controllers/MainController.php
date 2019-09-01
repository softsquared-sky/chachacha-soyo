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

        case "myPage":

//            echo "test";
            $jwt = $_SERVER["HTTP_X_ACCESS_TOKEN"];
//            echo "$jwt";
            // jwt 유효성 검사
            $result = isValidHeader($jwt, JWT_SECRET_KEY);
            $isintval = $result['intval'];
//            $userid = $result['userid'];

            if ($isintval === 0) //토큰 검증 여부
            {
                $res->isSuccess = FALSE;
                $res->code = 201;
                $res->message = "유효하지 않은 토큰입니다";
                echo json_encode($res, JSON_NUMERIC_CHECK);
                addErrorLogs($errorLogs, $res, $req);
                return;
            } else if ($isintval === 1) {
//                echo "$intval , $userid";
                $usernum = convert_to_num($vars["userId"]);
//                echo "$usernum";
                $res->result = myPage($usernum); // 토큰 발행 api
                $res->isSuccess = TRUE;
                $res->code = 115;
                $res->message = "마이페이지 조회를 성공했습니다";
                echo json_encode($res, JSON_NUMERIC_CHECK);

            }
            break;


        case "patchMypage":

            $jwt = $_SERVER["HTTP_X_ACCESS_TOKEN"];
//            echo "$jwt";
            // jwt 유효성 검사
            $result = isValidHeader($jwt, JWT_SECRET_KEY);
            $isintval = $result['intval'];
//            $userid = $result['userid'];

            $name = $req->name;
            $writing = $req->writing;
            $email = $req->email;
            $phone = $req->phone;

            $patternName = "/([^가-힣\x20])/"; //한글이름
            $patternPhone = "/^01[0-9]{8,9}$/"; // 핸드폰번호 형식
//            $patternWriting = "/^[가-힣a-zA-Z]+$/"; //소개글 형식 한글 영어만 가능
            $patternWriting = '/([\xEA-\xED][\x80-\xBF]{2}|[a-zA-Z0-9_ -])/'; //정규식 띄어쓰기 _ - 이거 넣기

            if ($isintval === 0) //토큰 검증 여부
            {
                $res->isSuccess = FALSE;
                $res->code = 201;
                $res->message = "유효하지 않은 토큰입니다";
                echo json_encode($res, JSON_NUMERIC_CHECK);
                addErrorLogs($errorLogs, $res, $req);
                return;
            } else if ($isintval === 1) {
                $usernum = convert_to_num($vars["userId"]);
//                echo "$usernum";
//                echo "토큰검증 성공";

//                echo "$name, $writing, $email, $phone";
                if (strlen($usernum) > 0 and strlen($name) > 0 and strlen($writing) > 0 and strlen($email) > 0 and strlen($phone) > 0) {
                    if (preg_match($patternName, $name)) {
                        $res->isSuccess = false;
                        $res->code = 103;
                        $res->message = "이름을 한글로 제대로 입력하세요";
                        echo json_encode($res, JSON_NUMERIC_CHECK);
                        return;
                    }


//                    echo "$writing";

                    if (preg_match_all($patternWriting, $writing, $match)) {
//                          preg_match_all($patternWriting, $writing, $match);
                        $writing = implode('', $match[0]);
//                          echo "$writing";
                    } else {
                        $res->isSuccess = false;
                        $res->code = 116;
                        $res->message = "소개글은 한글과 영어만 입력해주세요";
                        echo json_encode($res, JSON_NUMERIC_CHECK);
                        return;
                    }

                    if (preg_match($patternPhone, $phone)) {
                        $phone = addHyphen($phone);
                        patchMypage($usernum, $name, $writing, $email, $phone);
                        $res->isSuccess = TRUE;
                        $res->code = 200;
                        $res->message = "마이페이지 수정을 성공했습니다";
                        echo json_encode($res, JSON_NUMERIC_CHECK);
                    } else {
                        $res->isSuccess = false;
                        $res->code = 106;
                        $res->message = "번호 형식에 맞춰 입력해주세요";
                        echo json_encode($res, JSON_NUMERIC_CHECK);
                        return;
                    }
                } else if (strlen($usernum) < 1 or strlen($name) < 1 or strlen($writing) < 1 or strlen($email) < 1 or strlen($phone) < 1) {
                    $res->isSuccess = false;
                    $res->code = 109;
                    $res->message = "모든 항목을 완전히 입력해주세요";
                    echo json_encode($res, JSON_NUMERIC_CHECK);
                    return;
                }
            }
            break;

        case "reView":

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
            } else if ($isintval === 1) {
                $patternId = "/^[a-z0-9_]{4,10}$/"; // 4자 이상 10자 이하 영소문자/숫자/_ 허용
                $userid = ($vars['userId']);
                if (!preg_match($patternId, $userid)) {
                    $res->isSuccess = FALSE;
                    $res->code = 203;
                    $res->message = "영/소문자,숫자 조합 4자리 이상 10자리 이하로 아이디를 입력하세요";
                    echo json_encode($res, JSON_NUMERIC_CHECK);
                    addErrorLogs($errorLogs, $res, $req);
                    return;
                }
                $usernum = convert_to_num($userid);
//                echo "$usernum";
//                echo "토큰검증 성공";
                $res->result = myReview($usernum); // 토큰 발행 api
                $res->isSuccess = TRUE;
                $res->code = 202;
                $res->message = "마이리뷰 조회를 성공했습니다";
                echo json_encode($res, JSON_NUMERIC_CHECK);
            }

            break;


        case "bookMark":

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
            } else if ($isintval === 1) {
                $patternId = "/^[a-z0-9_]{4,10}$/"; // 4자 이상 10자 이하 영소문자/숫자/_ 허용
                $userid = ($vars['userId']);
                if (!preg_match($patternId, $userid)) {
                    $res->isSuccess = FALSE;
                    $res->code = 205;
                    $res->message = "영/소문자,숫자 조합 4자리 이상 10자리 이하로 아이디를 입력하세요";
                    echo json_encode($res, JSON_NUMERIC_CHECK);
                    addErrorLogs($errorLogs, $res, $req);
                    return;
                }
                $usernum = convert_to_num($userid);
//                echo "$usernum";
//                echo "토큰검증 성공";
                $res->result = mybookMark($usernum); // 토큰 발행 api
                $res->isSuccess = TRUE;
                $res->code = 204;
                $res->message = "즐겨찾기 조회를 성공했습니다";
                echo json_encode($res, JSON_NUMERIC_CHECK);

            }

            break;

        case "searchingStore":


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
            } else if ($isintval === 1) {

                $people = $req->people;
                $kind = $req->kind;
                $mode = $req->mode;

                $patternHun = "/([^가-힣\x20])/"; //한글 띄어쓰기 /^[가-힣\s]+$/
                $patternHun2 = "/([^가-힣\x20#])/"; //한글 띄어쓰기 /^[가-힣\s]+$/ 한글 특수문자 통과
                $patternMode = "/(?:#)[^\s\t\n\r]+/"; //# 뒤에 문자열
                $pattenstr = "상관없음";

                if ($people < 1 or $people > 6) {
                    $res->isSuccess = false;
                    $res->code = 116;
                    $res->message = "인원 형식에 맞춰 입력해주세요";
                    echo json_encode($res, JSON_NUMERIC_CHECK);
                    return;
                }


                if (preg_match($patternHun, $kind)) {
                    $res->isSuccess = false;
                    $res->code = 116;
                    $res->message = "가게 종류는 한글만 입력해주세요";
                    echo json_encode($res, JSON_NUMERIC_CHECK);
                    return;
                } else {
                    $strKind = explode(" ", $kind);
                    echo json_encode($strKind);
                    $countKind = count($strKind);
                }

                if (preg_match($patternMode, $mode)) {
                    if (preg_match($patternHun2, $mode)) {
                        $res->isSuccess = false;
                        $res->code = 116;
                        $res->message = "무드는 #태그를 붙혀 한글만 입력해주세요";
                        echo json_encode($res, JSON_NUMERIC_CHECK);
                        return;
                    } else {
                        $mode = str_replace("#", "", $mode);
                        $strMode = explode(" ", $mode);

                        echo json_encode($strMode);
                        $countMode = count($strMode); //카운트 12개 까지 있음

                        foreach ($strMode as $key => &$value) {
                            $r = '%';
                            $modeValue = $r . $value . $r;
                            $value = $modeValue;
                        }
                    }

                } else if (!preg_match($patternMode, $mode)) // 상관없음 필터
                {
                    if (strpos($mode, $pattenstr) !== false) {
//                    echo "포함되어 있습니다만...";
                        $isNotstr = 1;
//                    echo "$mode";
                    } else {
//                    echo "없군요.";
                        $isNotstr = 0;
                    }

                    if ($isNotstr == 0) {
                        $res->isSuccess = false;
                        $res->code = 116;
                        $res->message = "무드는 #태그를 붙혀 한글만 입력해주세요";
                        echo json_encode($res, JSON_NUMERIC_CHECK);
                        return;
                    }
                }

                if (strlen($people) > 0 and strlen($kind) > 0 and strlen($mode) > 0) {
                    $kindInt = 2;
                    $modeInt = 2;
                    $addedQuerykind = " OR kind LIKE ?";
                    $addedQuerykindreuslt = " OR kind LIKE ?";

                    while ($countKind > $kindInt) {
                        $kindInt = ++$kindInt;

                        $addedQuerykindreuslt = $addedQuerykind . $addedQuerykindreuslt;
                    }

//            echo "$addedQuerykindreuslt";

                    $addedQuerymode = " OR mode LIKE ?";
                    $addedQuerymoderesult = " OR mode LIKE ?";

                    while ($countMode > $modeInt) {
                        $modeInt = ++$modeInt;
                        $addedQuerymoderesult = $addedQuerymode . $addedQuerymoderesult;
                    }

//            echo "$addedQuerymoderesult";

                    $res->result = getStore($people, $strKind, $addedQuerykindreuslt, $strMode, $addedQuerymoderesult);
//            $res->isSuccess = true;
//            $res->code = 206;
//            $res->message = "가게 추천 검색 조회를 성공하였습니다";
//            echo json_encode($res, JSON_NUMERIC_CHECK);

                }
//        else if (strlen($people) < 1 or strlen($kind) < 1 or  strlen($mode) < 1)
//        {
//            $res->isSuccess = false;
//            $res->code = 109;
//            $res->message = "모든 항목을 완전히 입력해주세요";
//            echo json_encode($res, JSON_NUMERIC_CHECK);
//            return;
//        }
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

                $res->result =  storeDetail($storenum); // 토큰 발행 api
                $res->isSuccess = TRUE;
                $res->code = 207;
                $res->message = "가게 상세 조회를 성공했습니다";
                echo json_encode($res, JSON_NUMERIC_CHECK);

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
//                $res =  storeReview($storenum);

                $res->result = storeReview($storenum);
                $res->isSuccess = TRUE;
                $res->code = 209;
                $res->message = "가게 리뷰 조회를 성공했습니다";
                echo json_encode($res, JSON_NUMERIC_CHECK);

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
                echo "토큰 통과";
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
