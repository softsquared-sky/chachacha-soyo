<?php

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


//READ
function guest($usernum, $userid, $userpw, $name, $age, $gender, $email, $signuptime)
{
    $pdo = pdoSqlConnect();
    $query = "INSERT INTO guest (usernum, userid, userpw, name, age, gender, email, signuptime) VALUES (?,?,?,?,?,?,?,?);";
//    echo "$query";
    $st = $pdo->prepare($query);
    $st->execute([$usernum, $userid, $userpw, $name, $age, $gender, $email, $signuptime]);

    $st = null;
    $pdo = null;
}

function boss($usernum, $userid, $userpw, $name, $phone, $signuptime)
{
    $pdo = pdoSqlConnect();
    $query = "INSERT INTO boss (usernum, userid, userpw, username, userphone, signuptime) VALUES (?,?,?,?,?,?);";
//    echo "$query";
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

function myPage($usernum)
{
    $pdo = pdoSqlConnect();
    $query = "select name, writing, email, ( SELECT DATE_FORMAT(signuptime, '%Y.%m.%d')) signuptime  from guest where usernum = ?;";
//    echo "$query";
    $st = $pdo->prepare($query);
    $st -> execute([$usernum]);
//        $st->execute();
    $st->setFetchMode(PDO::FETCH_ASSOC);
    $res = $st->fetchAll();

    $st = null;
    $pdo = null;

    return $res;

}

function guestPost()
{
    $pdo = pdoSqlConnect();
    $query = "INSERT INTO TEST_TB (name) VALUES (?);";

    $st = $pdo->prepare($query);
    $st->execute([]);

    $st = null;
    $pdo = null;

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
