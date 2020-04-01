require('jqvmap/dist/jquery.vmap');
require('jqvmap/dist/maps/jquery.vmap.world');
var sample_data = require('jqvmap/examples/js/jquery.vmap.sampledata');
$(document).ready(function(){
    var map = $('#demo-map');
    map.vectorMap({
                      map: 'world_en',
                      backgroundColor: '#fff',
                      color: '#333',
                      hoverOpacity: 0.7,
                      selectedColor: '#666666',
                      enableZoom: true,
                      showTooltip: true,
                      scaleColors: ['#C8EEFF', '#006491'],
                      values: sample_data,
                      normalizeFunction: 'polynomial'
                  });
});