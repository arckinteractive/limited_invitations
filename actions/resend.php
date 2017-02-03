<?php

namespace LimitedInvitations;

$invitation = get_entity(get_input('guid'));

if (!$invitation || $invitation->owner_guid != elgg_get_logged_in_user_guid()) {
	register_error(elgg_echo('limited_invitations:error:invalid:guid'));
	forward(REFERER);
}

// resend the invitation
$message = $invitation->description;
$subject = $invitation->title;

// create the from address
$site = elgg_get_site_entity();
if ($site && $site->email) {
	$from = $site->email;
} else {
	$from = 'noreply@' . $site->getDomain();
}

elgg_send_email($from, $invitation->email, $subject, $message);

system_message(elgg_echo('limited_invitations:invitation:resent'));

forward(REFERER);