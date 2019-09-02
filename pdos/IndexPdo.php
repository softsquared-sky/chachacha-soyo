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

function emailcheckGuest($email)
{
    $pdo = pdoSqlConnect();
    $query = "select exists(SELECT * FROM guest WHERE email = ?)as exist;";
    $st = $pdo->prepare($query);

    $st->execute([$email]);
    $st->setFetchMode(PDO::FETCH_ASSOC);
    $res = $st->fetchAll();

    $st=null;
    $pdo = null;

    return intval($res[0]["exist"]);
}


function idcheckGuest($userid)
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


function idcheckBoss($userid)
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

function addHyphen($phone)
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
    return $res[0];
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


function strNomatter($speople, $strKind, $addedQuerykindreuslt)
{
    $pdo = pdoSqlConnect();
    $query = "SELECT storename, mode, storewriting, imageurl FROM store WHERE `people` = :speople AND `kind` LIKE (:Kind)";
    if(!empty($strKind))
    {
        $query = $query.$addedQuerykindreuslt;
    }
//    echo "$query";
    $st = $pdo->prepare($query);
    $st->bindParam(':speople' , $speople, PDO::PARAM_INT);
    foreach ($strKind as $value)
    {
        $st->bindValue(':Kind', $value, PDO::PARAM_STR );
//        echo "$value";
    }

    $st->execute();
    $st->setFetchMode(PDO::FETCH_ASSOC);
    $res = $st->fetchAll();
    $st = null;
    $pdo = null;
//
    return $res;
}

function  getStore($speople, $strKind, $addedQuerykindreuslt, $strMode, $addedQuerymoderesult)
{

    $pdo = pdoSqlConnect();
    $query = "SELECT storename, mode, storewriting, imageurl FROM store WHERE `people` = :speople AND `kind` LIKE (:Kind) AND `mode` LIKE (:Mode)";
    if(!empty($strKind))
    {
        $query = $query.$addedQuerykindreuslt;
    }
    $st = $pdo->prepare($query);
    $st->bindParam(':speople' , $speople, PDO::PARAM_INT);
    foreach ($strKind as $value)
    {
        $st->bindValue(':Kind', $value, PDO::PARAM_STR );
    }
    foreach ($strMode as $value)
    {
        $st->bindValue(':Mode', $value, PDO::PARAM_STR );
    }

    $st->execute();
    $st->setFetchMode(PDO::FETCH_ASSOC);
    $res = $st->fetchAll();
    $st = null;
    $pdo = null;
//
    return $res;
}

function chaCheck($storenum, $usernum)
{
    $pdo = pdoSqlConnect();
    $query = "select deletenum from mychachacha where storenum = ? and usernum = ?";

    $st = $pdo->prepare($query);
    $st -> execute([$storenum, $usernum]);
    $st->setFetchMode(PDO::FETCH_ASSOC);
    $res = $st->fetchAll();
    $st = null;
    $pdo = null;
    return intval($res[0]["deletenum"]);
}

function mychachacha($chanum, $storenum, $usernum, $delete)
{
    $delete = 0;
    $pdo = pdoSqlConnect();
    $query = "INSERT INTO mychachacha (chanum, storenum, usernum, deletenum) VALUES (?,?,?,?);";
//    echo "$query";

    $st = $pdo->prepare($query);
    $st->execute([$chanum, $storenum, $usernum, $delete]);

    $st = null;
    $pdo = null;
}

function getcha($usernum)
{
    $deletenum = 0;

    $pdo = pdoSqlConnect();
    $query = "select chanum, storename, imageurl from mychachacha inner join store on mychachacha.storenum = store.storenum where usernum = ?  and deletenum = ?;";

    $st = $pdo->prepare($query);
    $st -> execute([$usernum, $deletenum]);
    $st->setFetchMode(PDO::FETCH_ASSOC);
    $res = $st->fetchAll();
    $st = null;
    $pdo = null;

    return $res;

}

function chaExist($chanum)
{
    $pdo = pdoSqlConnect();
    $query = "select deletenum from mychachacha where  chanum = ?;";

    $st = $pdo->prepare($query);
    $st -> execute([$chanum]);
    $st->setFetchMode(PDO::FETCH_ASSOC);
    $res = $st->fetchAll();
    $st = null;
    $pdo = null;

    return intval($res[0]["deletenum"]);
}

function detailCha($chanum)
{
    $pdo = pdoSqlConnect();
    $query = "";

    $st = $pdo->prepare($query);
    $st -> execute([$chanum]);
    $st->setFetchMode(PDO::FETCH_ASSOC);
    $res = $st->fetchAll();
    $st = null;
    $pdo = null;

    return intval($res[0]);
}

function deleteCha($chanum)
{
    $deletenum = 1;
    $pdo = pdoSqlConnect();
    $query = "UPDATE mychachacha  SET deletenum  = ?  WHERE chanum = ?;";

    $st = $pdo->prepare($query);
    $st -> execute([$deletenum,$chanum]);

    $st = null;
    $pdo = null;


}

function  myReview($usernum)
{
//    echo "$usernum";
    $pdo = pdoSqlConnect();

    $query = "select storename, reviewstore.star,address, reviewstore.text   from (SELECT storenum, text ,star FROM review where usernum = ?) reviewstore inner join store on reviewstore.storenum =  store.storenum";
//    echo "$query";
    $st = $pdo->prepare($query);
    //    $st->execute([$param,$param]);
    $st->execute([$usernum]);
    $st->setFetchMode(PDO::FETCH_ASSOC);
    $res = $st->fetchAll();

    $st = null;
    $pdo = null;

    return $res;
}

