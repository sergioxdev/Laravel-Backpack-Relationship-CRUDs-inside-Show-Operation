<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateToolRequestsTable extends Migration
{
    /**
     * Schema table name to migrate
     * @var string
     */
    public $tableName = 'tool_requests';

    /**
     * Run the migrations.
     * @table tool_requests
     *
     * @return void
     */
    public function up()
    {
        Schema::create($this->tableName, function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->bigIncrements('id');
            $table->binary('description');
            $table->binary('activity_required')->nullable()->default(null);
            $table->unsignedBigInteger('tools_id');
            $table->unsignedBigInteger('tool_requesttypes_id');
            $table->unsignedBigInteger('tool_requestprioritys_id');
            $table->unsignedBigInteger('tool_requeststatuss_id');
            $table->unsignedBigInteger('users_id');
            $table->date('date_inserted');
            $table->date('date_request');
            $table->boolean('processed')->nullable()->default('0');
            $table->date('date_processed')->nullable()->default(null);

            $table->index(["users_id"], 'fk_tool_requests_users1_idx');

            $table->index(["tool_requesttypes_id"], 'fk_tool_requests_tool_requesttypes1_idx');

            $table->index(["tools_id"], 'fk_tool_requests_tools1_idx');

            $table->index(["tool_requeststatuss_id"], 'fk_tool_requests_tool_requeststatuss1_idx');

            $table->index(["tool_requestprioritys_id"], 'fk_tool_requests_tool_requestprioritys1_idx');
            $table->softDeletes();
            $table->nullableTimestamps();


            $table->foreign('tools_id', 'fk_tool_requests_tools1_idx')
                ->references('id')->on('tools')
                ->onDelete('no action')
                ->onUpdate('no action');

            $table->foreign('users_id', 'fk_tool_requests_users1_idx')
                ->references('id')->on('users')
                ->onDelete('no action')
                ->onUpdate('no action');

            $table->foreign('tool_requesttypes_id', 'fk_tool_requests_tool_requesttypes1_idx')
                ->references('id')->on('tool_requesttypes')
                ->onDelete('no action')
                ->onUpdate('no action');

            $table->foreign('tool_requestprioritys_id', 'fk_tool_requests_tool_requestprioritys1_idx')
                ->references('id')->on('tool_requestprioritys')
                ->onDelete('no action')
                ->onUpdate('no action');

            $table->foreign('tool_requeststatuss_id', 'fk_tool_requests_tool_requeststatuss1_idx')
                ->references('id')->on('tool_requeststatuss')
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
