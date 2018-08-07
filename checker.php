<?php

function show_result($data)
{
    header('Content-Type: application/json; charset=UTF-8');
    echo json_encode($data);
    exit;
}

function get_web_page($url)
{
    $useragent = isset($_SERVER['HTTP_USER_AGENT']) ? $_SERVER['HTTP_USER_AGENT'] : 'RedirectChecker/1.0';
    $options = [
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_HEADER => false,
        CURLOPT_FOLLOWLOCATION => true,
        CURLOPT_USERAGENT => $useragent,
        CURLOPT_AUTOREFERER => true,
        CURLOPT_CONNECTTIMEOUT => 60,
        CURLOPT_TIMEOUT => 60,
        CURLOPT_MAXREDIRS => 10,
        CURLOPT_FRESH_CONNECT => true,
        CURLOPT_SSL_VERIFYPEER => true,
        CURLOPT_SSL_VERIFYHOST => 2,
        CURLOPT_CAINFO => __DIR__ . '/cacert.pem',
    ];
    $ch = curl_init($url);
    curl_setopt_array($ch, $options);
    $content = curl_exec($ch);

    $err = curl_errno($ch);
    $err_msg = curl_error($ch);

    $info = curl_getinfo($ch);
    curl_close($ch);

    if ($err) {
        return [
            'error' => $err_msg,
        ];
    }

    return [
        'content' => $content,
        'info' => $info,
    ];
}

$url = isset($_POST['url']) ? trim($_POST['url']) : '';

if (empty($url)) {
    show_result(['error' => true, 'message' => 'URL не определен']);
}

$result = get_web_page($url);

if (isset($result['error'])) {
    show_result(['error' => true, 'message' => $result['error']]);
}

if (isset($result['info'])) {
    if (isset($result['info']['http_code'])) {
        if ($result['info']['http_code'] != 200) {
            show_result(['error' => true, 'message' => 'Код ответа: ' . $result['info']['http_code'], 'url' => $result['info']['url']]);
        } else {
            show_result(['error' => false, 'url' => $result['info']['url']]);
        }
    } else {
        show_result(['error' => true, 'message' => 'Неизвестный ответ сервера']);
    }
}

show_result(['error' => true, 'message' => 'Неизвестная ошибка']);
