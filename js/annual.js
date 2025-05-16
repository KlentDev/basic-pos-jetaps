$(document).ready(function () {
    var table = $("#annualSalesTable").DataTable({
        "responsive": true,
        "lengthChange": false,
        "autoWidth": false,
        "order": [[0, "asc"]],
        "dom": 'Bfrtip',
        "buttons": [
            {
                extend: "excel",
                text: '<i class="fas fa-file-excel" style="color: orange;"></i> Excel',
                title: 'Annual Sales',
                exportOptions: {
                    columns: ':visible',
                    modifier: {
                        footer: 'true'
                    }
                }
            },
            {
                extend: "pdf",
                text: '<i class="fas fa-file-pdf" style="color: red;"></i> PDF',
                title: 'Annual Sales',
                exportOptions: {
                    columns: ':visible',
                    modifier: {
                        footer: 'true'
                    }
                }
    
            },
            "colvis",
        ],
    });
});