
<form method="post" enctype="multipart/form-data" action="">
    <input type="file" name="file">
    <input type="submit" value="Upload">
</form>

<?php


$filename = "_" . time() . ".temp";

if ($_FILES['file']['tmp_name']) {

	//echo '<pre>';print_r($_FILES);echo '</pre>';
    $return = move_uploaded_file($_FILES['file']['tmp_name'], $filename);
    //var_dump($return);
    if (file_exists($filename)) {
    	echo 'Upload Success!';
		unlink($filename);
    } else {
		echo "Upload Fail!";
    }

}