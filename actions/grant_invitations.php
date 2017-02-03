<?php

namespace LimitedInvitations;

$user = get_user(get_input('guid'));

if (!$user) {
	register_error(elgg_echo('limited_invitations:error:invalid:userguid'));
	forward(REFERER);
}

$invitations = (int) get_input('invites');

if ($invitations >= 0) {
	$user->limited_invitations_granted = $invitations;
}
else {
	$user->limited_invitations_granted = null; // reset to default
}

system_message(elgg_echo('limited_invitations:granted:success'));

forward(REFERER);