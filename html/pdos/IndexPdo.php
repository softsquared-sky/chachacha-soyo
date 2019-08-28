<?php

function guest($usernum, $userid, $userpw, $name, $age, $gender, $email,$phone, $signuptime)
{
    $pdo = pdoSqlConnect();
    $query = "INSERT INTO guest (usernum, userid, userpw, name, age, gender, email, phone, signuptime) VALUES (?,?,?,?,?,?,?,?,?);";
    $st = $pdo->prepare($query);
    $st->execute([$usernum, $userid, $userpw, $name, $age, $gender, $email, $phone, $signuptime]);

    $st = null;
    $pdo = null;
}

function boss($usernum, $userid, $userpw, $name, $phone, $signuptime)
{
    $pdo = pdoSqlConnect();
    $query = "INSERT INTO boss (usernum, userid, userpw, username, userphone, signuptime) VALUES (?,?,?,?,?,?);";
    $st = $pdo->prepare($query);
    $st->execute([$usernum, $userid, $userpw, $name, $phone, $signuptime]);

    $st = null;
    $pdo = null;
}

function idcheck_guest($userid)
{
    $pdo = pdoSqlConnect();
    $query = "select exists(SELECT * FROM guest WHERE userid = ?)as exist;";
    $st = $pdo->prepare($query);

    $st->execute([$userid]);
    $st->setFetchMode(PDO::FETCH_ASSOC);
    $res = $st->fetchAll();

    $st=null;
    $pdo = null;

    return intval($res[0]["exist"]);
}


function idcheck_boss($userid)
{
    $pdo = pdoSqlConnect();
    $query = "select exists(SELECT * FROM boss WHERE userid = ?)as exist;";
    $st = $pdo->prepare($query);
    $st->execute([$userid]);
    $st->setFetchMode(PDO::FETCH_ASSOC);
    $res = $st->fetchAll();

    $st=null;
    $pdo = null;

    return intval($res[0]["exist"]);
}

function login($userid, $userpw)
{
    $userid2 = $userid;
    $userpw2 = $userpw;
    $pdo = pdoSqlConnect();
    $query = "select exists(select * from guest, boss where (guest.userid = ? and guest.userpw = ?) or (boss.userpw = ? and boss.userpw = ?) )as exist;";
    $st = $pdo->prepare($query);
    $st->execute([$userid, $userpw, $userid2, $userpw2]);
    $st->setFetchMode(PDO::FETCH_ASSOC);
    $res = $st->fetchAll();
    $st=null;
    $pdo = null;
    return intval($res[0]["exist"]);
}

function add_hyphen($phone)
{
    $phone = preg_replace("/[^0-9]/", "", $phone);    // 숫자 이외 제거
    if (substr($phone,0,2)=='02')
        return preg_replace("/([0-9]{2})([0-9]{3,4})([0-9]{4})$/", "\\1-\\2-\\3", $phone);
    else if (strlen($phone)=='8' && (substr($phone,0,2)=='15' || substr($phone,0,2)=='16' || substr($phone,0,2)=='18'))
        // 지능망 번호이면
        return preg_replace("/([0-9]{4})([0-9]{4})$/", "\\1-\\2", $phone);
    else
        return preg_replace("/([0-9]{3})([0-9]{3,4})([0-9]{4})$/", "\\1-\\2-\\3", $phone);
}


function myPage($usernum)
{
    $pdo = pdoSqlConnect();
    $query = "select name, writing, email, phone, ( SELECT DATE_FORMAT(signuptime, '%Y.%m.%d')) signuptime  from guest where usernum = ?;";
    $st = $pdo->prepare($query);
    $st -> execute([$usernum]);
    $st->setFetchMode(PDO::FETCH_ASSOC);
    $res = $st->fetchAll();
    $st = null;
    $pdo = null;
    return $res;
}

