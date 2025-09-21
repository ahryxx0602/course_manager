<?php
if (!defined('_ROOT_PATH')) {
    die('Truy cập không hợp lệ!');
}

function layout($layoutName, $data = [])
{
    if (file_exists(_PATH_URL_TEMPLATES . '/layouts/' . $layoutName . '.php')) {
        require_once _PATH_URL_TEMPLATES . '/layouts/' . $layoutName . '.php';
    }
}

// Hàm gửi mail (php mailer)
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;


function senMail($emailTo, $subject, $body)
{
    $mail = new PHPMailer(true);

    try {
        //Server settings
        $mail->SMTPDebug = SMTP::DEBUG_OFF;                      //Enable verbose debug output
        $mail->isSMTP();                                            //Send using SMTP
        $mail->Host       = 'smtp.gmail.com';                     //Set the SMTP server to send through
        $mail->SMTPAuth   = true;                                   //Enable SMTP authentication
        $mail->Username   = 'redcream2004@gmail.com';                     //SMTP username
        $mail->Password   = 'fycgpbkdsivhlvob';                               //SMTP password
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS;            //Enable implicit TLS encryption
        $mail->Port       = 465;                                    //TCP port to connect to; use 587 if you have set `SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS`

        //Recipients
        $mail->setFrom('redcream2004@gmail.com', 'Ahryxx Course');
        $mail->addAddress($emailTo);     //Add a recipient

        //Content
        $mail->CharSet = 'UTF-8';
        $mail->isHTML(true);                                  //Set email format to HTML
        $mail->Subject = $subject;
        $mail->Body    = $body;

        $mail->SMTPOptions = array(
            'ssl' => [
                'verify_peer' => true,
                'verify_depth' => 3,
                'allow_self_signed' => true,
            ],
        );

        return $mail->send();
    } catch (Exception $e) {
        echo "Gửi thất bại: {$mail->ErrorInfo}";
    }
}

// Kiểm tra phương thức POST
function isPOST()
{
    if ($_SERVER['REQUEST_METHOD'] == 'POST') {
        return true;
    }
    return false;
}

// Kiểm tra phương thức GET
function isGET()
{
    if ($_SERVER['REQUEST_METHOD'] == 'GET') {
        return true;
    }
    return false;
}

//Lọc dữ liệu
function filterData($method = '')
{
    $filterArr = [];
    if (empty($method)) {
        if (isGET()) {
            if (!empty($_GET)) {
                foreach ($_GET as $key => $value) {
                    $key = strip_tags($key);
                    if (is_array($value)) {
                        $filterArr[$key] = filter_var($_GET[$key], FILTER_SANITIZE_SPECIAL_CHARS, FILTER_REQUIRE_ARRAY);
                    } else {
                        $filterArr[$key] = filter_var($_GET[$key], FILTER_SANITIZE_SPECIAL_CHARS);
                    }
                }
            }
        }
        if (isPOST()) {
            if (!empty($_POST)) {
                if (!empty($_POST)) {
                    foreach ($_POST as $key => $value) {
                        $key = strip_tags($key);
                        if (is_array($value)) {
                            $filterArr[$key] = filter_input(INPUT_POST, $key, FILTER_SANITIZE_SPECIAL_CHARS, FILTER_REQUIRE_ARRAY);
                        } else {
                            $filterArr[$key] = filter_input(INPUT_POST, $key, FILTER_SANITIZE_SPECIAL_CHARS);
                        }
                    }
                }
            }
        }
    } else {
        if ($method == 'get') {
            if (!empty($_GET)) {
                foreach ($_GET as $key => $value) {
                    $key = strip_tags($key);
                    if (is_array($value)) {
                        $filterArr[$key] = filter_var($_GET[$key], FILTER_SANITIZE_SPECIAL_CHARS, FILTER_REQUIRE_ARRAY);
                    } else {
                        $filterArr[$key] = filter_var($_GET[$key], FILTER_SANITIZE_SPECIAL_CHARS);
                    }
                }
            }
        } else if ($method == 'post') {
            if (!empty($_POST)) {
                foreach ($_POST as $key => $value) {
                    $key = strip_tags($key);
                    if (is_array($value)) {
                        $filterArr[$key] = filter_input(INPUT_POST, $key, FILTER_SANITIZE_SPECIAL_CHARS, FILTER_REQUIRE_ARRAY);
                    } else {
                        $filterArr[$key] = filter_input(INPUT_POST, $key, FILTER_SANITIZE_SPECIAL_CHARS);
                    }
                }
            }
        }
    }
    return $filterArr;
}

function validateEmail($email)
{
    if (!empty($email)) {
        $checkEmail = filter_var($email, FILTER_VALIDATE_EMAIL);
    }
    return $checkEmail;
}

function validateInt($number)
{
    if (!empty($number)) {
        $checkNumber = filter_var($number, FILTER_VALIDATE_INT);
    }
    return $checkNumber;
}

// validate phone 0123456789
function isPhone($phone)
{
    $phoneFirst = false;
    if ($phone[0] == '0') {
        $phoneFirst = true;
        $phone = substr($phone, 1);
    }
    $checkPhone = false;
    if (validateInt($phone)) {
        $checkPhone = true;
    }
    if ($phoneFirst && $checkPhone) {
        return true;
    }

    return false;
}

//Thông báo lỗi

function getMessage($msg, $type = 'success')
{
    echo '<div class="announce-message alert alert-' . $type . '">';
    echo $msg;
    echo '</div>';
}

//Hiển thị lỗi
function formError($errors, $fieldName)
{
    return (!empty($errors[$fieldName])) ? '<div class="error">' . reset($errors[$fieldName]) . '</div>' : false;
}

function oldData($oldData, $fieldName)
{
    return (!empty($oldData[$fieldName]) ? $oldData[$fieldName] : null);
}

//Hàm điều hướng

function redirect($path, $pathFull = false)
{
    if ($pathFull) {
        header("location: $path");
        exit();
    } else {
        $url = _HOST_URL . $path;
        header("location: $url");
        exit();
    }
}

//Check login

function isLogin()
{
    $checkLogin = false;
    $tokenLogin = getSessionFlash('token_login');
    $checkToken = getOne("SELECT * FROM token_login WHERE token = '$tokenLogin'");
    if (!empty($checkToken)) {
        $checkLogin = true;
    } else {
        removeSession('token_login');
    }
    return $checkLogin;
}
