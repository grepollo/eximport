<style>
    .label
    {
        vertical-align: middle;
        padding-right: .5em;
    }
    #month
    {
        vertical-align: middle;
    }
    .toolbar {
        float: right;
    }
</style>
<div id="item-grid">
</div>

<script type="text/x-kendo-template" id="date_toolbar">
    <div class="toolbar">
        <label for="start">Search: </label><input class="k-input" id="search" name="search" style="width: 180px" value="" />
        <button id="searchBtn" class="k-button">Go</button>
    </div>
</script>

<script>
    $(document).ready(function(){
        $("#item-grid").kendoGrid({
            dataSource: new kendo.data.DataSource({
                transport: {
                    read: {
                        url: '<?php echo base_url("items/read?format=json"); ?>',
                        dataType: "json"
                    },
                    update: {
                        url: '<?php echo base_url("items/update?format=json") ;?>',
                        dataType: "json",
                        type: "POST"
                    },
                    destroy: {
                        url: '<?php echo base_url("items/delete?format=json") ;?>',
                        dataType: "json",
                        type: "POST"
                    },
                    create: {
                        url: '<?php echo base_url("items/create?format=json") ;?>',
                        dataType: "json",
                        type: "POST"
                    }
                },
                schema: {
                    model: {
                        id: "itemsId",
                        fields: {
                            itemsId: {editable: false, nullable: true},
                            itemsCode: { type: "string" },
                            itemsDesc: { type: "string" },
                            itemsStyle: { type: "string" },
                            itemsJobouterPrice: { type: "number" },
                            itemsExportPrice: {type: "number"}
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
            toolbar: [
                {name: "create", text: "Add new item"},
                {name: "", template: kendo.template($("#date_toolbar").html())}
            ],
            editable: {
                mode:"popup",
                confirmation: "Are you sure you want to delete this item?"
            },
            columns: [{
                command: [{name: "edit", text: ""},{name: "destroy", text: ""}],
                title: "&nbsp;",
                width: "80px"
            },{
                field: "itemsCode",
                title: "Code",
                width: "120px"
            },{
                field: "itemsDesc",
                title: "Description",
                width: "200px"
            },{
                field: "itemsStyle",
                title: "Style",
                width: "140px"
            },{
                field: "itemsJobouterPrice",
                title: "Job Outer Price",
                format: "{0:n2}",
                width: "110px"
            },{
                field: "itemsExportPrice",
                title: "Export Price",
                format: "{0:n2}",
                width: "110px"
            }]
        });

        $("#searchBtn").click(function(e) {
            $("#item-grid").data("kendoGrid").dataSource.filter({filters:[
                { field: "itemsCode", operator: "contains", value: $("#search").val() }
            ]});
        });
    });
</script>