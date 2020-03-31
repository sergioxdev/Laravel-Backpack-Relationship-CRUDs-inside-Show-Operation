{{-- FIELD CSS - will be loaded in the after_styles section --}}
@push('after_styles')
    <link rel="stylesheet" href="{{ asset('vendor/adminlte/bower_components/bootstrap-datepicker/dist/css/bootstrap-datepicker3.min.css') }}">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/bootstrap.datetimepicker/4.17.42/css/bootstrap-datetimepicker.min.css" />

    <!-- include select2 css-->
    <link href="{{ asset('vendor/adminlte/bower_components/select2/dist/css/select2.min.css') }}" rel="stylesheet" type="text/css" />
    <link href="https://cdnjs.cloudflare.com/ajax/libs/select2-bootstrap-theme/0.1.0-beta.10/select2-bootstrap.min.css" rel="stylesheet" type="text/css" />


    <link href="{{ asset('vendor/backpack/cropper/dist/cropper.min.css') }}" rel="stylesheet" type="text/css" />
    <style>
        .hide {
            display: none;
        }
        .image .btn-group {
            margin-top: 10px;
        }
        img {
            max-width: 100%; /* This rule is very important, please do not ignore this! */
        }
        .img-container, .img-preview {
            width: 100%;
            text-align: center;
        }
        .img-preview {
            float: left;
            margin-right: 10px;
            margin-bottom: 10px;
            overflow: hidden;
        }
        .preview-lg {
            width: 263px;
            height: 148px;
        }

        .btn-file {
            position: relative;
            overflow: hidden;
        }
        .btn-file input[type=file] {
            position: absolute;
            top: 0;
            right: 0;
            min-width: 100%;
            min-height: 100%;
            font-size: 100px;
            text-align: right;
            filter: alpha(opacity=0);
            opacity: 0;
            outline: none;
            background: white;
            cursor: inherit;
            display: block;
        }
    </style>

    <!-- include select2 css-->
    <link href="{{ asset('vendor/adminlte/bower_components/select2/dist/css/select2.min.css') }}" rel="stylesheet" type="text/css" />
    <link href="https://cdnjs.cloudflare.com/ajax/libs/select2-bootstrap-theme/0.1.0-beta.10/select2-bootstrap.min.css" rel="stylesheet" type="text/css" />
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/simplebar@latest/dist/simplebar.css" />
@endpush


{{-- FIELD JS - will be loaded in the after_scripts section --}}
@push('after_scripts')

<script src="https://cdn.jsdelivr.net/npm/simplebar@latest/dist/simplebar.min.js"></script>
<!-- include select2 js-->
<script src="{{ asset('vendor/adminlte/bower_components/select2/dist/js/select2.min.js') }}"></script>
<script>
    jQuery(document).ready(function($) {
        // trigger select2 for each untriggered select2 box
        $('.select3_field').each(function (i, obj) {
            if (!$(obj).hasClass("select2-hidden-accessible"))
            {
                $(obj).select2({
                    theme: "bootstrap"
                });
            }
        });
    });
