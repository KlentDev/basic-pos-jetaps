
$(".dltBttn").click(function() {
    var tdh = $(this);
    var id = $(this).attr("id");

    Swal.fire({
        title: 'Are you sure?',
        text: "Once Deleted You won't be able to revert this!",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#3085d6',
        cancelButtonColor: '#d33',
        confirmButtonText: 'Yes'
    }).then((result) => {
        if (result.isConfirmed) {
            $.ajax({
                url: "salesdelete.php",
                type: "post",
                data: {
                    invoice_id: id
                },
                success: function(data) {
                    tdh.parents("tr").hide();
                }
            });

            Swal.fire(
                'Deleted!',
                'Sales has been deleted.',
                'success'
            );
        }
    });
});