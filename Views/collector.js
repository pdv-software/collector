var collector = {
    'list':function()
    {
        var result = {};
        $.ajax({ url: path+"colector/list.json", dataType: 'json', async: false, success: function(data) {result = data;} });
        return result;
    },

    'set':function(id, fields)
    {
        var result = {};
        $.ajax({ url: path+"collector/set.json", data: "id="+id+"&fields="+JSON.stringify(fields), async: false, success: function(data) {result = data;} });
        return result;
    },

    'remove':function(id)
    {
        $.ajax({ url: path+"collector/delete.json", data: "id="+id, async: false, success: function(data){} });
    },

    'create':function(id)
    {
        $.ajax({ url: path+"collector/create.json", data: "id="+id, async: false, success: function(data){} });
    },

    'listtemplates':function()
    {
        var result = {};
        $.ajax({ url: path+"collector/listtemplates.json", dataType: 'json', async: false, success: function(data) {result = data;} });
        return result;
    },
	

    /*'settings':function(id, data)
    {
        var result = {};
        $.ajax({ url: path+"collector/inittemplate.json", data: "id="+id+"&data="+JSON.stringify(data), dataType: 'json', async: false, success: function(data) {result = data;} });
        return result;
    }*/
}