</script>
<!--//------------------------------------------------------------------------------- -->
<script type="text/javascript">
function register_create_button_action_(create_name)
{
    $("[data-button-type=create]").unbind('click');

    // CRUD CREATE
    // ask for confirmation before deleting an item
    $("[data-button-type=create]").click(function(e)
    {
      e.preventDefault();
      var button_ = $(this);
      var url_ = button_.parents('form').attr('action'); 
      var form_id = $("[form_id="+button_.parents('form').attr('form_id')+"]");
      var div_id = button_.parents('form').attr('form_id');
      var div_id_name = div_id.substring(0,div_id.length - 5);

      var form_id_ = button_.parents('form').attr('form_id');
      var form_ = $("[form_id="+form_id_+"]")[0];
      var data_ = new FormData(form_);

      $('#'+div_id_name+' .callout-danger').hide();
      $('#'+div_id_name+' .callout-danger ul').empty();
      $('#'+div_id_name+' .help-block').empty();
      $('#'+div_id_name+' .form-group').removeClass("has-error");

      $.ajax({
        url: url_,
        type:'POST',
        //data: form_id.serialize(),
        data: data_,
        dataType: 'json',
        processData: false,
        contentType: false,
        success: function(result) {
          button_.parents('.modal-dialog').parent().hide();
          $('.modal-backdrop').remove();
          $('body').removeClass("modal-open");
          $('body').css("padding-right", "");          
          // Show an alert with the result
          new PNotify({
              text: result['success'],
              type: "success"
          });

          if(div_id_name.indexOf("_create_modal") != -1)
          {
            crud_ = eval("crud_"+div_id_name.substring(0,div_id_name.indexOf("_create_modal")));
            crud_.table.ajax.reload();
          }            
        },
        error: function(result) {
          // Show an alert with the result
          $('#'+div_id_name+' .callout-danger').show();
          $.each( result['responseJSON']['errors'], function( key, value ) {
            
            $('#'+div_id_name+' .callout-danger ul').append('<li>'+value+'</li>');
            $('#'+div_id_name+' input[name='+key+']').parent().addClass("has-error");
            $('#'+div_id_name+' input[name='+key+']').parent().append('<div class="help-block">'+value+'</div>');
            //$('#'+div_id_name+' .has-error > label').css({color:'#dd4b39'});  
          });
        }
      });
    });
}
function register_update_button_action_(update_name)
{
    $("[data-button-type=update]").unbind('click');
    // CRUD Update
    // ask for confirmation before deleting an item
    $("[data-button-type=update]").click(function(e)
    {      
      e.preventDefault();
      var button_ = $(this);
      var url_ = button_.parents('form').attr('action'); 
      var form_id = $("[form_id="+button_.parents('form').attr('form_id')+"]");
      var div_id_name = button_.parents('form').attr('form_id');
      //var div_id_name = div_id.substring(0,div_id.length - 5);

      var form_id_ = button_.parents('form').attr('form_id');
      var form_ = $("[form_id="+form_id_+"]")[0];
      var data_ = new FormData(form_);
      
      $('#'+div_id_name+' .callout-danger').hide();
      $('#'+div_id_name+' .callout-danger ul').empty();
      $('#'+div_id_name+' .help-block').empty();
      $('#'+div_id_name+' .form-group').removeClass("has-error");

      $.ajax({
        url: url_,
        type:'POST',
        //data: form_id.serialize(),
        data: data_,
        dataType: 'json',        
        processData: false,
        contentType: false,
        success: function(result) {
          button_.parents('.modal-dialog').parent().hide();
          $('.modal-backdrop').remove();
          $('body').removeClass("modal-open");
          $('body').css("padding-right", "");
          // Show an alert with the result
          new PNotify({
              text: result['success'],
              type: "success"
          });

          if(div_id_name.indexOf("_update_modal_") != -1)
          {
            var crud_name = div_id_name.substring(div_id_name.indexOf("_update_modal_")+14, div_id_name.length);

            if(crud_name.indexOf("_") != -1)
            {
              crud_ = eval("crud_"+crud_name.substring(crud_name.indexOf("_")+1, crud_name.length));
              crud_.table.ajax.reload();
            }
            else
            {
              crud_ = eval("crud_"+div_id_name.substring(0,div_id_name.indexOf("_update_modal_")));
              crud_.table.ajax.reload();
            }
          }
        },
        error: function(result) {
          // Show an alert with the result          
          $('#'+div_id_name+' .callout-danger').show();
          $.each( result['responseJSON']['errors'], function( key, value ) {
            
            $('#'+div_id_name+' .callout-danger ul').append('<li>'+value+'</li>');
            $('#'+div_id_name+' input[name='+key+']').parent().addClass("has-error");
            $('#'+div_id_name+' input[name='+key+']').parent().append('<div class="help-block">'+value+'</div>');
            //$('#'+div_id_name+' .has-error > label').css({color:'#dd4b39'});  
          });
        }
      });
    });
}
//----------------------------------------------------------
function register_custom_button_action_()
{
    //-------------------------------------------------------------------------
    $("[data-button-type=custom_action]").unbind('click');
    // CRUD Update
    // ask for confirmation before deleting an item
    $("[data-button-type=custom_action]").click(function(e)
    {      
      e.preventDefault();
      var button_ = $(this);
      var url_ = button_.parents('form').attr('action'); 
      var form_id = $("[form_id="+button_.parents('form').attr('form_id')+"]");
      var div_id_name = button_.parents('form').attr('form_id');
      //var div_id_name = div_id.substring(0,div_id.length - 5);

      var form_id_ = button_.parents('form').attr('form_id');
      var form_ = $("[form_id="+form_id_+"]")[0];
      var data_ = new FormData(form_);
      
      $('#'+div_id_name+' .callout-danger').hide();
      $('#'+div_id_name+' .callout-danger ul').empty();
      $('#'+div_id_name+' .help-block').empty();
      $('#'+div_id_name+' .form-group').removeClass("has-error");

      $.ajax({
        url: url_,
        type:'POST',
        //data: form_id.serialize(),
        data: data_,
        dataType: 'json',        
        processData: false,
        contentType: false,
        success: function(result) {
          button_.parents('.modal-dialog').parent().hide();
          $('.modal-backdrop').remove();
          $('body').removeClass("modal-open");
          $('body').css("padding-right", "");
          // Show an alert with the result
          new PNotify({
              text: result['success'],
              type: "success"
          });
          
          if(div_id_name.indexOf("_custom_action_modal_") != -1)
          {
            var crud_name = div_id_name.substring(div_id_name.indexOf("_custom_action_modal_")+21, div_id_name.length);

            if(crud_name.indexOf("_") != -1)
            {
              crud_ = eval("crud_"+crud_name.substring(crud_name.indexOf("_")+1, crud_name.length));
              crud_.table.ajax.reload();
            }
            else
            {
              crud_ = eval("crud_"+div_id_name.substring(0,div_id_name.indexOf("_custom_action_modal_")));
              crud_.table.ajax.reload();
            }
          }
        },
        error: function(result) {
          // Show an alert with the result          
          $('#'+div_id_name+' .callout-danger').show();
          $.each( result['responseJSON']['errors'], function( key, value ) {
            
            $('#'+div_id_name+' .callout-danger ul').append('<li>'+value+'</li>');
            $('#'+div_id_name+' input[name='+key+']').parent().addClass("has-error");
            $('#'+div_id_name+' input[name='+key+']').parent().append('<div class="help-block">'+value+'</div>');
            //$('#'+div_id_name+' .has-error > label').css({color:'#dd4b39'});  
          });
        }
      });
    });
    //-------------------------------------------------------------------------
}

