<?php
    global $path;

	$collectors = array();
	foreach($collector_templates as $key => $value)
	{
		$collectors[$key] = ((!isset($value->name) || $value->name == "" ) ? $key : $value->name);
	}
?>

<script type="text/javascript" src="<?php echo $path; ?>Modules/collector/Views/collector.js"></script>
<script type="text/javascript" src="<?php echo $path; ?>Lib/tablejs/table.js"></script>
<script type="text/javascript" src="<?php echo $path; ?>Lib/tablejs/custom-table-fields.js"></script>
<script type="text/javascript" src="<?php echo $path; ?>Lib/misc/properties_visualizer.js"></script>

<style>
#table input[type="text"] {
  width: 88%;
}
</style>

<div>
    <div id="apihelphead" style="float:right;"><a href="api"><?php echo _('Collectors Help'); ?></a></div>
    <div id="localheading"><h2><?php echo _('Collectors'); ?></h2></div>

    <div id="table"><div align='center'><?php echo _("loading..."); ?></div></div>
	
    <div id="nocollectors" class="hide">
        <div class="alert alert-block">
            <h4 class="alert-heading"><?php echo _('No collectors'); ?></h4><br>
            <p><?php echo _('There are no collectors configured. Please add a new collector.'); ?></p>
        </div>
    </div>

    <div id="bottomtoolbar"><hr>
        <button id="addnewcollector" class="btn btn-small" >&nbsp;<i class="icon-plus-sign" ></i>&nbsp;<?php echo _('New collector'); ?></button>
    </div>
</div>

<div id="myModal" class="modal hide" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true" data-backdrop="static">
    <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
        <h3 id="myModalLabel"><?php echo _('Delete collector'); ?></h3>
    </div>
    <div class="modal-body">
        <p><?php echo _('Deleting a collector is permanent.'); ?>
           <br><br>
           <?php echo _('Are you sure you want to delete?'); ?>
        </p>
    </div>
    <div class="modal-footer">
        <button class="btn" data-dismiss="modal" aria-hidden="true"><?php echo _('Cancel'); ?></button>
        <button id="confirmdelete" class="btn btn-primary"><?php echo _('Delete permanently'); ?></button>
    </div>
</div>

<div id="collectorSettingsModal" class="modal hide" tabindex="-1" role="dialog" aria-labelledby="collectorSettingsModalLabel" aria-hidden="true" data-backdrop="static">
    <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
        <h3 id="collectorSettingsModalLabel"><?php echo _('Collector Settings'); ?></h3>
    </div>
    <div class="modal-body">
        <p><span id="collectorSettingsName"></span> <?php echo _('Settings'); ?>
		   <br><br>
		   <div id="collectorSettings"></div>
        </p>
    </div>
    <div class="modal-footer">
        <button id="cancelSettingsCollector" class="btn" data-dismiss="modal" aria-hidden="true"><?php echo _('Cancel'); ?></button>
        <button id="confirmSettingsCollector" class="btn btn-primary"><?php echo _('Save'); ?></button>
    </div>
</div>


