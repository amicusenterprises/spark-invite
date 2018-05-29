<?php

namespace ZiNETHQ\SparkInvite;

use Event;

class SparkInvite {
	public static function invitationModel() {
		return config( 'sparkinvite.models.invitation', 'App\Invitation' );
	}

	public static function invitationStatusModel() {
		return config( 'sparkinvite.models.invitation-status', 'App\InvitationStatus' );
	}

	public function invite( $referrer, $invitee, $data = null, $event = 'invite' ) {
		$model       = self::invitationModel();
		$invitations = $model::getByInvitee( $invitee );
		if ( $invitations->count() > 0 ) {
			$invitation = $invitations->first();
			$invitation->validate();

			return $invitation;
		}

		$invitation = $model::make( $referrer, $invitee, $data );

		$this->publishEvent( $event, $invitation );

		$invitation->setStatus( $model::STATUS_PENDING, $referrer, null );

		return $invitation;
	}

	public function reinvite( $invitation, $user = null, $notes = null ) {
		$invitation->revoke( $user, $notes );

		return $this->invite( $invitation->referrer, $invitation->invitee, $invitation->data, 'reinvite' );
	}

	public function acceptLink( $invitation ) {
		if ( config( 'sparkinvite.https', true ) ) {
			return secure_url( str_replace( '{token}', $invitation->token, config( 'sparkinvite.routes.accept' ) ) );
		}

		return url( str_replace( '{token}', $invitation->token, config( 'sparkinvite.routes.accept' ) ) );
	}

	public function rejectLink( $invitation ) {
		if ( config( 'sparkinvite.https', true ) ) {
			return secure_url( str_replace( '{token}', $invitation->token, config( 'sparkinvite.routes.reject' ) ) );
		}

		return url( str_replace( '{token}', $invitation->token, config( 'sparkinvite.routes.reject' ) ) );
	}

	/**
	 * Fire Laravel event
	 *
	 * @param  string $event event name
	 */
	private function publishEvent( $eventKey, $invitation = null ) {
		Event::fire( config( 'sparkinvite.event.prefix' ) . ".{$eventKey}", [
			'event'      => $eventKey,
			'invitation' => $invitation
		], false );
	}
}
