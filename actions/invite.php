<?php

/**
 * Elgg invite friends action
 * 
 * modified to add limitations
 *
 * @package ElggInviteFriends
 */

namespace LimitedInvitations;

elgg_make_sticky_form('invitefriends');

if (!elgg_get_config('allow_registration')) {
	register_error(elgg_echo('invitefriends:registration_disabled'));
	forward(REFERER);
}

$site = elgg_get_site_entity();

$emails = get_input('emails');
$emailmessage = get_input('emailmessage');

$emails = trim($emails);
if (strlen($emails) > 0) {
	$emails = preg_split('/\\s+/', $emails, -1, PREG_SPLIT_NO_EMPTY);
}

if (!is_array($emails) || count($emails) == 0) {
	register_error(elgg_echo('invitefriends:noemails'));
	forward(REFERER);
}

$current_user = elgg_get_logged_in_user_entity();

$invitations_remaining = Plugin::getRemainingInvitations($current_user, true);

if (!$invitations_remaining) {
	register_error(elgg_echo('limited_invitations:error:invite:no_remaining_invitations'));
	forward(REFERER);
}

$error = FALSE;
$bad_emails = array();
$already_members = array();
$ignored_emails = [];
$sent_total = 0;
foreach ($emails as $email) {

	$email = trim($email);
	if (empty($email)) {
		continue;
	}

	// send out other email addresses
	if (!is_email_address($email)) {
		$error = TRUE;
		$bad_emails[] = $email;
		continue;
	}

	if (get_user_by_email($email)) {
		$error = TRUE;
		$already_members[] = $email;
		continue;
	}
	
	if ($sent_total >= $invitations_remaining) {
		$error = true;
		$ignored_emails[] = $email;
		continue;
	}

	$invitecode = generate_invite_code($current_user->username);
	$link = elgg_get_registration_url(array(
		'friend_guid' => $current_user->guid,
		'invitecode' => $invitecode
	));
	
	$message = elgg_echo('invitefriends:email', array(
		$site->name,
		$current_user->name,
		$emailmessage,
		$link,
	));

	$subject = elgg_echo('invitefriends:subject', array($site->name));

	// create the from address
	$site = get_entity($site->guid);
	if ($site && $site->email) {
		$from = $site->email;
	} else {
		$from = 'noreply@' . $site->getDomain();
	}

	elgg_send_email($from, $email, $subject, $message);
	
	// create an invitation entity
	$invitation = new Invitation();
	$invitation->owner_guid = $current_user->guid;
	$invitation->email = strtolower($email);
	$invitation->invitecode = $invitecode;
	$invitation->description = $message;
	$invitation->title = $subject;
	$invitation->save();
	
	$sent_total++;
}

if ($error) {
	register_error(elgg_echo('invitefriends:invitations_sent', array($sent_total)));

	if (count($bad_emails) > 0) {
		register_error(elgg_echo('invitefriends:email_error', array(implode(', ', $bad_emails))));
	}

	if (count($already_members) > 0) {
		register_error(elgg_echo('invitefriends:already_members', array(implode(', ', $already_members))));
	}
	
	if (count($ignored_emails) > 0) {
		register_error(elgg_echo('limited_invitations:ignored_emails', array(implode(', ', $ignored_emails))));
	}

} else {
	elgg_clear_sticky_form('invitefriends');
	system_message(elgg_echo('invitefriends:success'));
}

forward(REFERER);
