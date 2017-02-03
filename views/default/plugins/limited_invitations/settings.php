<?php

namespace LimitedInvitations;

echo elgg_view('output/url', [
	'text' => elgg_echo('limited_invitations:since:reset'),
	'href' => 'action/limited_invitations/reset',
	'confirm' => elgg_echo('limited_invitations:since:reset:confirm'),
	'class' => 'elgg-button elgg-button-action float-alt',
	'action' => true
]);

echo elgg_view_field([
	'#type' => 'number',
	'#label' => elgg_echo('limited_invitations:settings:label:default_invites'),
	'#help' => elgg_echo('limited_invitations:settings:help:default_invites'),
	'#class' => 'clearfloat',
	'name' => 'params[default_invites]',
	'value' => $vars['entity']->default_invites ? : 0
]);



echo elgg_view_field([
	'#type' => 'select',
	'#label' => elgg_echo('limited_invitations:settings:label:invite_only'),
	'#help' => elgg_echo('limited_invitations:settings:help:invite_only'),
	'name' => 'params[invite_only]',
	'value' => $vars['entity']->invite_only,
	'options_values' => [
		'yes' => elgg_echo('option:yes'),
		'no' => elgg_echo('option:no')
	]
]);