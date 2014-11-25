<script>
    var gridDiv = $("<div/>");
    $(document).ready(function(){
        $("#detail-content").append(gridDiv);
        gridDiv.kendoGrid({
            dataSource: new kendo.data.DataSource({
                transport: {
                    read: {
                        url: '<?php echo base_url("unit/read?format=json"); ?>',
                        dataType: "json"
                    },
                    update: {
                        url: '<?php echo base_url("unit/update?format=json") ;?>',
                        dataType: "json",
                        type: "POST"
                    },
                    destroy: {
                        url: '<?php echo base_url("unit/delete?format=json") ;?>',
                        dataType: "json",
                        type: "POST"
                    },
                    create: {
                        url: '<?php echo base_url("unit/create?format=json") ;?>',
                        dataType: "json",
                        type: "POST"
                    }
                },
                schema: {
                    model: {
                        id: "unitId",
                        fields: {
                            unitId: {editable: false, nullable: true},
                            unitCode: { type: "string" },
                            unitDesc: { type: "string" }
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
            toolbar: [{name: "create", text: "Add new unit"}],
            editable: {
                mode:"popup",
                confirmation: "Are you sure you want to delete this unit?"
            },
            columns: [{
                command: [{name: "edit", text: ""},{name: "destroy", text: ""}],
                title: "&nbsp;",
                width: "82px"
            },{
                field: "unitCode",
                title: "Code",
                width: "100px"
            },{
                field: "unitDesc",
                title: "Description",
                width: "200px"
            }]
        });
    });
</script>