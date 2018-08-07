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
    <h2>Redirect Checker</h2>

    <form method="post" action="process.php">
        <div class="form-group">
            <label for="url_prefix">URL-префикс</label>
            <input type="text" class="form-control" name="url_prefix" id="url_prefix" placeholder="http://example.com">
            <small class="form-text text-muted">Указать, если ссылки в списке относительные</small>
        </div>
        <div class="form-group">
            <label for="url_list">Список URL</label>
            <textarea class="form-control" name="url_list" id="url_list" rows="10" placeholder="/news/example.html"></textarea>
            <small class="form-text text-muted">По одному URL на строку<br>Будут обработаны только первые 100 строк</small>
        </div>
        <button type="submit" class="btn btn-primary">Отправить</button>
    </form>

</div>

</body>
</html>
