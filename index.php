<?php
spl_autoload_register(function ($ns) {
    $path = str_replace('\\', '/', __DIR__ . '/Classes/' . $ns) . '.php';

    if (file_exists($path))
        include_once $path . '';
}, false, true);

$profile_file = "profile.json";
/** @var \MH828\Aparat\Profile $profile */
$profile = null;
$ap = new \MH828\Aparat\AparatAPI();

if (file_exists($profile_file)) {
    $profile = \MH828\Aparat\Profile::newInstance(json_decode(file_get_contents($profile_file)));
} else {
    $profile = $ap->Login('mh828', '2#FdutY#unpbF&e');
    file_put_contents($profile_file, json_encode($profile));
}




/*$categories = $ap->Categories();

if (!empty($_POST)) {
    $fileInput = $ap->UploadForm($profile);
    $res = $ap->uploadFile($fileInput, $_FILES['video'], $_POST['title'], $_POST['category']);
    var_dump($res);
}*/



header('Content-Type: application/json; encode=utf8');
echo json_encode($ap->VideoBySearch('فان'));


return;
?>
<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Aparat API</title>
</head>
<body>

<form method="post" enctype="multipart/form-data">

    <input type="file" name="video" required/>
    <input type="text" name="title" value="<?php echo isset($_POST['title']) ? $_POST['title'] : '' ?>" required/>

    <select required name="category">
        <option value="">-- انتخاب طبقه بندی --</option>

        <?php foreach ($categories as $cat) {
            ?>
            <option <?php echo (isset($_POST['category']) && $cat->id == $_POST['category']) ? 'selected' : '' ?>
                    value="<?php echo $cat->id ?>"><?php echo $cat->name ?></option>
            <?php
        } ?>
    </select>

    <input type="submit"/>
</form>


</body>
</html>
