<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAttendanceRequestsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('attendance_requests', function (Blueprint $table) {
            $table->id();
            $table->foreignId('attendance_id')->constrained('attendances')->onDelete('cascade');
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->datetime('requested_clock_in')->nullable();
            $table->datetime('requested_clock_out')->nullable();
            $table->json('requested_breaks')->nullable();
            $table->text('reason');
            $table->string('status')->default('pending')->comment('申請状態: pending=承認待ち, approved=承認済, rejected=却下');
            $table->foreignId('approver_id')->nullable()->constrained('users')->onDelete('set null');
            $table->dateTime('decided_at')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('attendance_requests');
    }
}
