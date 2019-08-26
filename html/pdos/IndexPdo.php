<?php

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
