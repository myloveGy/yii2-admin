<?php
/**
 * 执行命令：php ./read.php --read=true --delete=true
 *
 * @param bool read 是否重新读取
 * @param bool delete 是否删除多余文件
 */

$params = getopt('', ['read:', 'delete:']);

// 重新读取文件
if (isset($params['read']) && in_array($params['read'], ['true', '1'])) {
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
    echo date('Y-m-d H:i:s') . " 读取文件成功，写入 ./api/content.json \n";
}

if (isset($params['delete']) && in_array($params['delete'], ['true', '1'])) {
    $json   = file_get_contents('./asset-manifest.json');
    $values = array_values(json_decode($json, true));
    $dir    = './';
    $files  = scandir($dir);

    $number = 0;
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
        $number++;
    }

    echo date('Y-m-d H:i:s') . " 删除文件成功，删除文件数: {$number} \n";
}

