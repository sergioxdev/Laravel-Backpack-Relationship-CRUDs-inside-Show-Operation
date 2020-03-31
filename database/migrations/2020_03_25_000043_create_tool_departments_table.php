<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateToolDepartmentsTable extends Migration
{
    /**
     * Schema table name to migrate
     * @var string
     */
    public $tableName = 'tool_departments';

    /**
     * Run the migrations.
     * @table tool_departments
     *
     * @return void
     */
    public function up()
    {
        Schema::create($this->tableName, function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->bigIncrements('id');
            $table->string('name', 45);
            $table->unsignedBigInteger('tool_floors_id');

            $table->index(["tool_floors_id"], 'fk_tool_departments_tool_floors1_idx');
            $table->softDeletes();
            $table->nullableTimestamps();


            $table->foreign('tool_floors_id', 'fk_tool_departments_tool_floors1_idx')
                ->references('id')->on('tool_floors')
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
