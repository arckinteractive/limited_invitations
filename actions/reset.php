<?php

namespace LimitedInvitations;

Plugin::setInvitationsSince(time());

system_message(elgg_echo('limited_invitations:action:reset:success'));

forward(REFERER);