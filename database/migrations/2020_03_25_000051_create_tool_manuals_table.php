<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateToolManualsTable extends Migration
{
    /**
     * Schema table name to migrate
     * @var string
     */
    public $tableName = 'tool_manuals';

    /**
     * Run the migrations.
     * @table tool_manuals
     *
     * @return void
     */
    public function up()
    {
        Schema::create($this->tableName, function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->bigIncrements('id');
            $table->string('name', 45);
            $table->unsignedBigInteger('tools_id');
            $table->string('file')->nullable()->default(null);

            $table->index(["tools_id"], 'fk_tool_manuals_tools1_idx');
            $table->softDeletes();
            $table->nullableTimestamps();


            $table->foreign('tools_id', 'fk_tool_manuals_tools1_idx')
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