//----------------------------------------------------------

function load_data_js(crud_name)
{
    jQuery(document).ready(function($) {
      $( "form[form_id*='"+crud_name+"']").each(function() {
        var $fake_form = $(this);
        //---------------------------------------------------
        $fake_form.find('[data-bs-datepicker]').each(function() {
          var $fake = $(this),
          $field = $fake.parents('.form-group').find('input[type="hidden"]'),
          $customConfig = $.extend({
              format: 'dd/mm/yyyy'
          }, $fake.data('bs-datepicker'));
          $picker = $fake.bootstrapDP($customConfig);

          var $existingVal = $field.val();

          if( $existingVal.length ){
              // Passing an ISO-8601 date string (YYYY-MM-DD) to the Date constructor results in
              // varying behavior across browsers. Splitting and passing in parts of the date
              // manually gives us more defined behavior.
              // See https://stackoverflow.com/questions/2587345/why-does-date-parse-give-incorrect-results
              var parts = $existingVal.split('-');
              var year = parts[0];
              var month = parts[1] - 1; // Date constructor expects a zero-indexed month
              var day = parts[2];
              preparedDate = new Date(year, month, day).format($customConfig.format);
              $fake.val(preparedDate);
              $picker.bootstrapDP('update', preparedDate);
          }

          // prevent users from typing their own date
          // since the js plugin does not support it
          // $fake.on('keydown', function(e){
          //     e.preventDefault();
          //     return false;
          // });

          $picker.on('show hide change', function(e){
              if( e.date ){
                  var sqlDate = e.format('yyyy-mm-dd');
              } else {
                  try {
                      var sqlDate = $fake.val();

                      if( $customConfig.format === 'dd/mm/yyyy' ){
                          sqlDate = new Date(sqlDate.split('/')[2], sqlDate.split('/')[1] - 1, sqlDate.split('/')[0]).format('yyyy-mm-dd');
                      }
                  } catch(e){
                      if( $fake.val() ){
                          PNotify.removeAll();
                          new PNotify({
                              title: 'Whoops!',
                              text: 'Sorry we did not recognise that date format, please make sure it uses a yyyy mm dd combination',
                              type: 'error',
                              icon: false
                          });
                      }
                  }
              }

              $field.val(sqlDate);
          });
        });
        //---------------------------------------------------
        $fake_form.find('[data-bs-datetimepicker]').each(function() {
          var $fake = $(this),
          $field = $fake.closest('.form-group').find('input[type="hidden"]'),
          $customConfig = $.extend({
              format: 'DD/MM/YYYY HH:mm',
              defaultDate: $field.val(),
              @if(isset($field['allows_null']) && $field['allows_null'])
              showClear: true,
              @endif
          }, $fake.data('bs-datetimepicker'));

          $customConfig.locale = $customConfig['language'];
          delete($customConfig['language']);
          var $picker = $fake.datetimepicker($customConfig);

          // $fake.on('keydown', function(e){
          //     e.preventDefault();
          //     return false;
          // });

          $picker.on('dp.change', function(e){
              var sqlDate = e.date ? e.date.format('YYYY-MM-DD HH:mm:ss') : null;
              $field.val(sqlDate);
          });
        });
        //---------------------------------------------------
        $fake_form.find('[data-entity-select3]').each(function() {
          var $fake_sel = $(this);
          var $field = $fake_sel.find('input[type="hidden"]');
          var $select = $fake_sel.find('select');
          var name_previus_select = "";
          var input_previous_val = "0";
          var fields_previous = "";

          $($select).each(function() 
          {
            var $fake_ = $(this);
            var name_function = $(this).attr("name");
            var name_function_ = name_function.charAt(0).toUpperCase() + name_function.slice(1);

            
            //-------------------------------------------------------
            var button_ = $(this);
            var url_ = button_.parents('form').attr('action');
            var fn = "get"+name_function_;
            fn_function = eval(fn);
            
            function fn_function(val,fake)
            {
              if(fields_previous!==NULL)
              {
                data_ = 'id='+val;       
              }
              $.ajax({
                type: 'post',
                dataType: 'json',
                url: url_,
                data: data_,
                success: function(data)
                {
                  var json_obj = $.parseJSON(data);
                  var fields_entity = name_function_.toLowerCase();
                  var fields_entity_ = name_function_;
                  var fields_next = " ucfirst($fields_next)";
                  var fake_form = fake.parents('form:first');
                  var $field = fake.find('select[id="select3_'+fields_entity+'s_list"]');
                  var $input = fake_form.find('input[id="input_'+fields_entity+'s_id"]');

                  $field.empty().append('<option value disabled selected>Select '+fields_entity_+'</option>');
                  if(json_obj.length>0)
                  {
                    $.each(json_obj, function(key, value) {
                      $field.append('<option value="'+value.id+'" selected="">'+value.$fields_attribute+'</option>');
                    });
                    $field.removeAttr("disabled");
                  }

                  fake.find('#select3_'+fields_entity+'s_list').val(fake.find('#select3_'+fields_entity+'s_list option[value="Select '+fields_entity_+'"]').val());

                  var input_val = $input.val();
                  if (input_val !== "" && input_val !== "0" ) 
                  {
                    fake.find('#select3_'+fields_entity+'s_list').val(fake.find('#select3_'+fields_entity+'s_list option[value="'+input_val+'"]').val());
                  }
                }
              });
            }
            //-------------------------------------------------------

            if($fake_.data("type")=="")
            {
              eval("get"+name_function_)('0',$fake_sel);
            }
            else
            {
              input_previous_val = $fake_form.find('#input_'+name_previus_select+'_id').val();
              eval("get"+name_function_)(input_previous_val,$fake_sel);
            }
            name_previus_select = $fake_.attr("name");
            //------------------------------------
            $fake_.change(function() 
            {
              $fake_form.find('#input_'+name_function+'_id').val("");
              $fake_form.find('#input_'+name_function+'_id').val(this.value);

              var name_next_select = $fake_.parents(['data-gr-select']).next(['data-gr-select']).attr("data-gr-select");

              if($fake_form.find('#input_'+name_next_select+'s_id') != null && name_next_select != undefined)
              {
                var name_next_function_ = name_next_select.charAt(0).toUpperCase() + name_next_select.slice(1);

                $fake_form.find('#input_'+name_next_select+'s_id').val("");

                $fake_form.find('#select3_'+name_next_select+'s_list').prop('disabled', true);
                  $fake_form.find('#select3_'+name_next_select+'s_list').empty().append('<option value disabled selected>Select '+name_next_function_+'</option>');
                                
                $fake_form.find('#select3_'+name_next_select+'s_list').trigger("change");
                if(this.value!=="")
                {
                  eval('get'+name_next_function_+'s')(this.value,$fake_sel);
                  $fake_form.find('#input_'+name_function+'_id').val(this.value);
                } 
              }
            });
            var select_id_ = $(this).attr("id");
            //console.log(select_id_);
            $(this).select2({
                theme: "bootstrap"
            });
            /*
            // trigger select2 for each untriggered select2 box
            $('.select3_field').each(function (i, obj) {
                if (!$(obj).hasClass("select2-hidden-accessible"))
                {
                    console.log("ciao");
                    $(obj).select2({
                        theme: "bootstrap"
                    });
                }
            });
            */

            //------------------------------------
          });
        });
        //---------------------------------------------------
        $fake_form.find('textarea[id^="ckeditor-"]').each(function() {
          var fake_down = $(this);          
          fake_down.ckeditor({
            "extraPlugins" : 'oembed,widget'
          });

          fake_down.ckeditor(function(){
            this.on('change', function(){
              if(this.checkDirty())
              {
                this.updateElement();
                //console.log(this.getData());
                //console.log(fake_down.val());
                fake_down.attr("value",this.getData());
                //alert('text changed!');
              }
            });
          });
        });
        //---------------------------------------------------
        $fake_form.find('[id$="_file_clear_button"]').each(function() {
          var fake_clear = $(this);
          fake_clear.click(function() {

            $(this).parent().addClass('hidden');

            var input = $(this).parent().parent().find('[id$="_file_input"]');
            var id = input.attr('id');
            input.removeClass('hidden');
            input.attr("value", "").replaceWith(input.clone(true));
            // add a hidden input with the same name, so that the setXAttribute method is triggered
            $("<input type='hidden' name='"+id+"' value=''>").insertAfter(input);
          });

          fake_clear.parent().find('[id$="_file_input"]').change(function() {
            // remove the hidden input, so that the setXAttribute method is no longer triggered
            $(this).next("input[type=hidden]").remove();
          });
        });
        //---------------------------------------------------
        $fake_form.find('[id$="_file_download"]').each(function() {
          var fake_down = $(this);

          fake_down.click(function(e)
          {
            //---------------------------------------------------
            var href_url_file = $(this).attr('href');
            var dat = $(this).attr('dat');
            var dlink = $(this).attr('dlink'); 
            href_url_file = href_url_file.replace("#", "");
            
            var data_ = {
                'file_': href_url_file,
                'dat_': dat,
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
            };

            //---------------------------------------------------
            $.ajaxSetup({
                headers: {
                    'X-CSRF-Token': "{{ csrf_token() }}"
                }
            });

            @php
                /*
                $permission_ = str_replace("crud.", "", Route::currentRouteName());
                $pos = strpos($permission_, ".");

                if($pos>0)
                    $permission_ = substr($permission_, 0, $pos);

                $route = route("crud.".$permission_.".downloadFile");
                */
            @endphp

            $.ajax({
                type: "POST",
                url: dlink,
                data: data_,
                processData: true,
                xhrFields:{
                    responseType: 'blob'
                },
            }).done(function (response,status,xhr) {
                var filename = "";
                var disposition = xhr.getResponseHeader('Content-Disposition');
                if (disposition && disposition.indexOf('attachment') !== -1) {
                    var filenameRegex = /filename[^;=\n]*=((['"]).*?\2|[^;\n]*)/;
                    var matches = filenameRegex.exec(disposition);
                    if (matches != null && matches[1]) filename = matches[1].replace(/['"]/g, '');
                }
                var type = xhr.getResponseHeader('Content-Type');
                
                //console.log('response: %o', response);
                var blob = new Blob([response], { type: type });
                var URL = window.URL || window.webkitURL;
                var downloadUrl = URL.createObjectURL(blob);

                if (filename) {
                    // use HTML5 a[download] attribute to specify filename
                    var a = document.createElement("a");
                    // safari doesn't support this yet
                    if (typeof a.download === 'undefined') {
                        window.location = downloadUrl;
                    } else {
                        a.href = downloadUrl;
                        a.download = filename;
                        document.body.appendChild(a);
                        a.click();
                    }
                } else {
                    window.location = downloadUrl;
                }
                setTimeout(function() { URL.revokeObjectURL(downloadUrl); }, 100); // cleanup

            }).fail(function (jqXHR, textStatus, errorThrown) {
                //alert('Error: ' + textStatus);
            });
          });
        });
        //---------------------------------------------------
        $fake_form.find('[id^="select_change_"]').each(function() {
          var fake_down = $(this);
          var select_ = [];
          var form = fake_down.parents("form");
          fake_down.find('option').each(function() {
              var val_opt = $(this).val();
              val_opt = val_opt.replace(" ", "_").toLowerCase();
              
              if(val_opt != "")
              {
                select_.push(val_opt);
                var inputCount = form.find('input[name^="'+val_opt+'"]').length;
                if(inputCount>0)
                {
                  form.find('input[name^="'+val_opt+'"]').parent('div').attr('style','display:none !important');
                }

                var selectCount = form.find('select[name^="'+val_opt+'"]').length;
                if(selectCount>0)
                {
                  form.find('select[name^="'+val_opt+'"]').parent('div').attr('style','display:none !important');
                }
              }              
          });

          fake_down.change(function(e)
          {
            var value_ = this.value.replace(" ", "_").toLowerCase();
            var select__ = select_.slice();
            if(value_== "")
            {
                var pos__ = select__.indexOf(value_);
                select__.splice(pos__, 1);
            }
           
            select__.forEach(function (item, index, array) {
              var inputCount = form.find('input[name^="'+item+'"]').length;
                if(inputCount>0)
                {
                  form.find('input[name^="'+item+'"]').parent('div').attr('style','display:none !important');
                }

                var selectCount = form.find('select[name^="'+item+'"]').length;
                if(selectCount>0)
                {
                  form.find('select[name^="'+item+'"]').parent('div').attr('style','display:none !important');
                }
            });
            
            var inputCount = form.find('input[name^="'+value_+'"]').length;
            if(inputCount>0)
            {
              form.find('input[name^="'+value_+'"]').parent('div').attr('style','display:table !important');
            }

            var selectCount = form.find('select[name^="'+value_+'"]').length;
            if(selectCount>0)
            {
              form.find('select[name^="'+value_+'"]').parent('div').attr('style','display:table !important');
            }
          });
        });
        //---------------------------------------------------
        /*
        $fake_form.find('[id^="ckeditor-"]').each(function() {

          



        });
        */
        //---------------------------------------------------
        /*
        $fake_form.find('[id^="ckeditor-"]').each(function() {
          
          



        });
        */
        //---------------------------------------------------
      });
    });
}
</script>
<!--//------------------------------------------------------------------------------- -->

    <script src="{{ asset('vendor/adminlte/bower_components/bootstrap-datepicker/dist/js/bootstrap-datepicker.min.js') }}"></script>
    <script charset="UTF-8" src="{{ asset('vendor/adminlte/bower_components/bootstrap-datepicker/dist/locales/bootstrap-datepicker.en-GB.min.js') }}"></script>


    <script src="{{ asset('vendor/adminlte/bower_components/bootstrap-datepicker/dist/js/bootstrap-datepicker.min.js') }}"></script>
    <script type="text/javascript" src="{{ asset('vendor/adminlte/bower_components/moment/moment.js') }}"></script>
    <script type="text/javascript" src="https://cdn.jsdelivr.net/bootstrap.datetimepicker/4.17.42/js/bootstrap-datetimepicker.min.js"></script>
    

    <script>
    var dateFormat=function(){var a=/d{1,4}|m{1,4}|yy(?:yy)?|([HhMsTt])\1?|[LloSZ]|"[^"]*"|'[^']*'/g,b=/\b(?:[PMCEA][SDP]T|(?:Pacific|Mountain|Central|Eastern|Atlantic) (?:Standard|Daylight|Prevailing) Time|(?:GMT|UTC)(?:[-+]\d{4})?)\b/g,c=/[^-+\dA-Z]/g,d=function(a,b){for(a=String(a),b=b||2;a.length<b;)a="0"+a;return a};return function(e,f,g){var h=dateFormat;if(1!=arguments.length||"[object String]"!=Object.prototype.toString.call(e)||/\d/.test(e)||(f=e,e=void 0),e=e?new Date(e):new Date,isNaN(e))throw SyntaxError("invalid date");f=String(h.masks[f]||f||h.masks.default),"UTC:"==f.slice(0,4)&&(f=f.slice(4),g=!0);var i=g?"getUTC":"get",j=e[i+"Date"](),k=e[i+"Day"](),l=e[i+"Month"](),m=e[i+"FullYear"](),n=e[i+"Hours"](),o=e[i+"Minutes"](),p=e[i+"Seconds"](),q=e[i+"Milliseconds"](),r=g?0:e.getTimezoneOffset(),s={d:j,dd:d(j),ddd:h.i18n.dayNames[k],dddd:h.i18n.dayNames[k+7],m:l+1,mm:d(l+1),mmm:h.i18n.monthNames[l],mmmm:h.i18n.monthNames[l+12],yy:String(m).slice(2),yyyy:m,h:n%12||12,hh:d(n%12||12),H:n,HH:d(n),M:o,MM:d(o),s:p,ss:d(p),l:d(q,3),L:d(q>99?Math.round(q/10):q),t:n<12?"a":"p",tt:n<12?"am":"pm",T:n<12?"A":"P",TT:n<12?"AM":"PM",Z:g?"UTC":(String(e).match(b)||[""]).pop().replace(c,""),o:(r>0?"-":"+")+d(100*Math.floor(Math.abs(r)/60)+Math.abs(r)%60,4),S:["th","st","nd","rd"][j%10>3?0:(j%100-j%10!=10)*j%10]};return f.replace(a,function(a){return a in s?s[a]:a.slice(1,a.length-1)})}}();dateFormat.masks={default:"ddd mmm dd yyyy HH:MM:ss",shortDate:"m/d/yy",mediumDate:"mmm d, yyyy",longDate:"mmmm d, yyyy",fullDate:"dddd, mmmm d, yyyy",shortTime:"h:MM TT",mediumTime:"h:MM:ss TT",longTime:"h:MM:ss TT Z",isoDate:"yyyy-mm-dd",isoTime:"HH:MM:ss",isoDateTime:"yyyy-mm-dd'T'HH:MM:ss",isoUtcDateTime:"UTC:yyyy-mm-dd'T'HH:MM:ss'Z'"},dateFormat.i18n={dayNames:["Sun","Mon","Tue","Wed","Thu","Fri","Sat","Sunday","Monday","Tuesday","Wednesday","Thursday","Friday","Saturday"],monthNames:["Jan","Feb","Mar","Apr","May","Jun","Jul","Aug","Sep","Oct","Nov","Dec","January","February","March","April","May","June","July","August","September","October","November","December"]},Date.prototype.format=function(a,b){return dateFormat(this,a,b)};
    </script>

    {{-- FIELD JS - will be loaded in the after_scripts section --}}
    <script src="{{ asset('vendor/backpack/ckeditor/ckeditor.js') }}"></script>
    <script src="{{ asset('vendor/backpack/ckeditor/adapters/jquery.js') }}"></script>


    <script src="{{ asset('vendor/backpack/cropper/dist/cropper.min.js') }}"></script>
    <script type="text/javascript">
    jQuery(document).ready(function($) {
        // Check if content has changed
        var target = document.querySelector(".col-sm-12 [id^=crudTable] tbody");
        if(target!=null)
        {
            var observer = new MutationObserver(function(mutations) {
                //var tbody = v;
                //console.log(tbody);
                // Loop through all instances of the image field
                $('.form-group.image').each(function(index) {
                    
                    // Find DOM elements under this form-group element
                    var $mainImage = $(this).find('#mainImage');
                    var $uploadImage = $(this).find("#uploadImage");
                    var $hiddenImage = $(this).find("#hiddenImage");
                    var $rotateLeft = $(this).find("#rotateLeft")
                    var $rotateRight = $(this).find("#rotateRight")
                    var $zoomIn = $(this).find("#zoomIn")
                    var $zoomOut = $(this).find("#zoomOut")
                    var $reset = $(this).find("#reset")
                    var $remove = $(this).find("#remove")
                    // Options either global for all image type fields, or use 'data-*' elements for options passed in via the CRUD controller
                    var options = {
                        viewMode: 2,
                        checkOrientation: false,
                        autoCropArea: 1,
                        responsive: true,
                        preview : $(this).attr('data-preview'),
                        aspectRatio : $(this).attr('data-aspectRatio')
                    };
                    var crop = $(this).attr('data-crop');

                    // Hide 'Remove' button if there is no image saved
                    if (!$mainImage.attr('src')){
                        $remove.hide();
                    }
                    // Initialise hidden form input in case we submit with no change


                    $hiddenImage.val($mainImage.attr('src'));
                    
                    // Only initialize cropper plugin if crop is set to true
                    if(crop) {

                        $remove.click(function() {
                            $mainImage.cropper("destroy");
                            $mainImage.attr('src','');
                            $hiddenImage.val('');
                            $rotateLeft.hide();
                            $rotateRight.hide();
                            $zoomIn.hide();
                            $zoomOut.hide();
                            $reset.hide();
                            $remove.hide();
                        });
                    } else {

                        $(this).find("#remove").click(function() {
                            $mainImage.attr('src','');
                            $hiddenImage.val('');
                            $remove.hide();
                        });
                    }

                    $uploadImage.change(function() {
                        var fileReader = new FileReader(),
                                files = this.files,
                                file;

                        if (!files.length) {
                            return;
                        }
                        file = files[0];

                        if (/^image\/\w+$/.test(file.type)) {
                            fileReader.readAsDataURL(file);
                            fileReader.onload = function () {
                                $uploadImage.val("");
                                if(crop){
                                    $mainImage.cropper(options).cropper("reset", true).cropper("replace", this.result);

                                    form = $hiddenImage.closest('form').find(':submit');
                                    
                                    // Override form submit to copy canvas to hidden input before submitting
                                    //$('form').submit(function() {
                                    form.click(function() {
                                        var imageURL = $mainImage.cropper('getCroppedCanvas').toDataURL(file.type);
                                        
                                        $hiddenImage.val(imageURL);
                                        return true; // return false to cancel form action
                                    });
                                    $rotateLeft.click(function() {
                                        $mainImage.cropper("rotate", 90);
                                    });
                                    $rotateRight.click(function() {
                                        $mainImage.cropper("rotate", -90);
                                    });
                                    $zoomIn.click(function() {
                                        $mainImage.cropper("zoom", 0.1);
                                    });
                                    $zoomOut.click(function() {
                                        $mainImage.cropper("zoom", -0.1);
                                    });
                                    $reset.click(function() {
                                        $mainImage.cropper("reset");
                                    });
                                    $rotateLeft.show();
                                    $rotateRight.show();
                                    $zoomIn.show();
                                    $zoomOut.show();
                                    $reset.show();
                                    $remove.show();

                                } else {
                                    $mainImage.attr('src',this.result);
                                    $hiddenImage.val(this.result);
                                    $remove.show();
                                }
                            };
                        } else {
                            alert("Please choose an image file.");
                        }
                    });

                });
            });
            var config = { attributes: true, childList: true, characterData: true };
            observer.observe(target, config);  
        }              
    });
    </script>

    <script src="{{ asset('vendor/backpack/ckeditor/ckeditor.js') }}"></script>
@endpush