<div id="users-grid">
</div>

<script>
    $(document).ready(function(){
        $("#users-grid").kendoGrid({
            dataSource: new kendo.data.DataSource({
                transport: {
                    read: {
                        url: '<?php echo base_url("users/read?format=json"); ?>',
                        dataType: "json"
                    },
                    update: {
                        url: '<?php echo base_url("users/update?format=json"); ?>',
                        dataType: "json",
                        type: "POST"
                    },
                    destroy: {
                        url: '<?php echo base_url("users/delete?format=json"); ?>',
                        dataType: "json",
                        type: "POST"
                    },
                    create: {
                        url: '<?php echo base_url("users/create?format=json"); ?>',
                        dataType: "json",
                        type: "POST"
                    }
                },
                schema: {
                    model: {
                        id: "id",
                        fields: {
                            id: {editable: false, nullable: true},
                            username: { editable: false, type: "string" },
                            password: { type: "string" },
                            first_name: { type: "string" },
                            last_name: { type: "string" },
                            email: { type: "string"} ,
                            phone: { type: "string" }
                        }
                    },
                    data: "data",
                    total: "total"
                },
                pageSize: 15,
                serverPaging: true,
                serverSorting: true,
                serverFiltering: true
            }),
            pageable: true,
            scrollable: true,
            toolbar: [{name: "create", text: "Add new user"}],
            //editable: "popup",
            editable: {
                mode: "popup",
               //template: kendo.template($("#user-editor").html()),
                confirmation: "Are you sure you want to delete this record?"
            },
            //rowTemplate: kendo.template($("#user-editor").html()),
            columns: [{
                command: [{name: "edit", text: ""}, {name: "destroy", text: ""}],
                title: "&nbsp;",
                width: "85px"
            },{
                field: "username",
                title: "Username"
            },{
                field: "password",
                title: "Password"
            },{
                field: "first_name",
                title: "First Name"
            },{
                field: "last_name",
                title: "Last Name"
            },{
                field: "email",
                title: "Email"
            },{
                field: "phone",
                title: "Phone"
            }]
        });
    });
</script>