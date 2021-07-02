<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class UpdateMuseumTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('museum', function (Blueprint $table) {
            $table->json('adress')->change();
            $table->json('metadata')->nullable()->change();
            $table->string('logo');
            $table->string('description');
            $table->integer('qrCodeSize');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        //
    }
}
