<script>

    var wapfLtbl = function(args,$parent) {
        var findNearest = function(value,axis) {
            if(axis[''+value])
                return value;
            var keys = Object.keys(axis);
            value = parseFloat(value);
            if(value < parseFloat(keys[0]))
                return keys[0];
            for(var i=0; i < keys.length; i++ ) {
                if(value > parseFloat(keys[i]) && value <= parseFloat(keys[i+1]))
                    return keys[i+1];
            }
            return keys[i]; // return last
        };
        var lookuptable = wapf_lookup_tables[args[0]];
        var tableValues = [], prev = lookuptable;
        for(var i = 1; i < args.length; i++) {

            var v = '';

            // Assuming [qty] was used
            if( args[i].length < 8 ) {
                v = args[i];
            } else {
                v = WAPF.Util.getFieldValue($parent.find('.input-'+args[i]));
            }

            if(v == '') return 0;
            var n = findNearest(v,prev);
            tableValues.push(n);
            prev = prev[n];
        }
        return tableValues.reduce(function(acc,curr){
            return acc[curr];
        },lookuptable);
    };

    if( window.WAPF) window.WAPF.Util.formulas['lookuptable'] = wapfLtbl;
    else {
        window.customFormulas = window.customFormulas || {};
        window.customFormulas['lookuptable'] = wapfLtbl;
    }

</script>