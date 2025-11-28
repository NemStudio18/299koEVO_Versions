<?php

/**
 * @copyright (C) 2025, 299KoEVO, based on code (2010-2021) 99ko https://github.com/99kocms/
 * @license https://www.gnu.org/licenses/gpl-3.0.en.html GPLv3
 * @author Maxence Cauderlier <mx.koder@gmail.com>
 *
 * @package 299KoEVO https://github.com/NemStudio18/299koEVO
 */

define('ROOT', '../');

// Chemin vers le dépôt principal 299KoEVO (clone local)
// À adapter selon votre structure locale
define('KOPATH', ROOT . '299koEVO/');
// Chemin vers le dépôt des versions (ce dossier)
define('VERSIONSPATH', __DIR__ . '/');

// Version à créer (ex: 1.0.1)
$version = '1.0.1';

// SHA1 du commit de la dernière version (ex: 1.0.0)
$commitLastVersion = 'SHA1_DU_COMMIT_1.0.0';
// SHA1 du commit de la version à créer (ex: 1.0.1)
$commitFutureVersion = 'SHA1_DU_COMMIT_1.0.1';
