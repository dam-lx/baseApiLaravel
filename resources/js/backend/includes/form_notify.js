var $ = jQuery = require('jquery');
require('bootstrap-notify');
var Swal = require('sweetalert2/dist/sweetalert2.all');

$('#demoNotify').click(function(e){
    e.preventDefault();
    $.notify({
                 title: "Update Complete : ",
                 message: "Something cool is just updated!",
                 icon: 'fa fa-check'
             },{
                 type: "info"
             });
});

$('#demoSwal').click(function(e){
    e.preventDefault();
    Swal.fire({
                  title: 'Are you sure?',
                  text: "You won't be able to revert this!",
                  type: 'warning',
                  showCancelButton: true,
                  confirmButtonColor: '#3085d6',
                  cancelButtonColor: '#d33',
                  confirmButtonText: 'Yes, delete it!'
              }).then((result) => {
        if (result.value) {
            Swal.fire(
                'Deleted!',
                'Your file has been deleted.',
                'success'
            )
        }
    })
});