<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateToolPositionsTable extends Migration
{
    /**
     * Schema table name to migrate
     * @var string
     */
    public $tableName = 'tool_positions';

    /**
     * Run the migrations.
     * @table tool_positions
     *
     * @return void
     */
    public function up()
    {
        Schema::create($this->tableName, function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->bigIncrements('id');
            $table->boolean('active')->nullable()->default('0');
            $table->date('date_start');
            $table->date('date_end')->nullable()->default(null);
            $table->unsignedBigInteger('tools_id');
            $table->unsignedBigInteger('tool_buildings_id');
            $table->unsignedBigInteger('tool_floors_id');
            $table->unsignedBigInteger('tool_departments_id');

            $table->index(["tools_id"], 'fk_tool_positions_tools1_idx');

            $table->index(["tool_floors_id"], 'fk_tool_positions_tool_floors1_idx');

            $table->index(["tool_departments_id"], 'fk_tool_positions_tool_departments1_idx');

            $table->index(["tool_buildings_id"], 'fk_tool_positions_tool_buildings1_idx');
            $table->softDeletes();
            $table->nullableTimestamps();


            $table->foreign('tool_buildings_id', 'fk_tool_positions_tool_buildings1_idx')
                ->references('id')->on('tool_buildings')
                ->onDelete('no action')
                ->onUpdate('no action');

            $table->foreign('tool_floors_id', 'fk_tool_positions_tool_floors1_idx')
                ->references('id')->on('tool_floors')
                ->onDelete('no action')
                ->onUpdate('no action');

            $table->foreign('tool_departments_id', 'fk_tool_positions_tool_departments1_idx')
                ->references('id')->on('tool_departments')
                ->onDelete('no action')
                ->onUpdate('no action');

            $table->foreign('tools_id', 'fk_tool_positions_tools1_idx')
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
