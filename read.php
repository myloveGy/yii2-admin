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
            $index                = str_replace('.md', '', $file);
            $data['data'][$index] = file_get_contents($path);
        }
    }
}

file_put_contents('./api/content.json', json_encode($data, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT));

$json   = file_get_contents('./asset-manifest.json');
$values = array_values(json_decode($json, true));

$dir   = './';
$files = scandir($dir);

// 必须要删除的文件
foreach ($files as $file) {
    // 先排除特殊 和 不需要删除的 . 开头的 .md 结尾的
    if (
        in_array($file, ['.', '..', 'docs', 'api', 'read.php', '_config.yml', 'asset-manifest.json']) ||
        in_array('/yii2-admin/' . $file, $values) ||
        substr($file, 0, 1) === '.' ||
        substr($file, -3) === '.md'
    ) {
        continue;
    }

    unlink('./' . $file);
    echo "删除文件: {$file} \n";
}
