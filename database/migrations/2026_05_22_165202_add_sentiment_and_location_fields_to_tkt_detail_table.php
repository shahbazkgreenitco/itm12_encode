<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('tkt_detail', function (Blueprint $table) {
            $table->decimal('sentiment_score', 8, 2)
                ->nullable()
                ->after('updated_at');

            $table->enum('ticket_sentiment', ['1', '2', '3'])
                ->nullable()
                ->comment('1=positive,2=neutral,3=negative')
                ->after('sentiment_score');

            $table->text('sentiment_reson')
                ->nullable()
                ->after('ticket_sentiment');

            $table->unsignedBigInteger('location_id')
                ->nullable()
                ->after('sentiment_reson');

            $table->unsignedBigInteger('internal_place_id')
                ->nullable()
                ->after('location_id');

            $table->unsignedBigInteger('base_location_id')
                ->nullable()
                ->after('internal_place_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('tkt_detail', function (Blueprint $table) {
            $table->dropColumn([
                'sentiment_score',
                'ticket_sentiment',
                'sentiment_reson',
                'location_id',
                'internal_place_id',
                'base_location_id'
            ]);
        });
    }
};