function patchMypage($usernum, $name, $writing, $email, $phone)
{
//    echo "$usernum";
//    echo "$name, $writing, $email", $phone;
    $pdo = pdoSqlConnect();
    $query = "UPDATE guest SET name = ?, writing  = ?,  email = ?, phone = ? WHERE usernum = ?;";
    $st = $pdo->prepare($query);
//    echo "$query";
    $st->execute([$name, $writing, $email, $phone,$usernum]);
    $st = null;
    $pdo = null;
}

//READ
function test()
{
    $pdo = pdoSqlConnect();
    $query = "SELECT * FROM TEST_TB;";

    $st = $pdo->prepare($query);
    //    $st->execute([$param,$param]);
    $st->execute();
    $st->setFetchMode(PDO::FETCH_ASSOC);
    $res = $st->fetchAll();

    $st = null;
    $pdo = null;

    return $res;
}

//READ
function testDetail($testNo)
{
    $pdo = pdoSqlConnect();
    $query = "SELECT * FROM TEST_TB WHERE no = ?;";

    $st = $pdo->prepare($query);
    $st->execute([$testNo]);
    //    $st->execute();
    $st->setFetchMode(PDO::FETCH_ASSOC);
    $res = $st->fetchAll();

    $st = null;
    $pdo = null;

    return $res[0];
}


function testPost($name)
{
    $pdo = pdoSqlConnect();
    $query = "INSERT INTO TEST_TB (name) VALUES (?);";

    $st = $pdo->prepare($query);
    $st->execute([$name]);

    $st = null;
    $pdo = null;

}

// CREATE
//    function addMaintenance($message){
//        $pdo = pdoSqlConnect();
//        $query = "INSERT INTO MAINTENANCE (MESSAGE) VALUES (?);";
//
//        $st = $pdo->prepare($query);
//        $st->execute([$message]);
//
//        $st = null;
//        $pdo = null;
//
//    }


// UPDATE
//    function updateMaintenanceStatus($message, $status, $no){
//        $pdo = pdoSqlConnect();
//        $query = "UPDATE MAINTENANCE
//                        SET MESSAGE = ?,
//                            STATUS  = ?
//                        WHERE NO = ?";
//
//        $st = $pdo->prepare($query);
//        $st->execute([$message, $status, $no]);
//        $st = null;
//        $pdo = null;
//    }

// RETURN BOOLEAN
//    function isRedundantEmail($email){
//        $pdo = pdoSqlConnect();
//        $query = "SELECT EXISTS(SELECT * FROM USER_TB WHERE EMAIL= ?) AS exist;";
//
//
//        $st = $pdo->prepare($query);
//        //    $st->execute([$param,$param]);
//        $st->execute([$email]);
//        $st->setFetchMode(PDO::FETCH_ASSOC);
//        $res = $st->fetchAll();
//
//        $st=null;$pdo = null;
//
//        return intval($res[0]["exist"]);
//
//    }

function convert_to_num($userid)
{
    $pdo = pdoSqlConnect();
    $query = "SELECT usernum FROM guest WHERE userid = ?;";
    $st = $pdo->prepare($query);
    $st -> execute([$userid]);
    $st->setFetchMode(PDO::FETCH_ASSOC);
    $res = $st->fetchAll();
    $st=null;
    $pdo = null;

    return intval($res[0]["usernum"]);
}

function isValidJWToken($userid, $userpw)
{

    $pdo = pdoSqlConnect();
//        echo "현재 로그인한 유저 아이디: $userid";
//        echo "pw : $userpw";
    $query = "SELECT EXISTS(SELECT * FROM guest WHERE userid = ? and userpw = ?) AS exist";
//        echo $query;
    $st = $pdo->prepare($query);
    //    $st->execute([$param,$param]);
    $st->execute([$userid, $userpw]);
    $st->setFetchMode(PDO::FETCH_ASSOC);
    $res = $st->fetchAll();

    $st=null;
    $pdo = null;

    return array("intval"=>intval($res[0]["exist"]), "userid"=>$userid);
}

