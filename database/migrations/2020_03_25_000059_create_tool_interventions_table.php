<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateToolInterventionsTable extends Migration
{
    /**
     * Schema table name to migrate
     * @var string
     */
    public $tableName = 'tool_interventions';

    /**
     * Run the migrations.
     * @table tool_interventions
     *
     * @return void
     */
    public function up()
    {
        Schema::create($this->tableName, function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->bigIncrements('id');
            $table->binary('description');
            $table->unsignedBigInteger('tool_interventioncompanys_id');
            $table->unsignedBigInteger('tool_interventiontypes_id');
            $table->string('operator', 45);
            $table->date('date_inserted');
            $table->date('date_intervention');
            $table->unsignedBigInteger('tool_requests_id');

            $table->index(["tool_interventioncompanys_id"], 'fk_tool_interventions_tool_interventioncompanys1_idx');

            $table->index(["tool_requests_id"], 'fk_tool_interventions_tool_requests1_idx');

            $table->index(["tool_interventiontypes_id"], 'fk_tool_interventions_tool_interventiontypes1_idx');
            $table->softDeletes();
            $table->nullableTimestamps();


            $table->foreign('tool_requests_id', 'fk_tool_interventions_tool_requests1_idx')
                ->references('id')->on('tool_requests')
                ->onDelete('no action')
                ->onUpdate('no action');

            $table->foreign('tool_interventiontypes_id', 'fk_tool_interventions_tool_interventiontypes1_idx')
                ->references('id')->on('tool_interventiontypes')
                ->onDelete('no action')
                ->onUpdate('no action');

            $table->foreign('tool_interventioncompanys_id', 'fk_tool_interventions_tool_interventioncompanys1_idx')
                ->references('id')->on('tool_interventioncompanys')
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
