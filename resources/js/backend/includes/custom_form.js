var $ = require("jquery");
require('select2');
require('bootstrap-datepicker/js/bootstrap-datepicker');
$('#demoDate').datepicker({
                                format        : "dd/mm/yyyy",
                                autoclose     : true,
                                todayHighlight: true
                          });
$('#demoSelect').select2();

