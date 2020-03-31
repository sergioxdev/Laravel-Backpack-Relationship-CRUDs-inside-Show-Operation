<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateToolCalibrationsTable extends Migration
{
    /**
     * Schema table name to migrate
     * @var string
     */
    public $tableName = 'tool_calibrations';

    /**
     * Run the migrations.
     * @table tool_calibrations
     *
     * @return void
     */
    public function up()
    {
        Schema::create($this->tableName, function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->bigIncrements('id');
            $table->date('date_inserted');
            $table->date('date_calibration');
            $table->date('date_deadline');
            $table->unsignedBigInteger('tools_id');

            $table->index(["tools_id"], 'fk_tool_calibrations_tools1_idx');
            $table->softDeletes();
            $table->nullableTimestamps();


            $table->foreign('tools_id', 'fk_tool_calibrations_tools1_idx')
                ->references('id')->on('tools')
                ->onDelete('no action')
                ->onUpdate('no action');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
     public function down()
     {
       Schema::dropIfExists($this->tableName);
     }
}