function mybookMark($usernum)
{
    $pdo = pdoSqlConnect();
    $query = "select storename,mode, storewriting, imageurl from (SELECT storenum FROM bookmark where usernum = ?) resultstore inner join store on  resultstore.storenum = store.storenum;";
    $st = $pdo->prepare($query);
    //    $st->execute([$param,$param]);
    $st->execute([$usernum]);
    $st->setFetchMode(PDO::FETCH_ASSOC);
    $res = $st->fetchAll();

    $st = null;
    $pdo = null;

    return $res;
}

function storeDetail($storenum)
{
    $pdo = pdoSqlConnect();
    $query = "select storename, mode, storewriting, address, opentime, closstime, imageurl, phone from store where storenum = ?;";
    $st = $pdo->prepare($query);
    //    $st->execute([$param,$param]);
    $st->execute([$storenum]);
    $st->setFetchMode(PDO::FETCH_ASSOC);
    $res = $st->fetchAll();

    $st = null;
    $pdo = null;

    $phone = $res[0]['phone'];
    $phone = addHyphen($phone);
    $res[0]['phone'] = $phone;

    return $res;
}

function storeReview($storenum)
{
//    $storenum2 = $storenum;
    $pdo = pdoSqlConnect();
    $query = "select distinct count(*) as reviewcount from review where storenum = ?;";
//    echo "$query";
    $st = $pdo->prepare($query);
    //    $st->execute([$param,$param]);
    $st->execute([$storenum]);
    $st->setFetchMode(PDO::FETCH_ASSOC);
    $res = $st->fetchAll();

    $st = null;
    $pdo = null;


    $pdo = pdoSqlConnect();
    $query = "select name,text, star from guest inner join review on guest.usernum = review.usernum where storenum = ?;";
//    echo "$query";
    $st = $pdo->prepare($query);
    //    $st->execute([$param,$param]);
    $st->execute([$storenum]);
    $st->setFetchMode(PDO::FETCH_ASSOC);
    $res2 = $st->fetchAll();

    $st = null;
    $pdo = null;

//    echo json_encode($res);
//    echo json_encode($res2);

    return array("reviewcount" =>  $res, "review" => $res2);
}


function storeMenu($storenum)
{
    $pdo = pdoSqlConnect();
    $query = " select menuname, menuprice from menu where storenum = ? and kindnum = 0;";
//    echo "$query";
    $st = $pdo->prepare($query);
    //    $st->execute([$param,$param]);
    $st->execute([$storenum]);
    $st->setFetchMode(PDO::FETCH_ASSOC);
    $res = $st->fetchAll();

    $st = null;
    $pdo = null;

     $pdo = pdoSqlConnect();
    $query = " select menuname, menuprice from menu where storenum = ? and kindnum = 1;";
//    echo "$query";
    $st = $pdo->prepare($query);
    //    $st->execute([$param,$param]);
    $st->execute([$storenum]);
    $st->setFetchMode(PDO::FETCH_ASSOC);
    $res2 = $st->fetchAll();

    $st = null;
    $pdo = null;

    return array('food' =>$res , "drink" => $res2);
}
//READ


//READ
function testDetail($usernum)
{
    echo "$usernum";
    $pdo = pdoSqlConnect();
    $query = "SELECT * FROM guest WHERE usernum = ?;";

    $st = $pdo->prepare($query);
    $st->execute([$usernum]);
    //    $st->execute();
    $st->setFetchMode(PDO::FETCH_ASSOC);
    $res = $st->fetchAll();

    $st = null;
    $pdo = null;

    return $res;
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

function isIdexist($userId)
{
    $pdo = pdoSqlConnect();
    $query = "select exists (SELECT * FROM guest WHERE userid = ?)as exist;";
//    echo $query;
    $st = $pdo->prepare($query);
    $st -> execute([$userId]);
    $st->setFetchMode(PDO::FETCH_ASSOC);
    $res = $st->fetchAll();
    $st=null;
    $pdo = null;

    return intval($res[0]["exist"]);
}

function isStoreexist($storenum)
{
    $pdo = pdoSqlConnect();
    $query = "select exists (SELECT * FROM store WHERE storenum = ?)as exist;";
//    echo $query;
    $st = $pdo->prepare($query);
    $st -> execute([$storenum]);
    $st->setFetchMode(PDO::FETCH_ASSOC);
    $res = $st->fetchAll();
    $st=null;
    $pdo = null;

    return intval($res[0]["exist"]);
}

function isChaexist($chanum, $usernum)
{
    $pdo = pdoSqlConnect();
    $query = "select exists (SELECT * FROM mychachacha WHERE chanum = ? and usernum = ?)as exist;";
//    echo $query;
    $st = $pdo->prepare($query);
    $st -> execute([$chanum, $usernum]);
    $st->setFetchMode(PDO::FETCH_ASSOC);
    $res = $st->fetchAll();
    $st=null;
    $pdo = null;

    return intval($res[0]["exist"]);
}

function convert_to_num($userId)
{
//    echo "$userId";
    $pdo = pdoSqlConnect();
    $query = "SELECT usernum FROM guest WHERE userid = ?;";
//    echo $query;
    $st = $pdo->prepare($query);
    $st -> execute([$userId]);
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

