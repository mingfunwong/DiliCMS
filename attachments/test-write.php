<?php

$file = './_test.temp';

file_put_contents($file, 'Write Success!');
if ($content = file_get_contents($file)) {
	echo $content;
} else {
	echo "Write Fail!";
}
unlink($file);