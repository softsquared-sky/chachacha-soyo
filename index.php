<?php
require './pdos/DatabasePdo.php';
require './pdos/IndexPdo.php';
require './vendor/autoload.php';

use \Monolog\Logger as Logger;
use Monolog\Handler\StreamHandler;

//echo json_encode($_GET);
date_default_timezone_set('Asia/Seoul');
ini_set('default_charset', 'utf8mb4');

//에러출력하게 하는 코드
error_reporting(E_ALL); ini_set("display_errors", 1);
//
//Main Server API
$dispatcher = FastRoute\simpleDispatcher(function (FastRoute\RouteCollector $r) {
    /* ******************   Test   ****************** */
    $r->addRoute('GET', '/', ['IndexController', 'index']);
    $r->addRoute('POST', '/guest', ['IndexController', 'signupGuest']); //손님 회원가입 API
    $r->addRoute('POST', '/boss', ['IndexController', 'signupBoss']); //사장님 회원가입 API
    $r->addRoute('POST', '/token', ['IndexController', 'loginUser']); // 로그인 API post 토큰
    $r->addRoute('GET', '/user/{userId}', ['MainController', 'myPage']); //마이페이지 조히 API
    $r->addRoute('PATCH', '/user/{userId}', ['MainController', 'patchMypage']); //마이페이지 수정 API
    $r->addRoute('GET', '/user/{userId}/review', ['MainController', 'reView']); //마이 리뷰 조회 API
    $r->addRoute('GET', '/user/{userId}/bookmark', ['MainController', 'bookMark']); //즐겨찾기 조회 API
    $r->addRoute('GET', '/store/{storeNum}', ['MainController', 'storeDetail']); //가게 상세 API
    $r->addRoute('GET', '/store/{storeNum}/review', ['MainController', 'storeReview']); //가게 상세 리뷰 조회 API
    $r->addRoute('GET', '/store/{storeNum}/menu', ['MainController', 'storeMenu']); //가게 상세 메뉴 조회 API

    $r->addRoute('POST', '/store/recommend', ['MainController', 'searchingStore']); //마이차차차 추천 검색 API
    $r->addRoute('POST', '/store/search', ['MainController', 'storeName']); //가게 이름 지역 조회 API
    $r->addRoute('POST', '/store/{storeNum}/review', ['MainController', 'mychaReview']); //마이차차차 가게 리뷰 작성 API

    $r->addRoute('POST', '/user/{userId}/store', ['MainController', 'myCha']); //마이차차차 저장 API
    $r->addRoute('GET', '/user/{userId}/store', ['MainController', 'getCha']); //마이차차차 전체 조회 API
    $r->addRoute('GET', '/user/{userId}/store/{chaNum}', ['MainController', 'detailCha']); //마이차차차 상세 조회 API
    $r->addRoute('DELETE', '/user/{userId}/store/{chaNum}', ['MainController', 'deleteCha']); //마이차차차 삭제 API

    $r->addRoute('GET', '/test', ['IndexController', 'test']);
//    $r->addRoute('POST', '/store/{storeNum}/review', ['IndexController', 'testDetail']);// 가게 리뷰 작성 API 마이차차차에 잇는 가게만 쓰게 하기

//    $r->addRoute('POST', '/test', ['IndexController', 'testPost']);
//    $r->addRoute('GET', '/jwt', ['MainController', 'validateJwt']);
//    $r->addRoute('POST', '/jwt', ['MainController', 'createJwt']);


//    $r->addRoute('GET', '/users', 'get_all_users_handler');
//    // {id} must be a number (\d+)
//    $r->addRoute('GET', '/user/{id:\d+}', 'get_user_handler');
//    // The /{title} suffix is optional
//    $r->addRoute('GET', '/articles/{id:\d+}[/{title}]', 'get_article_handler');
});

// Fetch method and URI from somewhere
$httpMethod = $_SERVER['REQUEST_METHOD'];
$uri = $_SERVER['REQUEST_URI'];

// Strip query string (?foo=bar) and decode URI
if (false !== $pos = strpos($uri, '?')) {
    $uri = substr($uri, 0, $pos);
}
$uri = rawurldecode($uri);

$routeInfo = $dispatcher->dispatch($httpMethod, $uri);

// 로거 채널 생성
$accessLogs = new Logger('ACCESS_LOGS');
$errorLogs = new Logger('ERROR_LOGS');
// log/your.log 파일에 로그 생성. 로그 레벨은 Info
$accessLogs->pushHandler(new StreamHandler('logs/access.log', Logger::INFO));
$errorLogs->pushHandler(new StreamHandler('logs/errors.log', Logger::ERROR));
// add records to the log
//$log->addInfo('Info log');
// Debug 는 Info 레벨보다 낮으므로 아래 로그는 출력되지 않음
//$log->addDebug('Debug log');
//$log->addError('Error log');

switch ($routeInfo[0]) {
    case FastRoute\Dispatcher::NOT_FOUND:
        // ... 404 Not Found
        echo "404 Not Found";
        break;
    case FastRoute\Dispatcher::METHOD_NOT_ALLOWED:
        $allowedMethods = $routeInfo[1];
        // ... 405 Method Not Allowed
        echo "405 Method Not Allowed";
        break;
    case FastRoute\Dispatcher::FOUND:
        $handler = $routeInfo[1];
        $vars = $routeInfo[2];

        switch ($routeInfo[1][0]) {
            case 'IndexController':
                $handler = $routeInfo[1][1];
                $vars = $routeInfo[2];
                require './controllers/IndexController.php';
                break;
            case 'MainController':
                $handler = $routeInfo[1][1];
                $vars = $routeInfo[2];
                require './controllers/MainController.php';
                break;
            /*case 'EventController':
                $handler = $routeInfo[1][1]; $vars = $routeInfo[2];
                require './controllers/EventController.php';
                break;
            case 'ProductController':
                $handler = $routeInfo[1][1]; $vars = $routeInfo[2];
                require './controllers/ProductController.php';
                break;
            case 'SearchController':
                $handler = $routeInfo[1][1]; $vars = $routeInfo[2];
                require './controllers/SearchController.php';
                break;
            case 'ReviewController':
                $handler = $routeInfo[1][1]; $vars = $routeInfo[2];
                require './controllers/ReviewController.php';
                break;
            case 'ElementController':
                $handler = $routeInfo[1][1]; $vars = $routeInfo[2];
                require './controllers/ElementController.php';
                break;
            case 'AskFAQController':
                $handler = $routeInfo[1][1]; $vars = $routeInfo[2];
                require './controllers/AskFAQController.php';
                break;*/
        }

        break;
}
