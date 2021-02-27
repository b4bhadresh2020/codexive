<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateMasterAccountsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('master_accounts', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('account_type_id');
            $table->foreign('account_type_id')->references('id')->on('account_types');
            $table->string('name');
            $table->string('middle_name')->nullable();
            $table->string('last_name')->nullable();
            $table->string('email')->nullable();
            $table->string('mobile_no')->nullable();
            $table->string('emergency_no')->nullable();
            $table->string('address')->nullable();
            $table->string('pan_no')->nullable();
            $table->string('pan_img')->nullable();
            $table->string('aadhar_no')->nullable();
            $table->string('aadhar_img')->nullable();
            $table->string('profile_img')->nullable();
            $table->timestamp('dob')->nullable();
            $table->timestamp('doj')->nullable();
            $table->bigInteger('ctc')->nullable();
            $table->string('position')->nullable();
            $table->string('branch')->nullable();
            $table->string('ifsc')->nullable();
            $table->string('gst_no')->nullable();

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
        Schema::dropIfExists('master_accounts');
    }
}
