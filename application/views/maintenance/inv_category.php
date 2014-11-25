<div id="invCat-grid">
</div>

<script>
    $(document).ready(function(){
        $("#invCat-grid").kendoGrid({
            dataSource: new kendo.data.DataSource({
                transport: {
                    read: {
                        url: '<?php echo base_url("inv_category/read?format=json"); ?>',
                        dataType: "json"
                    },
                    update: {
                        url: '<?php echo base_url("inv_category/update?format=json") ;?>',
                        dataType: "json",
                        type: "POST"
                    },
                    destroy: {
                        url: '<?php echo base_url("inv_category/delete?format=json") ;?>',
                        dataType: "json",
                        type: "POST"
                    },
                    create: {
                        url: '<?php echo base_url("inv_category/create?format=json") ;?>',
                        dataType: "json",
                        type: "POST"
                    }
                },
                schema: {
                    model: {
                        id: "invCategoryId",
                        fields: {
                            invCategoryId: {editable: false, nullable: true},
                            invCategoryCode: { type: "string" },
                            invCategoryDesc: { type: "string" }
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
            toolbar: [{name: "create", text: "Add new category"}],
            editable: {
                mode:"popup",
                confirmation: "Are you sure you want to delete this category?"
            },
            columns: [{
                command: [{name: "edit", text: ""},{name: "destroy", text: ""}],
                title: "&nbsp;",
                width: "62px"
            },{
                field: "invCategoryId",
                title: "Id",
                width: "50px"
            },{
                field: "invCategoryCode",
                title: "Code",
                width: "150px"
            },{
                field: "invCategoryDesc",
                title: "Description",
                width: "200px"
            }]
        });
    });
</script>