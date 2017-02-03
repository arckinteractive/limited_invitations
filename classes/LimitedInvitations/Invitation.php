<?php

namespace LimitedInvitations;
use ElggObject;

class Invitation extends ElggObject {

	/**
	 * Set subtype to invitation_sent.
	 */
	protected function initializeAttributes() {
		parent::initializeAttributes();

		$this->attributes['subtype'] = "invitation_sent";
		$this->access_id = ACCESS_PRIVATE;
	}
	
	/**
	 * Has this invite been accepted
	 * 
	 * @return type
	 */
	function isAccepted() {
		return (bool) $this->invite_accepted;
	}
	
	/**
	 * Has the invitee become a member
	 * not necessarily via this invite
	 */
	function isRegistered() {
		static $registered;
		
		if (!is_null($registered)) {
			return $registered;
		}
		
		if ($this->invitee_registered) {
			$registered = true;
			return $registered;
		}
		
		$invitee = get_user_by_email($this->email);
		if ($invitee) {
			$this->invitee_registered = 1;
			$registered = true;
			return $registered;
		}
		
		$registered = false;
		return $registered;
	}
}