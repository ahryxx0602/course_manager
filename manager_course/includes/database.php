<?php
if(!defined('_ROOT_PATH')) {
    die('Truy cập không hợp lệ!');
}


// SELECT DỮ LIỆU

// LẤY NHIỀU BẢN GHI
function getAll($sql){
    global $conn;
    $stm = $conn -> prepare($sql);

    $stm -> execute();

    $result = $stm -> fetchAll(PDO::FETCH_ASSOC);
    return $result;
}
// LẤY 1 BẢN GHI
function getOne($sql){
    global $conn;
    $stm = $conn -> prepare($sql);

    $stm -> execute();

    $result = $stm -> fetch(PDO::FETCH_ASSOC);
    return $result;
}

//
function getRows($sql){
    global $conn;
    $stm = $conn -> prepare($sql);

    $stm -> execute();

    $rel = $stm -> rowCount();
    return $rel;
}

// INSERT DỮ LIỆU

function insertData($table, $data){
    /*
    $data = [
    'name' => 'Lập trình PHP',
    'slug'=> 'lap-trinh-php',
    ];
    */
    global $conn;

    $keys = array_keys($data); // Lấy ra các khóa của mảng
    $collumns = implode(',', $keys); // Chuyển mảng thành chuỗi, ngăn cách nhau bởi dấu ,
    $placeholders = ':' .implode(',:', $keys); // Tạo chuỗi các placeholder, ngăn cách nhau bởi dấu , và có dấu : ở đầu mỗi khóa

    $sql = "INSERT INTO $table($collumns) VALUES($placeholders)";

    $stm = $conn -> prepare($sql);

    $rel = $stm -> execute($data);

    return $rel;
}

//UPPDATE DỮ LIỆU

function updateData($table, $data, $condition =''){
    global $conn;
    $update ='';
    foreach($data as $key => $value){
    $update .= $key . '=:' .$key .',';
    }
    $update = trim($update, ',');
    if(!empty($condition)){
        $sql = "UPDATE $table SET $update WHERE $condition";
    } else {
        $sql = "UPDATE $table SET $update";
    }
    $tmp = $conn -> prepare($sql);

    //Thực thi câu lệnh
    $rel = $tmp -> execute($data);
    return $rel;
}

// DELETE

function deleteData($table, $condition=''){
    global $conn;

    if(!empty($condition)){
    $sql = "DELETE FROM $table WHERE $condition";
    } else {
        $sql = "DELETE FROM $table";
    }

    $stm = $conn -> prepare($sql);

    $rel = $stm -> execute();

    return $rel;
}

// Lấy id dữ liệu moiiws INSERT

function lastID(){
    global $conn;

    return $conn -> lastInsertId();
}