<script>
  var path = "<?php echo $path; ?>";
  var collectors = <?php echo json_encode($collectors); ?>;
  var collectorTemplates = <?php echo json_encode($collector_templates); ?>;
  
  // Extend table library field types
  for (z in customtablefields) table.fieldtypes[z] = customtablefields[z];
  table.element = "#table";
  //table.groupby = 'description';
  table.deletedata = false;
  table.fields = {
    //'id':{'type':"fixed"},
    'name':{'title':'<?php echo _("Name"); ?>','type':"text"},
    'description':{'title':'<?php echo _('Description'); ?>','type':"text"},
	'type':{'title':'<?php echo _("Type"); ?>','type':"select",'options':collectors},
	'active':{'title':'<?php echo _("Active"); ?>', 'type':"icon", 'trueicon':"icon-ok", 'falseicon':"icon-remove"},
	'interval':{'title':'<?php echo _("Interval"); ?>', 'type':"text"},
    'public':{'title':"<?php echo _('Public'); ?>", 'type':"icon", 'trueicon':"icon-globe", 'falseicon':"icon-lock"},
    // Actions
    'edit-action':{'title':'', 'type':"edit", 'tooltip':"<?php echo _('Edit'); ?>"},
    'delete-action':{'title':'', 'type':"delete", 'tooltip':"<?php echo _('Delete'); ?>"},
    //'view-action':{'title':'', 'type':"iconbasic", 'icon':'icon-wrench'},
    'create-action':{'title':'', 'type':"iconbasic", 'icon':'icon-wrench', 'tooltip':"<?php echo _('Collector Settings'); ?>"}
  };

  update();

  function update(){
    var requestTime = (new Date()).getTime();
    $.ajax({ url: path+"collector/list.json", dataType: 'json', async: true, success: function(data, textStatus, xhr) {
      table.timeServerLocalOffset = requestTime-(new Date(xhr.getResponseHeader('Date'))).getTime(); // Offset in ms from local to server time
      table.data = data;
/*
	  for (d in data) {
        if (data[d]['own'] != true){ 
          data[d]['#READ_ONLY#'] = true;  // if the data field #READ_ONLY# is true, the fields type: edit, delete will be ommited from the table row and icon type will not update when clicked.
        }
      }
*/
      table.draw();
      if (table.data.length !== 0) {
        $("#nocollectors").hide();
        $("#localheading").show();
        $("#apihelphead").show();
      } else {
        $("#nocollectors").show();
        $("#localheading").hide();
        $("#apihelphead").hide();
      }
    }});
  }

  var updater;
  var settingInputs;
  var colTemplate = null;
  var rowid;
  function updaterStart(func, interval)
  {
    clearInterval(updater);
    updater = null;
    if (interval > 0) updater = setInterval(func, interval);
  }
  updaterStart(update, 10000);

  $("#table").bind("onEdit", function(e){
    updaterStart(update, 0);
  });

  $("#table").bind("onSave", function(e,id,fields_to_update){
    collector.set(id,fields_to_update);
  });

  $("#table").bind("onResume", function(e){
    updaterStart(update, 10000);
  });

  $("#table").bind("onDelete", function(e,id,row){
    $('#myModal').modal('show');
    $('#myModal').attr('collectorid',id);
    $('#myModal').attr('collectorrow',row);
  });

  $("#confirmdelete").click(function()
  {
    var id = $('#myModal').attr('collectorid');
    var row = $('#myModal').attr('schedulerow');
    collector.remove(id);
    table.remove(row);
    update();

    $('#myModal').modal('hide');
  });

  $("#addnewcollector").click(function(){
    $.ajax({ url: path+"collector/create.json", success: function(data){update();} });
  });
 
  $("#table").on('click', '.icon-wrench', function() {
        $('#collectorSettings').html('');
	var type = table.data[$(this).attr('row')]['type'];
	if(type !== ''){
	  
	  for(col in collectorTemplates){
	    if(col === type){
	      colTemplate = collectorTemplates[col];
	      break;
	    }
	  }
	  if(colTemplate !== null){
	    $('#collectorSettingsName').html(type);
	    var container = $('#collectorSettings');
	    
	    rowid = $(this).attr('row');
	    var props = table.data[rowid]['properties'];
	    if(props === ""){
		props = {};
	    }else{
		props = JSON.parse(props);
	    }
            if(colTemplate.properties){
              var visualizer = new PropertiesVisualizer();
	      settingInputs = visualizer.visualize(colTemplate.properties, container, props);
            }else{
              container.html('<?php echo _("No additional settings available."); ?>');  
              settingInputs = [];   
            }
	    $('#collectorSettingsModal').attr('collectorid',table.data[$(this).attr('row')]['id']);
            $('#collectorSettingsModal').modal('show');
	  }

	}
    
  });
  
  $("#confirmSettingsCollector").click(function()
  {
    var id = $('#collectorSettingsModal').attr('collectorid');
    var data = {'properties':{}};
    $.each(settingInputs, function(key, value){
      var id = value.attr("id");
      var val = value.val();
      var dataType = "";
      $.each(colTemplate.properties, function(key, value){
	if(value.name === id){
	dataType = value.dataType;
          return false;
	}
      });
      if(dataType === "number"){
        data.properties[id] = parseFloat(val);
      }else{
        data.properties[id] = val;
      }
    });
    if(!$.isEmptyObject(data.properties)){
      collector.set(id, data);
    }
    $('#collectorSettingsModal').modal('hide');
    $('#collectorSettings').html('');
    var newProps = JSON.stringify(data.properties);
    table.data[rowid]['properties'] = newProps;
  });
  
  $("#cancelSettingsCollector").click(function(){
	$('#collectorSettings').html('');  
  });


</script>

