<?php

function esc_html($string)
{
    return htmlspecialchars($string, ENT_NOQUOTES, 'UTF-8');
}

$url_prefix = isset($_POST['url_prefix']) ? trim($_POST['url_prefix']) : '';
$url_list = isset($_POST['url_list']) ? trim($_POST['url_list']) : '';

$urls = [];

if (!empty($url_prefix) && !preg_match('/^https?:\/\//', $url_prefix)) {
    $url_prefix = '';
}

$url_list_array = explode("\n", $url_list);
foreach ($url_list_array as $url) {
    $url = trim($url);
    if (empty($url)) {
        continue;
    }
    if (!preg_match('/^https?:\/\//', $url)) {
        if (!empty($url_prefix)) {
            if (strpos($url, '/') === '') {
                $url = rtrim($url_prefix, '/') . $url;
            } else {
                $url = $url_prefix . $url;
            }
            $urls[$url] = true;
        }
    } else {
        $urls[$url] = true;
    }
}

$urls = array_keys($urls);
$urls = array_slice($urls, 0, 100);
?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <title>Redirect Checker</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.1.3/css/bootstrap.min.css">
</head>
<body>

<div class="container-fluid" style="margin-top: 10px;">
    <h1>Redirect Checker</h1>

    <?php
    if (empty($urls)) {
        ?>
        <p>Список URL пуст.</p>
        <?php
    } else {
        ?>
        <table class="table table-sm">
            <thead>
            <tr>
                <th style="width: 50%">URL</th>
                <th style="width: 50%">Результат</th>
            </tr>
            </thead>
            <tbody>
            <?php
            foreach ($urls as $url) {
                ?>
                <tr>
                    <td><a href="<?= esc_html($url) ?>" target="_blank" rel="noreferrer"><?= esc_html($url) ?></a></td>
                    <td><div class="redirect-status" data-url="<?= esc_html($url) ?>"><em class="text-muted">Ожидание</em></div></td>
                </tr>
                <?php
            }
            ?>
            </tbody>
        </table>
        <?php
    }
    ?>

    <p><a href="index.php" class="btn btn-secondary back-link">Назад</a></p>

</div>

<script src="https://code.jquery.com/jquery-3.3.1.min.js"></script>

<script>
    jQuery(function ($) {
        "use strict";

        checkUrl();

        function checkUrl() {
            var $el = $('.redirect-status:not(.checked)').eq(0);
            if (!$el.length) {
                $('.back-link').removeClass('btn-secondary').addClass('btn-success');
                return;
            }
            var url = $el.attr('data-url');
            if (url) {
                $el.html('<em>Проверка...</em>');
                $.post('checker.php', {"url": url}, function (data) {
                    $el.addClass('checked');
                    $el.html('');
                    if (data.url) {
                        $el.append('<a href="' + data.url + '" target="_blank" rel="noreferrer">' + data.url + '</em>');
                    }
                    if (data.error) {
                        if (data.url) {
                            $el.append('<br>');
                        }
                        $el.append('<em class="text-danger">' + data.message + '</em>');
                    }
                    checkUrl();
                }).fail(function () {
                    $el.addClass('checked').html('<em class="text-danger">Ошибка запроса</em>');
                    checkUrl();
                });
            } else {
                $el.addClass('checked').html('<em class="text-danger">URL не определен</em>');
            }
        }
    });
</script>

</body>
</html>
