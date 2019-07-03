<?php

$data = [
    'code' => 10000,
    'data' => [],
    'msg'  => 'ok',
];

$dir = './docs';

$array = scandir($dir);
if ($array) {
    foreach ($array as $file) {
        if (in_array($file, ['.', '..'])) {
            continue;
        }

        $str  = substr($file, -3);
        $path = $dir . '/' . $file;
        if (is_file($path) && $str == '.md') {
            $index = str_replace('.md', '', $file);
            $data['data'][$index] = file_get_contents($path);
        }
    }
}

file_put_contents('./api/content.json', json_encode($data, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT));