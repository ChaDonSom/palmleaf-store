<?php

use Lunar\Base\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

class SetLastFourToNullableOnTransactions extends Migration
{
    public function up()
    {
        // Check if we're using PostgreSQL and need to handle the type conversion
        if (DB::getDriverName() === 'pgsql') {
            // Use raw SQL to handle the type conversion from string to smallint
            DB::statement("ALTER TABLE {$this->prefix}transactions ALTER COLUMN last_four TYPE SMALLINT USING last_four::smallint");
            DB::statement("ALTER TABLE {$this->prefix}transactions ALTER COLUMN last_four DROP NOT NULL");
        } else {
            // For other databases, use the standard Laravel approach
            Schema::table($this->prefix.'transactions', function (Blueprint $table) {
                $table->smallInteger('last_four')->nullable()->change();
            });
        }
    }

    public function down()
    {
        // Check if we're using PostgreSQL and need to handle the type conversion
        if (DB::getDriverName() === 'pgsql') {
            // Use raw SQL to make the column not nullable
            DB::statement("ALTER TABLE {$this->prefix}transactions ALTER COLUMN last_four SET NOT NULL");
        } else {
            // For other databases, use the standard Laravel approach
            Schema::table($this->prefix.'transactions', function ($table) {
                $table->smallInteger('last_four')->nullable(false)->change();
            });
        }
    }
}
