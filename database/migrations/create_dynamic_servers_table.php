<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('dynamic_servers', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('provider');
            $table->string('status');
            $table->timestamp('status_updated_at')->nullable();
            $table->json('meta');
            $table->text('exception_message')->nullable();
            $table->timestamps();
        });
    }
};
