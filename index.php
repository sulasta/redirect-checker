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
    <link rel="stylesheet" href="assets/bootstrap.min.css">
</head>
<body>

<div class="container-fluid my-2">
    <h2>Redirect Checker</h2>

    <form method="post" action="process.php">
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
        <button type="submit" class="btn btn-primary px-3">Start</button>
    </form>

</div>

</body>
</html>
