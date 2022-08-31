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
            $table->string('type')->nullable();
            $table->json('configuration')->nullable();
            $table->string('provider')->nullable();
            $table->string('status');
            $table->timestamp('status_updated_at')->nullable();

            $table->json('meta');
            $table->text('exception_class')->nullable();
            $table->text('exception_message')->nullable();
            $table->text('exception_trace')->nullable();

            $table->timestamps();
        });
    }
};
