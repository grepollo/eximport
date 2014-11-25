<style type="text/css">
    .k-grid tbody .k-button {
        min-width: 24px;
    }
    .k-grid .k-button, .k-edit-form-container .k-button {
        margin: 0 0.02em;
    }
    .k-button-icontext .k-edit, .k-button-icontext .k-delete {
        margin: 0;
        vertical-align: text-top;
    }
    .k-grid td:first-child {
        text-align: center;
    }
</style>
<h3 class="tm-article-subtitle">Job Order Entry</h3>
<div class="uk-grid" data-uk-grid-margin="">
    <div class="uk-width-medium-1-4">
        <div id="tabstrip">
            <ul>
                <li class="k-state-active">ACTIVE</li>
                <li>CLOSED</li>
            </ul>
            <div>
                <div id="active-grid"></div>
            </div>
            <div>
                <div id="closed-grid"></div>
            </div>
        </div>
    </div>
    <div class="uk-width-medium-3-4">
        <div class="uk-alert uk-alert-success" data-uk-alert style="display:none">
            <a href="" class="uk-alert-close uk-close"></a>
            <p id="msg-success"></p>
        </div>
        <div class="uk-alert uk-alert-danger" data-uk-alert style="display:none">
            <a href="" class="uk-alert-close uk-close"></a>
            <p id="msg-danger"></p>
        </div>
        <div class="uk-grid">
            <div class="uk-width-small-1-2" >
                <label>Buyer: </label>
                <span id="buyerCode" style="font-weight: bold"></span>

            </div>
            <div class="uk-width-small-1-2" >
                <label>Job Order No.: </label>
                <span id="jobOrderId" style="font-weight: bold"></span>
            </div>
        </div>
        <hr class="uk-article-divider">

        <div id="jobEntryItems">
        </div>
    </div>
</div>
<script type="text/x-kendo-template" id="template">
    <div></div>
