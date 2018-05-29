<?php

namespace ZiNETHQ\SparkInvite\Models;

use Illuminate\Database\Eloquent\Model;
use Laravel\Spark\Spark;

use ZiNETHQ\SparkInvite\SparkInvite;

class InvitationStatus extends Model {
	public $timestamps = true;
	protected $table = 'invitation_status';

	public static function make( $invitation, $state, $user = null, $notes = null ) {
		$status = new InvitationStatus();
		$status->invitation()->associate( $invitation );
		$status->state = $state;
		$status->user()->associate( $user );
		$status->notes = $notes;
		$status->save();

		return $status;
	}

	/**
	 * Invitation
	 */
	public function invitation() {
		return $this->belongsTo( SparkInvite::invitationModel(), 'invitation_id' );
	}

	/**
	 * Audit User
	 */
	public function user() {
		return $this->belongsTo( Spark::userModel(), 'user_id' );
	}
}
