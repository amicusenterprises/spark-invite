<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
use Laravel\Spark\Spark;

use ZiNETHQ\SparkInvite\Models\Invitation;

class CreateInvitationStatusTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('invitation_status', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedInteger('invitation_id');
            $table->uuid('user_id')->nullable()->default(null);
            $table->enum('state', Invitation::STATUS)->default(Invitation::STATUS_PENDING);
            $table->text('notes')->nullable();
            $table->timestamps();

            // Indexes
            $table->index('state');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('invitation_status');
    }
}
