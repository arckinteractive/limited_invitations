<?php

namespace LimitedInvitations;

elgg_gatekeeper();

elgg_set_context('invite');

$user = elgg_get_logged_in_user_entity();

$title = elgg_echo('limited_invitations:history');

$link = elgg_view('output/url', [
	'text' => elgg_echo('friends:invite'),
	'href' => 'invite'
]);

$content = elgg_list_entities([
	'type' => 'object',
	'subtype' => 'invitation_sent',
	'owner_guid' => $user->guid,
	'no_results' => elgg_echo('limited_invitations:history:none', [$link])
]);

$layout = elgg_view_layout('content', [
	'title' => $title,
	'content' => $content,
	'filter' => false
]);

echo elgg_view_page($title, $layout);