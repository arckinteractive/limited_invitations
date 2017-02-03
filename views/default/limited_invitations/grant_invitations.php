<?php

$user = elgg_extract('entity', $vars, null);

if (!$user instanceof ElggUser) {
	return;
}

echo elgg_view_form('limited_invitations/grant_invitations', [], $vars);