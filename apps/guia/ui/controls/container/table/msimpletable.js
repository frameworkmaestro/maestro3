require(["dojo/_base/array", "dojo/_base/json", "dojo/store/Memory", "dgrid/OnDemandGrid"], function (array, dojo, Memory, OnDemandGrid) {
    var response = {{$data}};
    var cols = {{$cols}};
    var attrs = {{$attrs}};
    var arrObj = [];
    var i = 0;
    array.forEach(response, function(row){
        var o = {};
        var arr = array.map(row, function(item, index){ var fieldName = 'field' + index; ; o[fieldName] = item; });
        arrObj[i++] = o;
    });
    var j = dojo.toJson(arrObj);
    console.log(arrObj);
    var store = new Memory({ data: arrObj });
    var c = {};
    var arr = array.map(cols, function(item, i){ 
        var fieldName = 'field' + i; 
        console.log(/class=\"(.*)\"/.attrs[i]);
        c[i] = {
            label: item, 
            field: fieldName,
            renderHeaderCell: function(node) { if (item.charAt(0) == '<') { node.innerHTML = item; } else {node.appendChild(document.createTextNode(item));}},
            renderCell: function(object, value, node) { if (value.charAt(0) == '<') { node.innerHTML = value; } else {node.appendChild(document.createTextNode(value));}}
        };
    });
    var col = dojo.fromJson(dojo.toJson(c));
    var grid = new OnDemandGrid({store: store, columns: c }, "{{$id}}");
    grid.startup();
});
