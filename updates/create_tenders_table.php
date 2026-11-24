<?php

namespace ImpulseTechnologies\Opportunities\Updates;

use Winter\Storm\Database\Schema\Blueprint;
use Winter\Storm\Database\Updates\Migration;
use Winter\Storm\Support\Facades\Schema;

class CreateTendersTable extends Migration
{
    public function up()
    {
        Schema::create('impulsetechnologies_opportunities_tenders', function (Blueprint $table) {
            $table->increments('id');
            $table->string('title');
            $table->string('slug')->unique();
            $table->string('reference', 100)->nullable();
            $table->string('category', 40)->nullable()->index();
            $table->text('summary')->nullable();
            $table->mediumText('description')->nullable();
            $table->text('submission_details')->nullable();
            $table->string('contact_email')->nullable();
            $table->dateTime('closing_at')->nullable()->index();
            $table->dateTime('published_at')->nullable();
            $table->boolean('is_published')->default(false)->index();
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('impulsetechnologies_opportunities_tenders');
    }
}
