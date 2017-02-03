<?php

namespace LimitedInvitations;

$user = elgg_extract('entity', $vars, null);

echo elgg_view_field([
	'#type' => 'number',
	'#label' => elgg_echo('limited_invitations:grant_invitations'),
	'#help' => elgg_echo('limited_invitations:grant:invites:help'),
	'name' => 'invites',
	'value' => Plugin::getAllowedInvitations($user)
]);

echo elgg_view_field([
	'#type' => 'hidden',
	'name' => 'guid',
	'value' => $user->guid
]);

echo elgg_view_field([
	'#type' => 'submit',
	'value' => elgg_echo('submit')
]);