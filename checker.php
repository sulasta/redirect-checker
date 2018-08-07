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
        CURLOPT_FOLLOWLOCATION => false,
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

$url = isset($_GET['url']) ? trim($_GET['url']) : '';
if (!empty($url)) {
    $result = get_web_page($url);
    echo '<pre>' . (isset($result['info']) ? print_r($result['info'], true) : print_r($result, true)) . '</pre>';
    if (!empty($result['info']['redirect_url'])) {
        $result2 = get_web_page($result['info']['redirect_url']);
        echo '<pre>' . (isset($result2['info']) ? print_r($result2['info'], true) : print_r($result2, true)) . '</pre>';
    }
    exit;
}

$url = isset($_POST['url']) ? trim($_POST['url']) : '';

if (empty($url)) {
    show_result(['error' => true, 'message' => 'URL не определен']);
}

$redirects = [];

$try_count = 0;
while ($try_count < 10) {

    $result = get_web_page($url);

    // got curl error
    if (isset($result['error'])) {
        show_result(['error' => true, 'message' => $result['error']]);
    }

    // got some wrong
    if (!isset($result['info']['http_code'])) {
        show_result(['error' => true, 'message' => 'Неизвестный ответ сервера']);
    }

    // got redirect
    if (!empty($result['info']['redirect_url'])) {
        $redirects[] = [$result['info']['http_code'], $result['info']['redirect_url']];
        $url = $result['info']['redirect_url'];
        continue;
    }

    show_result([
        'error' => false,
        'code' => (int)$result['info']['http_code'],
        'url' => $result['info']['url'],
        'redirects' => $redirects,
    ]);

    $try_count++;
}

if (count($redirects) > 0) {
    show_result(['error' => true, 'message' => 'Слишком много перенаправлений']);
}

show_result(['error' => true, 'message' => 'Неизвестная ошибка']);
