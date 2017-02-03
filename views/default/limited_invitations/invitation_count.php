<?php

namespace LimitedInvitations;

$user = elgg_get_logged_in_user_entity();

$count = Plugin::getRemainingInvitations($user);

$log_link = elgg_view('output/url', [
	'text' => elgg_echo('limited_invitations:history'),
	'href' => 'limited_invitations/history'
]);

if ($count) {
	$message = elgg_echo('limited_invitations:count:remaining:some', [$count, $log_link]);
}
else {
	$message = elgg_echo('limited_invitations:count:remaining:none', [$log_link]);
}

echo elgg_view('output/longtext', [
	'value' => $message
]);