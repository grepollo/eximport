<button class="uk-button" id="excelBtn">Excel View</button> <br/>
<div id="jobouter-grid">
</div>

<script type="text/x-kendo-template" id="unpaidDetail">
    <div class="toolbar">
        <a class="k-button k-button-icontext excel-view" href="\\#">Excel View</a>
    </div>
</script>

<script>

    $(document).ready(function(){

    	//excel
        $("#excelBtn").click(function(e) {            
            $.ajax({
                url: '<?php echo base_url("reports/unpaid_excel") ;?>',
                //data: {filter: filter, sort: {field:'jobouterCode', dir: 'asc'}},
                type: 'GET',
                dataType: 'json',
                success: function(resp) {
                    var url = '<?php echo base_url("reports/download") ;?>';
                    var win = window.open(url, '_self', '');
                    win.focus();
                }
            })
        });

        function detailInit(e) {
            $("<div/>").appendTo(e.detailCell).kendoGrid({
                dataSource: new kendo.data.DataSource({
                    transport: {
                        read: {
                            url: '<?php echo base_url("distribution/read?format=json"); ?>',
                            dataType: "json"
                        }
                    },
                    schema: {
                        model: {
                            id: "distributionId",
                            fields: {
                                distributionId: {editable: false, nullable: true},
                                amount: {type: "number"},
                                netAmount: {type: "number"}
                            }
                        },
                        data: "data",
                        total: "total"
                    },
                    aggregate: [
                        { field: "netAmount", aggregate: "sum" }
                    ],
                    serverFiltering: true,
                    filter: {
                        logic: "and",
                        filters: [
                            {field: "jobouterId", operator: "eq", value: e.data.jobouterId},
                            {field: "distributionPayment", operator: "neq", value: 'PAID'},
                            {field: "distributionStatus", operator: "neq", value: 'Not Worked'}
                        ]
                    }
                }),
                //toolbar: kendo.template($("#unpaidDetail").html()),
                scrollable: true,
                sortable: true,
                columns: [{
                    field: "jobEntryId",
                    title: "Job Order",
                    width: "80px"
                },{
                    field: "distributionDate",
                    title: "Date",
                    width: "100px"
                },{
                    field: "handler",
                    title: "PO Handler",
                    width: "100px"
                },{
                    field: "itemsCode",
                    title: "Item",
                    width: "100px"
                },{
                    field: "unitCode",
                    title: "Unit",
                    width: "80px"
                },{
                    field: "distributionQty",
                    title: "Job Quantity",
                    width: "100px",
                    format: "{0:n0}"
                },{
                    field: "distributionFinishedQty",
                    title: "Finished Qty",
                    width: "100px",
                    format: "{0:n0}"
                },{
                    field: "partialQty",
                    title: "Partial Qty",
                    width: "100px",
                    format: "{0:n0}"
                },{
                    field: "distributionStatus",
                    title: "Status",
                    width: "100px"
                },{
                    field: "itemsJobouterPrice",
                    title: "Labor Cost",
                    width: "80px",
                    format: "{0:n2}"
                },{
                    field: "amount",
                    title: "Amount",
                    width: "80px",
                    format: "{0:n2}"
                },{
                    field: "netAmount",
                    title: "Net Income (-2%)",
                    footerTemplate: "<span style='color:red'>Total: #= kendo.toString(sum, 'N') #</span>",
                    width: "130px",
                    width: "130px"
                }]
            });

        }

        $("#jobouter-grid").kendoGrid({
            dataSource: new kendo.data.DataSource({
                transport: {
                    read: {
                        url: '<?php echo base_url("job_outer/unpaid?format=json"); ?>',
                        dataType: "json"
                    }
                },
                schema: {
                    model: {
                        id: "jobouterId",
                        fields: {
                            jobouterId: {editable: false, nullable: true},
                            jobouterCode: { type: "string" },
                            jobouterFirstname: { type: "string" },
                            jobouterLastname: { type: "string" },
                            jobouterContactNo: { type: "string" },
                            jobouterAddress: { type: "string" }
                        }
                    },
                    data: "data",
                    total: "total"
                }//,
                //pageSize: 20
            }),
            scrollable: true,
            filterable: true,
            //pageable: true,
            detailInit: detailInit,
            dataBound: function() {
                this.expandRow(this.tbody.find("tr.k-master-row").first());
            },
            columns: [{
                field: "jobouterCode",
                title: "Code",
                width: "100px"
            },{
                field: "jobouterFirstname",
                title: "First Name",
                filterable: false,
                width: "120px"
            },{
                field: "jobouterLastname",
                title: "Last Name",
                filterable: false,
                width: "120px"
            },{
                field: "jobouterContactNo",
                title: "Contact No",
                filterable: false,
                width: "100px"
            },{
                field: "jobouterAddress",
                title: "Address",
                filterable: false,
                width: "150px"
            }]
        });
    });
</script>