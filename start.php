<?php

namespace LimitedInvitations;

const PLUGIN_ID = 'limited_invitations';
const PLUGIN_VERSION = 20170202;
const PLUGIN_DIR = __DIR__;

elgg_register_event_handler('init', 'system', __NAMESPACE__ . '\\init');

function init() {
	
	elgg_extend_view('forms/invitefriends/invite', 'limited_invitations/invitation_count', 200);
	
	// replace the default action with our own
	elgg_register_action('invitefriends/invite', PLUGIN_DIR . '/actions/invite.php');
	
	elgg_register_action('limited_invitations/reset', PLUGIN_DIR . '/actions/reset.php', 'admin');
	elgg_register_action('limited_invitations/resend', PLUGIN_DIR . '/actions/resend.php');
	
	// register plugin hooks
	elgg_register_plugin_hook_handler('view_vars', 'forms/invitefriends/invite', __NAMESPACE__ . '\\Hooks::inviteFormVars');
	elgg_register_plugin_hook_handler('register', 'menu:entity', __NAMESPACE__ . '\\Hooks::entityMenuRegister');
	elgg_register_plugin_hook_handler('route', 'register', __NAMESPACE__ . '\\Hooks::registerRouter');
	elgg_register_plugin_hook_handler('action', 'register', __NAMESPACE__ . '\\Hooks::registerActionCheck');
	elgg_register_plugin_hook_handler('register', 'user', __NAMESPACE__ . '\\Hooks::registerUserComplete', 0);
	
	elgg_register_page_handler('limited_invitations', __NAMESPACE__ . '\\pagehandler');
}

/**
 * Handle our pages
 * 
 * @param type $page
 */
function pagehandler($page) {
	if (!is_array($page)) {
		return false;
	}
	
	if (!isset($page[0])) {
		return false;
	}
	
	$content = elgg_view_resource('limited_invitations/' . $page[0]);
	if ($content) {
		echo $content;
	}
	
	return $content ? true : false;
}