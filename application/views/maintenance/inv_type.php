<div id="invType-grid">
</div>

<script>
    $(document).ready(function(){
        $("#invType-grid").kendoGrid({
            dataSource: new kendo.data.DataSource({
                transport: {
                    read: {
                        url: '<?php echo base_url("inv_type/read?format=json"); ?>',
                        dataType: "json"
                    },
                    update: {
                        url: '<?php echo base_url("inv_type/update?format=json") ;?>',
                        dataType: "json",
                        type: "POST"
                    },
                    destroy: {
                        url: '<?php echo base_url("inv_type/delete?format=json") ;?>',
                        dataType: "json",
                        type: "POST"
                    },
                    create: {
                        url: '<?php echo base_url("inv_type/create?format=json") ;?>',
                        dataType: "json",
                        type: "POST"
                    }
                },
                schema: {
                    model: {
                        id: "invTypeId",
                        fields: {
                            invTypeId: {editable: false, nullable: true},
                            invTypeCode: { type: "string" },
                            invTypeDesc: { type: "string" }
                        }
                    },
                    data: "data",
                    total: "total"
                },
                pageSize: 20,
                serverPaging: true,
                serverSorting: true,
                serverFiltering: true
            }),
            scrollable: true,
            pageable: true,
            toolbar: [{name: "create", text: "Add new type"}],
            editable: {
                mode:"popup",
                confirmation: "Are you sure you want to delete this type?"
            },
            columns: [{
                command: [{name: "edit", text: ""},{name: "destroy", text: ""}],
                title: "&nbsp;",
                width: "62px"
            },{
                field: "invTypeId",
                title: "Id",
                width: "50px"
            },{
                field: "invTypeCode",
                title: "Code",
                width: "150px"
            },{
                field: "invTypeDesc",
                title: "Description",
                width: "200px"
            }]
        });
    });
</script>