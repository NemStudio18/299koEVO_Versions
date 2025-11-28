<?php

/**
 * @copyright (C) 2025, 299KoEVO, based on code (2010-2021) 99ko https://github.com/99kocms/
 * @license https://www.gnu.org/licenses/gpl-3.0.en.html GPLv3
 * @author Maxence Cauderlier <mx.koder@gmail.com>
 * 
 * @package 299KoEVO https://github.com/NemStudio18/299koEVO
 */

// Need to be started in CLI, in repo_version folder
// php execUpdate.php
// Create a new version for auto update
// 
// Prerequisites:
// - config.php must be configured with correct KOPATH, version, and commit SHA1s
// - KOPATH must point to a local clone of the main 299KoEVO repository

require 'config.php';

chdir(KOPATH);

$result = null;
exec('git diff --name-status ' . $commitLastVersion . ' ' . $commitFutureVersion, $result);

chdir(VERSIONSPATH);
@mkdir('update_to/' . $version);

$deleted = [];
$json = [];

foreach ($result as $r) {
    $line = explode("\t", $r);
    if (count($line) === 2) {
        $json[$line[0]][] = $line[1];
        if ($line[0] === 'D') {
            $deleted[] = $line[1];
        }
    } elseif (count($line) === 3) {
        if (substr($line[0],0,1) === "R") {
            // Rename detected
            $deleted[] = $line[1];
            $json["D"][] = $line[1];
            $json["A"][] = $line[2];
        }
    }
}

if (!is_dir('core/' . $version)) {
    mkdir('core/' . $version, 0777, true);
}

file_put_contents('core/' . $version . '/files.json', json_encode($json, JSON_PRETTY_PRINT));
file_put_contents('core/' . $version . '/deleted.json', json_encode($deleted, JSON_PRETTY_PRINT));
