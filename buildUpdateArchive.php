<?php

/**
 * @copyright (C) 2025, 299Ko, based on code (2010-2021) 99ko https://github.com/99kocms/
 * @license https://www.gnu.org/licenses/gpl-3.0.en.html GPLv3
 * @author Maxence Cauderlier <mx.koder@gmail.com>
 *
 * @package 299Ko https://github.com/299Ko/299ko
 */

// need to be started in CLI, in versions folder
// php buildUpdateArchive.php
// Create an archive for manual update

require 'config.php';

function delTree(string $dir) {
    $files = array_diff(scandir($dir), ['.', '..']);
    foreach ($files as $file) {
        (is_dir($dir . '/' . $file)) ? delTree($dir . '/' . $file) : unlink($dir . '/' . $file);
    }
    return rmdir($dir);
}

chdir(KOPATH);

exec('git archive --output=' . VERSIONSPATH . 'changes.zip HEAD $(git diff --name-only ' .$commitLastVersion . ' ' . $commitFutureVersion . ' --diff-filter=ACMRTUXB)');

$zip = new ZipArchive;
if ($zip->open(VERSIONSPATH . 'changes.zip') === TRUE) {
    $zip->extractTo(VERSIONSPATH . 'update_to/' . $version . '/files/');
    $zip->close();
}

unlink(VERSIONSPATH . 'changes.zip');

$filename = 'update.zip';

chdir(VERSIONSPATH . 'update_to/' . $version . '/');
$folder = 'files/';
$ignoreRegex = [];

$data['dir'] = [];
$data['file'] = [];
$objects = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($folder), RecursiveIteratorIterator::SELF_FIRST);
foreach ($objects as $name => $object) {
    if ($object->isDir()) {
        $data['dir'][] = $name;
    } else {
        $data['file'][] = $name;
    }
}

$zip = new ZipArchive();
$zip->open($filename, ZipArchive::CREATE);

foreach ($data['dir'] as $dir) {
    $dir = trim($dir,'.');
    $dir = str_replace('\\', '/', $dir);
    $dir = trim($dir, '/');
    foreach ($ignoreRegex as $regex) {
        if (preg_match($regex, $dir)) {
            continue 2;
        }
    }
    $zip->addEmptyDir('update/' . $dir);
}
foreach ($data['file'] as $file) {
    $file = trim($file,'.');
    $file = str_replace('\\', '/', $file);
    $file = trim($file, '/');
    foreach ($ignoreRegex as $regex) {
        if (preg_match($regex, $file)) {
            continue 2;
        }
    }
    $zip->addFile($file, 'update/' . $file);
}

$filesToAdd = ['deleted.json', '_afterChangeFiles.php', '_beforeChangeFiles.php'];

chdir('../../');

foreach ($filesToAdd as $file) {
    if (is_file('core/' . $version . '/' . $file)) {
        $zip->addFile('core/' . $version . '/' . $file, 'update/' . $file);
    } else {
        echo '[WARNING] File not found : ' . $file . PHP_EOL;
    }
}
$zip->close();

delTree('update_to/' . $version . '/files');
