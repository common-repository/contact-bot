<?php

// Load models
require_once MCB_PLUGIN_DIR . '/models/MCBUser.php';

// Load modules
require_once MCB_PLUGIN_DIR . '/modules/MCBNames.php';
require_once MCB_PLUGIN_DIR . '/modules/MCBSession.php';
require_once MCB_PLUGIN_DIR . '/modules/MCBUtils.php';
require_once MCB_PLUGIN_DIR . '/modules/MCBMessenger.php';
require_once MCB_PLUGIN_DIR . '/common/js_localization.php';

// Load the approriate files
if ( is_admin() ) {
	require_once MCB_PLUGIN_DIR . '/admin/admin.php';
} else {
	require_once MCB_PLUGIN_DIR . '/client/client.php';
}