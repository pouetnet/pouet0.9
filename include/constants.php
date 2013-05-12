<?php
/**
 * Some usefull constants used around the code base
 */

// Folder used to store temporary stuff like cache or state files
// Any file in this folder should be deleted without impacting pouet
// Just making thinks slower
define('TMP_FOLDER', 'tmp');

// File used to check if scene.org is up, if not pouet goes read-only
define('SCENE_ORG_CHECK_FILE', TMP_FOLDER.'/IS_SCENE_ORG_UP');

// File used by the deploy script to store the commit that is currently
// deployed
define('LOCAL_COMMIT_FILE', TMP_FOLDER.'/LOCAL_COMMIT');

// File used by the github web hook to store the commit that is gonna be
// deployed next
define('REMOTE_COMMIT_FILE', TMP_FOLDER.'/REMOTE_COMMIT');
