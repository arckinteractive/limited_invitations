<?php

namespace LimitedInvitations;

class Plugin {
	
	/**
	 * Get the number of invitations remaining for a user
	 * 
	 * @staticvar type $cache
	 * @param \ElggUser $user - the user we're checking
	 * @param bool $recalculate - whether to force the calculation, false to use cached values
	 * @return int
	 */
	public static function getRemainingInvitations($user, $recalculate = false) {
		static $cache;
		
		if (!$user instanceof \ElggUser) {
			return 0;
		}
		
		if (!is_array($cache)) {
			$cache = [];
		}
		
		if (isset($cache[$user->guid]) && !$recalculate) {
			return $cache[$user->guid];
		}
		
		if ($user->limited_invitations_granted) {
			$allowed_invitations = (int) $user->limited_invitations_granted;
		}
		else {
			$allowed_invitations = (int) elgg_get_plugin_setting('default_invites', PLUGIN_ID);
		}
		
		$invitations_sent = self::countInvitations($user);
		
		$cache[$user->guid] = max([0, $allowed_invitations - $invitations_sent]);
		
		return $cache[$user->guid];
	}
	
	/**
	 * Count the number of invitations sent
	 * 
	 * @param \ElggUser $user - the user we are checking
	 * @param int|null $since - timestamp we're checking since
	 * @return int
	 */
	public static function countInvitations($user, $since = null) {
		if (!$user instanceof \ElggUser) {
			return 0;
		}

		$options = [
			'type' => 'object',
			'subtype' => 'invitation_sent',
			'owner_guid' => $user->guid,
			'count' => true
		];
		
		if (is_null($since)) {
			$options['created_time_lower'] = self::getInvitationsSinceTime();
		}
		else {
			$options['created_time_lower'] = (int) $since;
		}
		
		return elgg_get_entities($options);
	}
	
	/**
	 * Set the global 'since' time for invitation count
	 * 
	 * @param int $time
	 */
	public static function setInvitationsSince($time) {
		elgg_set_plugin_setting('invitations_reset', (int) $time, PLUGIN_ID);
	}
	
	/**
	 * Get the global settings invitation 'since' time
	 * 
	 * @staticvar int $time
	 * @return int
	 */
	public static function getInvitationsSinceTime() {
		static $time;
		
		if (!is_null($time)) {
			return $time;
		}
		
		$time = (int) elgg_get_plugin_setting('invitations_reset', PLUGIN_ID);
		
		return $time;
	}
}
