<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateToolRequestcommentsTable extends Migration
{
    /**
     * Schema table name to migrate
     * @var string
     */
    public $tableName = 'tool_requestcomments';

    /**
     * Run the migrations.
     * @table tool_requestcomments
     *
     * @return void
     */
    public function up()
    {
        Schema::create($this->tableName, function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->bigIncrements('id');
            $table->binary('content');
            $table->unsignedBigInteger('tool_requests_id');
            $table->unsignedBigInteger('users_id');
            $table->date('date_inserted');

            $table->index(["users_id"], 'fk_tool_requestcomments_users1_idx');

            $table->index(["tool_requests_id"], 'fk_tool_requestcomments_tool_requests1_idx');
            $table->softDeletes();
            $table->nullableTimestamps();


            $table->foreign('tool_requests_id', 'fk_tool_requestcomments_tool_requests1_idx')
                ->references('id')->on('tool_requests')
                ->onDelete('no action')
                ->onUpdate('no action');

            $table->foreign('users_id', 'fk_tool_requestcomments_users1_idx')
                ->references('id')->on('users')
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