</script>
<script>

    function getEntryDetail(dataItem)
    {
        //console.log(dataItem);
        var buyerDropDownList = $("#buyerId").data("kendoDropDownList");

        $("#jobEntryItems").empty();

        var jobOrderId = dataItem.jobEntryId;

        var status = dataItem.jobEntryStatus;

        //populate data to form
        $("#buyerCode").html(dataItem.buyerCode);
        $("#jobOrderId").html(jobOrderId);


        function detailInit(e) {
            $("<div/>").appendTo(e.detailCell).kendoGrid({
                 dataSource: new kendo.data.DataSource({
                        transport: {
                            read: {
                                url: '<?php echo base_url("distribution/read?format=json"); ?>',
                                dataType: "json"
                            },
                            update: {
                                url: '<?php echo base_url("distribution/update?format=json") ;?>',
                                dataType: "json",
                                type: "POST"
                            },
                            destroy: {
                                url: '<?php echo base_url("distribution/delete?format=json") ;?>',
                                dataType: "json",
                                type: "POST"
                            },
                            create: {
                                url: '<?php echo base_url("distribution/create?format=json") ;?>',
                                dataType: "json",
                                type: "POST"
                            },
                            parameterMap: function(data, type) {
                            	
                           		if (type === 'create') {
                                  	data.jedId = e.data.jedId;
                                  	
                                }
                                return data;
                         	}
                        },
                        
                        requestEnd: function(e) {
                            var response = e.response;
                            var type = e.type;
                            console.log(e.response);
                            if(e.type == 'update' || e.type == 'create') {
							 	$('#msg-success').html(e.response.msg);
                                $('.uk-alert-success').show();
                                 setTimeout(function(){
                                     $('.uk-alert-success').hide();
                                     $('#msg-success').empty();
                                  },10000);
                            } 
                                //$("#jobEntryItems").data("kendoGrid").dataSource.read();
                                //$("#jobEntryItems").data("kendoGrid").refresh();

                        },
                        error: function(e) {
                        	console.log(e.xhr);
                        	var resp = JSON.parse(e.xhr.responseText);
                        	
                        	alert('ERROR: ' + resp.msg);
                        	
                        },
                        schema: {
                            model: {
                                id: "distributionId",
                                fields: {
                                    distributionId: {editable: false, nullable: true},
                                    jedId: {editable: false },
                                    jobouterId: { editable: true, type: "string" },
                                    jobouterCode: { type: "string" },
                                    distributionDate: {type: "string"},
                                    distributionQty: { type: "number" },
                                    distributionBalancedQty: { type: "string" },
                                    distributionFinishedQty: { type: "number" },
                                    distributionStatus: { type: "string" },
                                    distributionDateUpdated: {type: "string" }
                                }
                            },
                            data: "data",
                            total: "total"
                        },
                        serverFiltering: true,
                        filter: {
                            logic: "and",
                            filters: [
                                {field: "jedId", operator: "eq", value: e.data.jedId}

                            ]
                        }
                    }),
                scrollable: false,
                sortable: true,
                pageable: true,
                edit: function(e) {
                    console.log('edit', e);
                    $(".k-edit-form-container input[name='distributionBalancedQty'], .k-edit-form-container input[name='distributionDate']")
                        .attr('readonly', true);
                    $(".k-edit-form-container input[name='distributionDateUpdated']")
                        .attr('readonly', true)
                        .val('<?php echo date('Y-m-d');?>');
                    $(".k-edit-form-container input[name='distributionStatus']")
                        .attr('readonly', true);

                },
                toolbar: [{name: "create", text: "Add new jobouter"}],
                editable: {
                    mode:"popup",
                    confirmation: "Are you sure you want to delete this jobouter?"
                },
                columns: [{
                    command: [{name: "edit", text: ""},{name: "destroy", text: ""}],
                    title: "&nbsp;",
                    width: "60px"
                },{
                    field: "jobouterId",
                    title: "Job Outer",
                    template: "#= jobouterCode#",
                    editor: function(container, options) {

                        $('<input data-text-field="jobouterCode" data-value-field="jobouterId" data-bind="value:' + options.field + '"/>')
                            .appendTo(container)
                            .kendoDropDownList({
                                autoBind: false,
                                dataSource: new kendo.data.DataSource({
                                    transport: {
                                        read: {
                                            url: '<?php echo base_url("job_outer/read?format=json"); ?>',
                                            dataType: "json"
                                        }
                                    },
                                    schema: {
                                        model: {
                                            id: "jobouterId",
                                            fields: {
                                                jobouterId: {editable: false, nullable: true},
                                                jobouterCode: { type: "string" }
                                            }
                                        },
                                        data: "data",
                                        total: "total"
                                    }
                                }),
                                optionLabel: "Select jobouter...",
                                select: function(e) {
                                    var dataItem = this.dataItem(e.item.index());
                                    /*$(".k-edit-form-container input[name='itemsDesc']")
                                        .attr('disabled', true)
                                        .val(dataItem.itemsDesc);
                                    $(".k-edit-form-container input[name='itemsStyle']")
                                        .attr('disabled', true)
                                        .val(dataItem.itemsStyle);*/
                                    //console.log("event :: select (" + dataItem.text + " : " + dataItem.value + ")" );
                                }
                            });
                    },
                    width: "140px"
                },{
                    field: "distributionDate",
                    title: "Date Encoded",
                    width: "100px"
                },{
                    field: "distributionDateUpdated",
                    title: "Date Updated",
                    width: "100px"
                },{
                    field: "distributionQty",
                    title: "Quantity",
                    width: "80px",
                    format: "{0:n0}"
                },{
                    field: "distributionBalancedQty",
                    title: "Balance",
                    width: "80px",
                    format: "{0:n0}"
                },{
                    field: "distributionFinishedQty",
                    title: "Finished",
                    width: "80px",
                    format: "{0:n0}"
                },{
                    field: "distributionStatus",
                    title: "Status",
                    width: "140px"
                }]
            });

        }

        if(status == 'ACTIVE') {

        var itemsGrid = $("#jobEntryItems").kendoGrid({
                dataSource: new kendo.data.DataSource({
                    transport: {
                        read: {
                            url: '<?php echo base_url("entry_details/read?format=json"); ?>',
                            dataType: "json"
                        }
                    },
                    schema: {
                        model: {
                            id: "jedId",
                            fields: {
                                jedId: {editable: false, nullable: true},
                                jobEntryId: { editable:true, type: "string" },
                                itemsCode: { editable: true, type: "string" },
                                itemsId: {editable:true},
                                itemsDesc: { type: "string" },
                                itemsStyle: { type: "string" },
                                color: { type: "string" },
                                unitId: { editable:true },
                                unitCode: { editable:true, type: "string" },
                                quantity: { type: "number"},
                                handler: {type: "string"}
                            }
                        },
                        data: "data",
                        total: "total"
                    },
                    serverFiltering: true,
                    filter: {
                        logic: "and",
                        filters: [
                            {field: "jobEntryId", operator: "eq", value: jobOrderId}

                        ]
                    }
                }),
                filterable: true,
                scrollable: true,
                sortable: true,
                //rowTemplate: kendo.template($("#user-editor").html()),
                detailInit: detailInit,
                dataBound: function() {
                    this.expandRow(this.tbody.find("tr.k-master-row").first());
                },
                columns: [{
                    field: "itemsCode",
                    title: "Item Code",
                    width: "120px"
                },{
                    field: "itemsDesc",
                    title: "Description",
                    filterable: false,
                    width: "220px"
                },{
                    field: "itemsStyle",
                    title: "Style",
                    filterable: false,
                    width: "120px"
                },{
                    field: "color",
                    title: "Color",
                    filterable: false,
                    width: "100px"
                },{
                    field: "unitCode",
                    title: "U.M.",
                    filterable: false,
                    width: "80px"
                },{
                    field: "quantity",
                    title: "Quantity",
                    width: "80px",
                    filterable: false,
                    format: "{0:n0}"
                },{
                    field: "handler",
                    title: "P.O. Handler",
                    width: "100px"
                }]
            });
        } else {
            $("#jobEntryItems").kendoGrid({
                dataSource: new kendo.data.DataSource({
                    transport: {
                        read: {
                            url: '<?php echo base_url("entry_details/read?format=json"); ?>',
                            dataType: "json"
                        }
                    },
                    schema: {
                        model: {
                            id: "jedId",
                            fields: {
                                jedId: {editable: false, nullable: true},
                                jobEntryId: { editable:true, type: "string" },
                                itemsCode: { editable: true, type: "string" },
                                itemsId: {editable:true},
                                itemsDesc: { type: "string" },
                                itemsStyle: { type: "string" },
                                color: { type: "string" },
                                unitId: { editable:true },
                                unitCode: { editable:true, type: "string" },
                                quantity: { type: "number"},
                                handler: {type: "string"}
                            }
                        },
                        data: "data",
                        total: "total"
                    },
                    serverFiltering: true,
                    filter: {
                        logic: "and",
                        filters: [
                            {field: "jobEntryId", operator: "eq", value: jobOrderId}

                        ]
                    }
                }),
                scrollable: true,
                //rowTemplate: kendo.template($("#user-editor").html()),
                columns: [{
                    field: "itemsCode",
                    title: "Item Code",
                    width: "120px"
                },{
                    field: "itemsDesc",
                    title: "Description",
                    width: "220px"
                },{
                    field: "itemsStyle",
                    title: "Style",
                    width: "120px"
                },{
                    field: "color",
                    title: "Color",
                    width: "100px"
                },{
                    field: "unitCode",
                    title: "U.M.",
                    width: "80px"
                },{
                    field: "quantity",
                    title: "Quantity",
                    width: "80px",
                    format: "{0:n0}"
                },{
                    field: "handler",
                    title: "P.O. Handler",
                    width: "100px"
                }]
            });
        }
    }

    function populateList(status){
        var id = status.toLowerCase();
        $("#"+id+"-grid").kendoGrid({
            dataSource: new kendo.data.DataSource({
                transport: {
                    read: {
                        url: '<?php echo base_url("entry/read?format=json"); ?>',
                        dataType: "json"
                    },
                    update: {
                        url: '<?php echo base_url("entry_details/update?format=json") ;?>',
                        dataType: "json",
                        type: "POST"
                    },
                    destroy: {
                        url: '<?php echo base_url("entry_details/delete?format=json") ;?>',
                        dataType: "json",
                        type: "POST"
                    },
                    create: {
                        url: '<?php echo base_url("entry_details/create?format=json") ;?>',
                        dataType: "json",
                        type: "POST"
                    }
                },
                schema: {
                    model: {
                        id: "jobEntryId",
                        fields: {
                            jobEntryId: {editable: false, nullable: true},
                            buyerCode: { type: "string" }
                        }
                    },
                    data: "data",
                    total: "total"
                },
                pageSize: 20,
                serverPaging: true,
                serverSorting: true,
                serverFiltering: true,
                filter: {
                    logic: "and",
                    filters: [
                        {field: "jobEntryStatus", operator: "eq", value: status}
                    ]
                }
            }),
            change: function(e) {
                var dataItem = this.dataItem(this.select());
                getEntryDetail(dataItem);
            },
            filterable: true,
            selectable: "row",
            scrollable: true,
            pageable: true,
            columns: [{
                field: "jobEntryId",
                title: "No.",
                filterable: false,
                width: "40px"
            }, {
                field: "buyerCode",
                title: "Buyer"
            }]
        });

    }

    $(document).ready(function(){
        populateList('ACTIVE');
        //for listing
        $("#tabstrip").kendoTabStrip({
            animation:  {
                open: {
                    effects: "fadeIn"
                }
            },
            select: function(e) {
                var status = $(e.item).find("> .k-link").text();
                populateList(status);

            }
        });


        //for buyer autocomplete
        $("#buyerId").kendoDropDownList({
            autoBind: false,
            dataTextField: "buyerCode",
            dataValueField: "buyerId",
            dataSource: new kendo.data.DataSource({
                transport: {
                    read: {
                        url: '<?php echo base_url("buyer/read?format=json"); ?>',
                        dataType: "json"
                    }
                },
                schema: {
                    model: {
                        id: "buyerId",
                        fields: {
                            buyerId: {editable: false, nullable: true},
                            buyerCode: { type: "string" }
                        }
                    },
                    data: "data",
                    total: "total"
                }
            }),
            select: function(e) {
                var dataItem = this.dataItem(e.item.index());
                console.log(dataItem);
                $("#buyerDesc").val(dataItem.buyerDesc);
                $("#buyerAddress").val(dataItem.buyerAddress);
                /* $(".k-edit-form-container input[name='itemsDesc']")
                 .attr('disabled', true)
                 .val(dataItem.itemsDesc);
                 $(".k-edit-form-container input[name='itemsStyle']")
                 .attr('disabled', true)
                 .val(dataItem.itemsStyle); */
                //console.log("event :: select (" + dataItem.text + " : " + dataItem.value + ")" );
            }
        });

        //shipment date picker
        $("#jobEntryShipmentDate").kendoDatePicker({
            // display month and year in the input
            format: "yyyy-MM-dd"
        });
        var activeGrid = $("#active-grid").data("kendoGrid");
        //new button
        $(".newBtn").click(function(){
            $('.newBtn, .delBtn').hide();
            $("#jobEntryItems").empty();
            $(".uk-form-horizontal").get(0).reset();
            activeGrid.dataSource.read();
            activeGrid.refresh();
        });

        //save button
        $('.savBtn').click(function(){
            var orderNo = $("#jobEntryId").val();
            console.log(orderNo);
            if(orderNo > 0) { //update

                console.log('update');
            } else { //create
                console.log('add');
                var formData = $(".uk-form-horizontal").serialize();
                console.log(formData);
                $.ajax({
                    url: '<?php echo base_url("entry/create"); ?>',
                    data: formData,
                    type: 'POST',
                    dataType: 'json',
                    success: function(resp) {
                        console.log(resp);
                        activeGrid.dataSource.read();
                        activeGrid.refresh();
                        $('#active-grid').data('kendoGrid').bind('dataBound',function(e){
                            this.element.find('tbody tr:first').addClass('k-state-selected');
                        })

                        getEntryDetail(resp.data[0]);
                    }
                });
            }
        });
        //delete button
        $('.delBtn').click(function(){
            var jobEntryId = $("#jobEntryId").val();
            $.ajax({
                url: '<?php echo base_url("entry/delete"); ?>',
                data: {'jobEntryId': jobEntryId},
                type: 'POST',
                dataType: 'json',
                success: function(resp) {

                    activeGrid.dataSource.read();
                    activeGrid.refresh();
                    $(".uk-form-horizontal").get(0).reset();
                    $("#jobEntryItems").empty();
                    $('.newBtn, .delBtn').hide();
                }
            });
        });
    });
</script>