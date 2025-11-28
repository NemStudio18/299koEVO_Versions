<?php

/**
 * @copyright (C) 2025, 299Ko, based on code (2010-2021) 99ko https://github.com/99kocms/
 * @license https://www.gnu.org/licenses/gpl-3.0.en.html GPLv3
 * @author Maxence Cauderlier <mx.koder@gmail.com>
 * 
 * @package 299Ko https://github.com/299Ko/299ko
 */

// need to be started in CLI, in versions folder
// php execUpdate.php
// Create a new version for auto update

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
