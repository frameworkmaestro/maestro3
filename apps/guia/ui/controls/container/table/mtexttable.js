require([
    "dojo/_base/array",
    "dojo/_base/json",
    "dgrid/Grid"
], function (array, dojo, Grid) {
        var response = {{$data}} ;
        var cols = {{$cols}};
        var arrObj = [];
        var i = 0;
        array.forEach(response, function(row){
            var o = {};
            var arr = array.map(row, function(item,index){ o[cols[index]] = item; });
            arrObj[i++] = o;
        });
        var c = {};
        var arr = array.map(cols, function(item){ c[item] = item; });
        var col = dojo.fromJson(dojo.toJson(c));
        var grid = new Grid({columns: c }, "{{$id}}");
        grid.renderArray(arrObj);
});
