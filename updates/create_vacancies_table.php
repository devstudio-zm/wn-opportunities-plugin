<?php

namespace ImpulseTechnologies\Opportunities\Updates;

use Winter\Storm\Database\Schema\Blueprint;
use Winter\Storm\Database\Updates\Migration;
use Winter\Storm\Support\Facades\Schema;

class CreateVacanciesTable extends Migration
{
    public function up()
    {
        Schema::create('impulsetechnologies_opportunities_vacancies', function (Blueprint $table) {
            $table->increments('id');
            $table->string('title');
            $table->string('slug')->unique();
            $table->string('reference', 100)->nullable();
            $table->string('department')->nullable();
            $table->string('location')->nullable();
            $table->string('employment_type', 20)->nullable()->index('opp_vacancies_employment_type_idx');
            $table->string('salary')->nullable();
            $table->text('summary')->nullable();
            $table->mediumText('description')->nullable();
            $table->text('how_to_apply')->nullable();
            $table->string('apply_url')->nullable();
            $table->string('apply_email')->nullable();
            $table->dateTime('closing_at')->nullable()->index();
            $table->dateTime('published_at')->nullable();
            $table->boolean('is_published')->default(false)->index();
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('impulsetechnologies_opportunities_vacancies');
    }
}
