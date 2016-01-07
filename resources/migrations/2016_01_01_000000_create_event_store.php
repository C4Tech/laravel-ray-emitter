<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateEventStore extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (!Schema::hasTable('event_store')) {
            Schema::create('event_store', function (Blueprint $table) {
                $table->string('identifier', 40);
                $table->unsignedInteger('sequence');
                $table->string('event', 100);
                $table->text('payload');
                $table->timestamps();

                $table->primary(['identifier', 'sequence'], 'entity_version');
                $table->index('identifier');
            });
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        // Event Store should never be deleted.
    }
}
