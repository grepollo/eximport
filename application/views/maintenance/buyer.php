<div id="buyer-grid">
</div>

<script>
    $(document).ready(function(){
        $("#buyer-grid").kendoGrid({
            dataSource: new kendo.data.DataSource({
                transport: {
                    read: {
                        url: '<?php echo base_url("buyer/read?format=json"); ?>',
                        dataType: "json"
                    },
                    update: {
                        url: '<?php echo base_url("buyer/update?format=json") ;?>',
                        dataType: "json",
                        type: "POST"
                    },
                    destroy: {
                        url: '<?php echo base_url("buyer/delete?format=json") ;?>',
                        dataType: "json",
                        type: "POST"
                    },
                    create: {
                        url: '<?php echo base_url("buyer/create?format=json") ;?>',
                        dataType: "json",
                        type: "POST"
                    }
                },
                schema: {
                    model: {
                        id: "buyerId",
                        fields: {
                            buyerId: {editable: false, nullable: true},
                            buyerCode: { type: "string" },
                            buyerDesc: { type: "string" },
                            buyerAddress: { type: "string" },
                            buyerContactNo: { type: "string" }
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
            toolbar: [{name: "create", text: "Add new buyer"}],
            editable: {
                mode:"popup",
                confirmation: "Are you sure you want to delete this buyer?"
            },
            columns: [{
                command: [{name: "edit", text: ""},{name: "destroy", text: ""}],
                title: "&nbsp;",
                width: "62px"
            },{
                field: "buyerCode",
                title: "Code",
                width: "100px"
            },{
                field: "buyerDesc",
                title: "Description",
                width: "200px"
            },{
                field: "buyerAddress",
                title: "Address",
                width: "140px"
            },{
                field: "buyerContactNo",
                title: "Contact No",
                width: "100px"
            }]
        });
    });
</script>