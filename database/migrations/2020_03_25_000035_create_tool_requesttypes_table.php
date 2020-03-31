<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateToolRequesttypesTable extends Migration
{
    /**
     * Schema table name to migrate
     * @var string
     */
    public $tableName = 'tool_requesttypes';

    /**
     * Run the migrations.
     * @table tool_requesttypes
     *
     * @return void
     */
    public function up()
    {
        Schema::create($this->tableName, function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->bigIncrements('id');
            $table->string('name', 100);
            $table->string('color', 45);
            $table->unsignedBigInteger('tool_requestprioritys_id');

            $table->index(["tool_requestprioritys_id"], 'fk_tool_requesttypes_tool_requestprioritys1_idx');
            $table->softDeletes();
            $table->nullableTimestamps();


            $table->foreign('tool_requestprioritys_id', 'fk_tool_requesttypes_tool_requestprioritys1_idx')
                ->references('id')->on('tool_requestprioritys')
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
