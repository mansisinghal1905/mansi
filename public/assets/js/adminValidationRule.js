$(document).ready(function() {
  // Function to get the next row index
  function getNextRowIndex() {
    var rows = $(".permissionRow"); // Get all rows with the class permissionRow

        var lastRow = rows.last(); // Get the last row

    //var lastRow = $(".permissionRow:last-child");
    if (lastRow.length>1) {
        var lastRowId = lastRow.attr("id");
        if (lastRowId) {
            return parseInt(lastRowId.split('addr')[1]) + 1;
        }
    }
    return parseInt(lastRow.attr("id").split('addr')[1]) ; // Default starting index if no rows exist
}

var rowIndex = getNextRowIndex(); // Initialize row index
   // var rowIndex = parseInt($(".permissionRow:last-child").attr("id").split('addr')[1]); // Start index from 2 to match existing rows
    if(rowIndex >1){
        rowIndex=rowIndex+1;
    }

    // Add a new row
    $("#add_row").click(function() {
        var lastRow = $("#addr" + (rowIndex - 1));
        var newRow = lastRow.clone().attr("id", "addr" + rowIndex);

        // Clear input values in the new row
        newRow.find("input").each(function() {
            $(this).val(""); // Clear value
            $(this).removeAttr('readonly'); // Make sure input is not readonly
        });

        // Update the row index
        newRow.find("td:first-child").html(rowIndex + 1);
        $("#tab_logic").append(newRow);
        rowIndex++;
    });

    // Delete the last row
    $("#delete_row").click(function() {
        var rows = $(".permissionRow"); // Get all rows with the class permissionRow

        if (rows.length > 1) { // Ensure at least one row remains
            var lastRow = rows.last(); // Get the last row\
            // console.log(lastRow);
            lastRow.remove(); // Remove the last row
            rowIndex--; // Decrement the row index
        }
    });

    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });

   const permissionsTable= $('#permissionsTable').DataTable({
        processing: true,
        serverSide: true,
        ajax: {
            url: $("#permissionsTable").data("url"),
            type: 'POST', // Use 'POST' method for server-side processing
            data: function(d) {
                // Add CSRF token for Laravel
                d.from_date= $('input[name=from_date]').val();
                d.end_date= $('input[name=end_date]').val();
            },
            dataSrc:"data"
        },
        paging: true,
                pageLength: 5,
                "bServerSide": true,
                "bLengthChange": false,
                'searching': true,
        columns: [
            { data: 'id', name: 'id' },
            { data: 'name', name: 'name' },
            { data: 'created_at', name: 'created_at' },
            { data: 'actions', name: 'actions', orderable: false, searchable: false }
        ]
    });
});
