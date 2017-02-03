<?php

namespace LimitedInvitations;

$version = elgg_get_plugin_setting('version', PLUGIN_ID);
if (!$version) {
	elgg_set_plugin_setting('version', PLUGIN_VERSION, PLUGIN_ID);
}

// the invitations_reset timestamp is used to count invitations since the last reset
$invitations_reset = elgg_get_plugin_setting('invitations_reset', PLUGIN_ID);
if (!$invitations_reset) {
	elgg_set_plugin_setting('invitations_reset', time(), PLUGIN_ID);
}

$invite_only = elgg_get_plugin_setting('invite_only', PLUGIN_ID);
if (!$invite_only) {
	elgg_set_plugin_setting('invite_only', 'no', PLUGIN_ID);
}


// handle invitations in our own class
if (get_subtype_id('object', 'invitation_sent')) {
	update_subtype('object', 'invitation_sent', __NAMESPACE__ . '\\Invitation');
} else {
	add_subtype('object', 'invitation_sent', __NAMESPACE__ . '\\Invitation');
}