<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <title>Redirect Checker</title>
    <link rel="apple-touch-icon" sizes="180x180" href="fav/apple-touch-icon.png">
    <link rel="icon" type="image/png" sizes="192x192" href="fav/android-chrome-192x192.png">
    <link rel="icon" type="image/png" sizes="32x32" href="fav/favicon-32x32.png">
    <link rel="icon" type="image/png" sizes="16x16" href="fav/favicon-16x16.png">
    <link rel="manifest" href="fav/site.webmanifest">
    <link rel="shortcut icon" href="fav/favicon.ico">
    <meta name="theme-color" content="#ffffff">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.1.3/css/bootstrap.min.css" integrity="sha512-GQGU0fMMi238uA+a/bdWJfpUGKUkBdgfFdgBm72SUQ6BeyWjoY/ton0tEjH+OSH9iP4Dfh+7HM0I9f5eR0L/4w==" crossorigin="anonymous" referrerpolicy="no-referrer">
</head>
<body>

<div class="container-fluid my-2">
    <h2>Redirect Checker</h2>

<?php if (!isset($_GET['process'])): ?>

    <form method="post" action="index.php?process">
        <div class="mb-3">
            <label for="url_prefix" class="form-label">Base URL</label>
            <input type="text" class="form-control" name="url_prefix" id="url_prefix" placeholder="http://example.com">
            <div class="form-text">Fill if URL list contains relative paths</div>
        </div>
        <div class="mb-3">
            <label for="url_list" class="form-label">URL list</label>
            <textarea class="form-control" name="url_list" id="url_list" rows="10" placeholder="/news/example.html
/news/example2.html
or
http://example.com/news/example.html
http://example.com/news/example2.html"></textarea>
            <div class="form-text">One URL per line<br>Only first 100 URLs will be checked</div>
        </div>
        <button type="submit" class="btn btn-primary">Start</button>
    </form>

<?php else:

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

    if (empty($urls)): ?>

        <p>Empty URL list.</p>

    <?php else: ?>

        <table class="table table-sm">
            <thead>
            <tr>
                <th>#</th>
                <th style="width: 49.5%">URL</th>
                <th style="width: 49.5%">Result</th>
            </tr>
            </thead>
            <tbody>
            <?php
            $count = 1;
            foreach ($urls as $url) {
                ?>
                <tr>
                    <td><?= $count++ ?></td>
                    <td><a href="<?= esc_html($url) ?>" target="_blank" rel="noreferrer"><?= esc_html($url) ?></a></td>
                    <td><div class="redirect-status" data-url="<?= esc_html($url) ?>"><em class="text-muted">Waiting</em></div></td>
                </tr>

                <?php
            }
            ?>
            </tbody>
        </table>

    <?php endif; ?>

    <p><a href="index.php" class="btn btn-secondary back-link">Back</a></p>

<?php endif; ?>

</div>

<?php if (isset($_GET['process'])): ?>

<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.0/jquery.min.js" integrity="sha512-894YE6QWD5I59HgZOGReFYm4dnWc1Qt5NtvYSaNcOP+u1T9qYdvdihz0PPSiiqn/+/3e7Jo4EaG7TubfWGUrMQ==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>

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
                $el.html('<em>Checking...</em>');
                $.post('checker.php', {"url": url}, function (data) {
                    $el.addClass('checked');
                    if (data.error) {
                        $el.html('<em class="text-danger">' + data.message + '</em>');
                    } else {
                        $el.html('');
                        if (data.redirects && data.redirects.length > 0) {
                            for (var i = 0; i < data.redirects.length; i++) {
                                $el.append('<small class="text-muted">' + data.redirects[i][0] + ' &rarr; ' + data.redirects[i][1] + '</small><br>');
                            }
                        }
                        if (data.url) {
                            var code_class = data.code && data.code === 200 ? '' : 'text-danger';
                            $el.append('<span class="' + code_class + '">' + data.code + '</span>: ' + '<a href="' + data.url + '" target="_blank" rel="noreferrer">' + data.url + '</em>');
                        }
                    }
                    checkUrl();
                }).fail(function () {
                    $el.addClass('checked').html('<em class="text-danger">Request error</em>');
                    checkUrl();
                });
            } else {
                $el.addClass('checked').html('<em class="text-danger">Undefined URL</em>');
            }
        }
    });
</script>

<?php endif; ?>

</body>
</html>
