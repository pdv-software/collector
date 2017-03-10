<?php global $path, $session, $user; ?>
<style>
  a.anchor{display: block; position: relative; top: -50px; visibility: hidden;}
</style>

<h2><?php echo _('Collector API'); ?></h2>
<h3><?php echo _('Apikey authentication'); ?></h3>
<p><?php echo _('If you want to call any of the following actions when your not logged in you have this options to authenticate with the API key:'); ?></p>
<ul><li><?php echo _('Append on the URL of your request: &apikey=APIKEY'); ?></li>
<li><?php echo _('Use POST parameter: "apikey=APIKEY"'); ?></li>
<li><?php echo _('Add the HTTP header: "Authorization: Bearer APIKEY"'); ?></li></ul>
<p><b><?php echo _('Read only:'); ?></b><br>
<input type="text" style="width:255px" readonly="readonly" value="<?php echo $user->get_apikey_read($session['userid']); ?>" />
</p>
<p><b><?php echo _('Read & Write:'); ?></b><br>
<input type="text" style="width:255px" readonly="readonly" value="<?php echo $user->get_apikey_write($session['userid']); ?>" />
</p>


<h3><?php echo _('Available HTML URLs'); ?></h3>
<table class="table">
    <tr><td><?php echo _('The collector list view'); ?></td><td><a href="<?php echo $path; ?>collector/view"><?php echo $path; ?>collector/view</a></td></tr>
    <tr><td><?php echo _('This page'); ?></td><td><a href="<?php echo $path; ?>collector/api"><?php echo $path; ?>collector/api</a></td></tr>
</table>

<h3><?php echo _('Available JSON commands'); ?></h3>
<p><?php echo _('To use the json api the request url needs to include <b>.json</b>'); ?></p>

<p><b><?php echo _('Collector actions'); ?></b></p>
<table class="table">
    <tr><td><?php echo _('List collectors'); ?></td><td><a href="<?php echo $path; ?>collector/list.json"><?php echo $path; ?>collector/list.json</a></td></tr>
    <tr><td><?php echo _('Get collector details'); ?></td><td><a href="<?php echo $path; ?>collector/get.json?id=1"><?php echo $path; ?>collector/get.json?id=1</a></td></tr>
    <tr><td><?php echo _('Add a collector'); ?></td><td><a href="<?php echo $path; ?>collector/create.json"><?php echo $path; ?>collector/create.json</a></td></tr>
    <tr><td><?php echo _('Delete collector'); ?></td><td><a href="<?php echo $path; ?>collector/delete.json?id=0"><?php echo $path; ?>collector/delete.json?id=0</a></td></tr>
    <tr><td><?php echo _('Update collector'); ?></td><td><a href="<?php echo $path; ?>collector/set.json?id=0&fields={%22name%22:%22New Collector%22,%22description%22:%22Collector%22,%22active%22:%0,%22type%22:%221-Wire%22, %22public%22:0, %22active%22:0, %22interval%22:0}"><?php echo $path; ?>collector/set.json?id=0&fields={"name":"New Collector","description":"Collector","active":0,"type":"1-Wire", "public":0, "active":0, "interval":0}</a></td></tr>
    <tr><td><?php echo _('List templates'); ?></td><td><a href="<?php echo $path; ?>collector/listtemplates.json"><?php echo $path; ?>collector/listtemplates.json</a></td></tr>
</table>

<a class="anchor" id="expression"></a> 
<h3><?php echo _('Collectors templates documentation'); ?></h3>
<p><?php echo _('Template files are located at <b>\'\\Modules\\collector\\data\\*.json\'</b>'); ?></p>
<p><?php echo _('Each file defines a collector type and its properties.'); ?></p>





