<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateToolFloorsTable extends Migration
{
    /**
     * Schema table name to migrate
     * @var string
     */
    public $tableName = 'tool_floors';

    /**
     * Run the migrations.
     * @table tool_floors
     *
     * @return void
     */
    public function up()
    {
        Schema::create($this->tableName, function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->bigIncrements('id');
            $table->string('name', 45);
            $table->unsignedBigInteger('tool_buildings_id');

            $table->index(["tool_buildings_id"], 'fk_tool_floors_tool_buildings1_idx');
            $table->softDeletes();
            $table->nullableTimestamps();


            $table->foreign('tool_buildings_id', 'fk_tool_floors_tool_buildings1_idx')
                ->references('id')->on('tool_buildings')
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
