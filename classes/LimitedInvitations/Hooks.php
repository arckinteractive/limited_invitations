<?php

namespace LimitedInvitations;

class Hooks {
	
	/**
	 * 
	 * @param type $hook
	 * @param type $type
	 * @param type $return
	 * @param type $params
	 */
	public static function inviteFormVars($hook, $type, $return, $params) {
		$user = elgg_get_logged_in_user_entity();
		
		$count = Plugin::getRemainingInvitations($user);
		
		if (!$count) {
			$output = elgg_view('limited_invitations/invitation_count');
			
			// we have no invitations left, so we're replacing the form with the message
			// letting them know
			return ['__view_output' => $output];
		}
		
		return $return;
	}
	
	/**
	 * Modify the entity menu
	 * 
	 * @param type $hook
	 * @param type $type
	 * @param type $return
	 * @param type $params
	 */
	public static function entityMenuRegister($hook, $type, $return, $params) {
		if (!$params['entity'] instanceof Invitation) {
			return $return;
		}
		
		// ignore core-added stuff
		// other plugins can hook in after this if they want
		$return = [];
		
		if (!$params['entity']->isRegistered() && !$params['entity']->isAccepted()) {
			$text = elgg_view_icon('repeat');
			$href = elgg_http_add_url_query_elements('action/limited_invitations/resend', ['guid' => $params['entity']->guid]);
			$resend = new \ElggMenuItem('resend', $text, $href);
			$resend->setConfirmText(elgg_echo('limited_invitations:resend:confirm'));
			$resend->setTooltip(elgg_echo('limited_invitations:resend'));
			$return[] = $resend;
		}
		
		return $return;
	}
	
	
	/**
	 * 
	 * @param type $hook
	 * @param type $type
	 * @param type $return
	 * @param type $params
	 */
	public static function registerRouter($hook, $type, $return, $params) {
		if ($return['segments']) {
			// this is not a standard register url... let's let it pass
			return $return;
		}
		
		$friend_guid = get_input('friend_guid');
		$invite_code = get_input('invitecode');
		
		$friend = get_user($friend_guid);
		
		if ($friend && elgg_validate_invite_code($friend->username, $invite_code)) {
			return $return; // welcome!
		}
		
		// you shall not pass
		register_error(elgg_echo('limited_invitations:reroute:invite_only'));
		forward();
	}
	
	/**
	 * enforce registration by invite if necessary
	 * 
	 * @param type $hook
	 * @param type $type
	 * @param type $return
	 * @param type $params
	 */
	public static function registerActionCheck($hook, $type, $return, $params) {
		
		if (elgg_get_plugin_setting('invite_only', PLUGIN_ID) != 'yes') {
			return $return;
		}
		
		// we're enforcing invitation only
		elgg_make_sticky_form('register');
		
		$email = get_input('email');
		$friend_guid = get_input('friend_guid');
		$friend = get_user($friend_guid);
		$invite_code = get_input('invitecode');
		
		if (!$friend || !$invite_code) {
			register_error(elgg_echo('limited_invitations:reroute:invite_only'));
			forward(REFERER);
		}
		
		if (!elgg_validate_invite_code($friend->username, $invite_code)) {
			register_error(elgg_echo('limited_invitations:reroute:invite_only'));
			forward(REFERER);
		}
		
		// ok, so we have a valid friend and code
		// but the url could have been shared, so lets make sure the email matches an invitation
		$ia = elgg_set_ignore_access(true);
		$invitations = elgg_get_entities_from_metadata([
			'type' => 'object',
			'subtype' => 'invitation_sent',
			'owner_guid' => $friend->guid,
			'metadata_name_value_pairs' => [
				'name' => 'invitecode',
				'value' => $invite_code
			],
			'limit' => false,
			'batch' => true
		]);
		
		foreach ($invitations as $invite) {
			if (strtolower($invite->email) == strtolower($email)) {
				// we're good!
				// happy registration
				elgg_set_ignore_access($ia);
				return $return;
			}
		}
		elgg_set_ignore_access($ia);
		
		// we didn't match an email address
		register_error(elgg_echo('limited_invitations:error:email_mismatch'));
		forward(REFERER);
	}
	
	
	/**
	 * The user has just successfully registered, if there is an invitation we can match
	 * let's mark it accepted/registered
	 * 
	 * @param type $hook
	 * @param type $type
	 * @param type $return
	 * @param type $params
	 */
	public static function registerUserComplete($hook, $type, $return, $params) {
		$user = $params['user'];
		$friend = get_user($params['friend_guid']);
		$invite_code = $params['invitecode'];
		
		if (!$friend || !$invite_code || !elgg_validate_invite_code($friend->username, $invite_code)) {
			// not sure how that could be, but perhaps there are other plugins doing stuff we don't expect
			// so I guess they're sneaking through here
			// could return false to delete the user but that's probably overly disruptive
			return $return;
		}

		$ia = elgg_set_ignore_access(true);
		$invitations = elgg_get_entities_from_metadata([
			'type' => 'object',
			'subtype' => 'invitation_sent',
			'metadata_name_value_pairs' => [
				'name' => 'email',
				'value' => strtolower($user->email)
			],
			'limit' => false
		]);

		foreach ($invitations as $invite) {
			$invite->invitee_registered = 1;
			if ($invite->invitecode == $invite_code) {
				$invite->invite_accepted = 1;
			}
		}
		
		// since invitation validates we'll skip the email validation
		// as they received this url via email anyway
		elgg_set_user_validation_status($user->guid, TRUE, 'invitation');
		
		elgg_set_ignore_access($ia);
		
		return $return;
	}
}