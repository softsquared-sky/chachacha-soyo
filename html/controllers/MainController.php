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

            echo "test";
            $jwt = $_SERVER["HTTP_X_ACCESS_TOKEN"];
//            echo "$jwt";
            // jwt 유효성 검사
            $result = isValidHeader($jwt, JWT_SECRET_KEY);
            $intval = $result['intval'];
            $userid = $result['userid'];

            if ($intval === 0) //토큰 검증 여부
            {
                $res->isSuccess = FALSE;
                $res->code = 201;
                $res->message = "유효하지 않은 토큰입니다";
                echo json_encode($res, JSON_NUMERIC_CHECK);
                addErrorLogs($errorLogs, $res, $req);
                return;
            }
            else if ($intval === 1)
            {
//                echo "$intval , $userid";
                $usernum =convert_to_num($userid);
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
            $intval = $result['intval'];
            $userid = $result['userid'];

            $patterName = "/([^가-힣\x20])/"; //한글이름

            if ($intval === 0) //토큰 검증 여부
            {
                $res->isSuccess = FALSE;
                $res->code = 201;
                $res->message = "유효하지 않은 토큰입니다";
                echo json_encode($res, JSON_NUMERIC_CHECK);
                addErrorLogs($errorLogs, $res, $req);
                return;
            }
            else if($intval === 1)
            {
                $usernum =convert_to_num($userid);
//                echo "$usernum";
//                echo "토큰검증 성공";
                $name = $req->name;
                $writing = $req->writing;
                $email = $req->email;
//                echo "$name, $writing, $email";
                if (strlen($usernum) > 0 and strlen($name) > 0 and strlen($writing) > 0 and strlen($email) > 0)
                {
                    if (!preg_match($patterName, $name))
                    {
                        patchMypage($usernum, $name, $writing, $email);
                        $res->isSuccess = TRUE;
                        $res->code = 118;
                        $res->message = "마이페이지 수정을 성공했습니다";
                        echo json_encode($res, JSON_NUMERIC_CHECK);
                    }
                    else
                    {
                        $res->isSuccess = false;
                        $res->code = 105;
                        $res->message = "이름을 한글로 제대로 입력해쉐요";
                        echo json_encode($res, JSON_NUMERIC_CHECK);
                    }
                }
                else if (strlen($name) < 1)
                {
                    $res->isSuccess = false;
                    $res->code = 110;
                    $res->message = "이름를 엽력해주세요";
                    echo json_encode($res, JSON_NUMERIC_CHECK);
                }
                else if (strlen($writing) < 1)
                {
                    $res->isSuccess = false;
                    $res->code = 119;
                    $res->message = "소개글을 엽력해주세요";
                    echo json_encode($res, JSON_NUMERIC_CHECK);
                }
                else if (strlen($email) < 1)
                {
                    $res->isSuccess = false;
                    $res->code = 112;
                    $res->message = "이메일을 엽력해주세요";
                    echo json_encode($res, JSON_NUMERIC_CHECK);
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
