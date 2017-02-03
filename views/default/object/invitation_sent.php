<?php

namespace LimitedInvitations;

$menu = elgg_view_menu('entity', [
	'entity' => $vars['entity'],
	'handler' => 'limited_invitations',
	'sort_by' => 'priority',
	'class' => 'elgg-menu-hz',
]);


echo '<div class="elgg-comments">';

echo $menu;

echo elgg_format_element('h4', [], $vars['entity']->email);

echo elgg_view_friendly_time($vars['entity']->time_created);

if ($vars['entity']->isAccepted()) {
	echo elgg_view('output/longtext', [
		'value' => elgg_echo('limited_invitations:accepted'),
		'class' => 'elgg-text-help'
	]);
}
elseif ($vars['entity']->isRegistered()) {
	echo elgg_view('output/longtext', [
		'value' => elgg_echo('limited_invitations:registered'),
		'class' => 'elgg-text-help'
	]);
}


echo '</div>';