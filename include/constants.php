<?php
/**
 * Some usefull constants used around the code base
 */

// Folder used to store temporary stuff like cache or state files
// Any file in this folder should be deleted without impacting pouet
// Just making thinks slower
define('TMP_FOLDER', 'tmp/');

// File used to check if scene.org is up, if not pouet goes read-only
define('SCENE_ORG_CHECK_FILE', TMP_FOLDER.'IS_SCENE_ORG_UP');
