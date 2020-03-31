<?php 

CRUD::resource('tool', 'ToolCrudController');
CRUD::resource('tool_calibration', 'Tool_calibrationCrudController');
CRUD::resource('tool_type', 'Tool_typeCrudController');
CRUD::resource('tool', 'ToolCrudController');
CRUD::resource('tool_building', 'Tool_buildingCrudController');
CRUD::resource('tool_floor', 'Tool_floorCrudController');
CRUD::resource('tool_department', 'Tool_departmentCrudController');
CRUD::resource('tool_position', 'Tool_positionCrudController');
CRUD::resource('tool_note', 'Tool_noteCrudController');
CRUD::resource('tool_calibration', 'Tool_calibrationCrudController');
CRUD::resource('tool_requestpriority', 'Tool_requestpriorityCrudController');
CRUD::resource('tool_requesttype', 'Tool_requesttypeCrudController');
CRUD::resource('tool_requeststatus', 'Tool_requeststatusCrudController');
CRUD::resource('tool_request', 'Tool_requestCrudController');
CRUD::resource('tool_manual', 'Tool_manualCrudController');
CRUD::resource('tool_interventiontype', 'Tool_interventiontypeCrudController');
CRUD::resource('tool_interventioncompany', 'Tool_interventioncompanyCrudController');
CRUD::resource('tool_intervention', 'Tool_interventionCrudController');
CRUD::resource('tool_requestcomment', 'Tool_requestcommentCrudController');



//-------------------
Route::post("tool_calibration/store2", 'Tool_calibrationCrudController@store2')->name('crud.tool_calibration.store2');
Route::post("tool_calibration/update2", 'Tool_calibrationCrudController@update2')->name('crud.tool_calibration.update2');
//-------------------
Route::post("tool_department/store2", 'Tool_departmentCrudController@store2')->name('crud.tool_department.store2');
Route::post("tool_department/update2", 'Tool_departmentCrudController@update2')->name('crud.tool_department.update2');
//-------------------
Route::post("tool_floor/store2", 'Tool_floorCrudController@store2')->name('crud.tool_floor.store2');
Route::post("tool_floor/update2", 'Tool_floorCrudController@update2')->name('crud.tool_floor.update2');
//-------------------
Route::post("tool_building/gettool_buildings", 'Tool_buildingCrudController@getTool_buildings')->name('crud.tool_building.gettool_buildings');
Route::post("tool_floor/gettool_floors", 'Tool_floorCrudController@getTool_floors')->name('crud.tool_floor.gettool_floors');
Route::post("tool_department/gettool_departments", 'Tool_departmentCrudController@getTool_departments')->name('crud.tool_department.getTool_departments');
//-------------------
Route::post("tool_manual/store2", 'Tool_manualCrudController@store2')->name('crud.tool_manual.store2');
Route::post("tool_manual/update2", 'Tool_manualCrudController@update2')->name('crud.tool_manual.update2');
Route::post("tool_manual/download", 'Tool_manualCrudController@downloadFile')->name('crud.tool_manual.downloadFile');
//-------------------
Route::post("tool_note/store2", 'Tool_noteCrudController@store2')->name('crud.tool_note.store2');
Route::post("tool_note/update2", 'Tool_noteCrudController@update2')->name('crud.tool_note.update2');
//-------------------
Route::post("tool_position/store2", 'Tool_positionCrudController@store2')->name('crud.tool_position.store2');
Route::post("tool_position/update2", 'Tool_positionCrudController@update2')->name('crud.tool_position.update2');
//-------------------
Route::post("tool_request/store2", 'Tool_requestCrudController@store2')->name('crud.tool_request.store2');
Route::post("tool_request/update2", 'Tool_requestCrudController@update2')->name('crud.tool_request.update2');
//-------------------
Route::post("tool_requestcomment/store2", 'Tool_requestcommentCrudController@store2')->name('crud.tool_requestcomment.store2');
Route::post("tool_requestcomment/update2", 'Tool_requestcommentCrudController@update2')->name('crud.tool_requestcomment.update2');
//-------------------
Route::post("tool_intervention/store2", 'Tool_interventionCrudController@store2')->name('crud.tool_intervention.store2');
Route::post("tool_intervention/update2", 'Tool_interventionCrudController@update2')->name('crud.tool_intervention.update2');



