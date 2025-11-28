<?php

/**
 * @copyright (C) 2025, 299KoEVO, based on code (2010-2021) 99ko https://github.com/99kocms/
 * @license https://www.gnu.org/licenses/gpl-3.0.en.html GPLv3
 * @author Maxence Cauderlier <mx.koder@gmail.com>
 *
 * @package 299KoEVO https://github.com/NemStudio18/299koEVO
 */

// Need to be started in CLI, in repo_version folder
// php buildUpdateArchive.php
// Create an archive for manual update
//
// Prerequisites:
// - execUpdate.php must have been run first to generate files.json
// - config.php must be configured with correct KOPATH, version, and commit SHA1s

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
