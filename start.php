<?php

namespace LimitedInvitations;

const PLUGIN_ID = 'limited_invitations';
const PLUGIN_VERSION = 20170202;
const PLUGIN_DIR = __DIR__;

elgg_register_event_handler('init', 'system', __NAMESPACE__ . '\\init');

function init() {
	
	elgg_extend_view('forms/invitefriends/invite', 'limited_invitations/invitation_count', 200);
	elgg_extend_view('group_tools/invite/csv', 'limited_invitations/gt_warning', 200);
	elgg_extend_view('group_tools/invite/email', 'limited_invitations/gt_warning', 200);
	
	// replace the default action with our own
	elgg_register_action('invitefriends/invite', PLUGIN_DIR . '/actions/invite.php');
	
	elgg_register_action('limited_invitations/reset', PLUGIN_DIR . '/actions/reset.php', 'admin');
	elgg_register_action('limited_invitations/resend', PLUGIN_DIR . '/actions/resend.php');
	elgg_register_action('limited_invitations/grant_invitations', PLUGIN_DIR . '/actions/grant_invitations.php', 'admin');
	
	// register plugin hooks
	elgg_register_plugin_hook_handler('view_vars', 'forms/invitefriends/invite', __NAMESPACE__ . '\\Hooks::inviteFormVars');
	elgg_register_plugin_hook_handler('register', 'menu:entity', __NAMESPACE__ . '\\Hooks::entityMenuRegister');
	elgg_register_plugin_hook_handler('route', 'register', __NAMESPACE__ . '\\Hooks::registerRouter');
	elgg_register_plugin_hook_handler('action', 'register', __NAMESPACE__ . '\\Hooks::registerActionCheck');
	elgg_register_plugin_hook_handler('register', 'user', __NAMESPACE__ . '\\Hooks::registerUserComplete', 0);
	elgg_register_plugin_hook_handler('register', 'menu:user_hover', __NAMESPACE__ . '\\Hooks::userHoverMenuRegister');
	elgg_register_plugin_hook_handler('action', 'groups/invite', __NAMESPACE__ . '\\Hooks::groupsInviteCheck');
	elgg_register_plugin_hook_handler('register', 'menu:login', __NAMESPACE__ . '\\Hooks::loginMenuRegister');
	
	//hybridauth compatibility
	elgg_register_plugin_hook_handler('view_vars', 'forms/hybridauth/register', __NAMESPACE__ . '\\Hooks::hybridauthRegisterForm');
	elgg_register_plugin_hook_handler('action', 'hybridauth/register', __NAMESPACE__ . '\\Hooks::hybriauthRegisterAction');
	
	elgg_register_page_handler('limited_invitations', __NAMESPACE__ . '\\pagehandler');
	
	if (elgg_is_admin_logged_in()) {
		elgg_register_ajax_view('limited_invitations/grant_invitations');
	}
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