// user table start
$(document).ready(function() {
    $("#user-table").DataTable({
        processing: true,
        serverSide: true,
        pageLength: 50,
        ajax: route("user.index"),
        rowId: "id",
        columns: [
            {
                data: "DT_RowIndex",
                name: "DT_RowIndex",
                orderable: false,
                searchable: false
            },
            { data: "name", name: "name" },
            { data: "email", name: "email" },
            { data: "roles", name: "roles" },
            { data: "status_ui", name: "status_ui" },
            {
                data: "action",
                name: "action",
                orderable: false,
                searchable: false
            }
        ]
    });
});


// role table
$(document).ready(function() {
    $("#role-table").DataTable({
        processing: true,
        serverSide: true,
        pageLength: 50,
        ajax: route("role.index"),
        rowId: "id",
        columns: [
            {
                data: "DT_RowIndex",
                name: "DT_RowIndex",
                orderable: false,
                searchable: false
            },
            { data: "name", name: "name" },
            {
                data: "action",
                name: "action",
                orderable: false,
                searchable: false
            }
        ]
    });
});


// Module table
$(document).ready(function() {
    $("#module-table").DataTable({
        processing: true,
        serverSide: true,
        pageLength: 50,
        ajax: route("module.index"),
        rowId: "id",
        columns: [
            {
                data: "DT_RowIndex",
                name: "DT_RowIndex",
                orderable: false,
                searchable: false
            },
            { data: "parent_menu_name", name: "parent_menu_name" },
            { data: "name", name: "name" },
            { data: "url", name: "url" },
            {
                data: "action",
                name: "action",
                orderable: false,
                searchable: false
            }
        ]
    });
});

