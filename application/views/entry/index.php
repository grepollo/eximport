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

        <form class="uk-form uk-form-horizontal">
            <div class="uk-grid">
                <div class="uk-width-1-2">
                    <div class="uk-form-row">
                        <label class="uk-form-label" for="buyerCode">Buyer Code</label>
                        <div class="uk-form-controls">
                            <input class="uk-form-width-large" name="buyerId" type="text" id="buyerId" placeholder="Buyer Code">
                        </div>
                    </div>
                    <div class="uk-form-row">
                        <label class="uk-form-label" for="buyerDesc">Description</label>
                        <div class="uk-form-controls">
                            <textarea cols="25" rows="2" name="buyerDesc" id="buyerDesc" placeholder="Description"></textarea>
                        </div>
                    </div>
                    <div class="uk-form-row">
                        <label class="uk-form-label" for="buyerAddress">Address</label>
                        <div class="uk-form-controls">
                            <input class="uk-form-width-large" name="buyerAddress" type="text" id="buyerAddress" placeholder="Address">
                        </div>
                    </div>
                    <div class="uk-form-row">
                        <label class="uk-form-label" for="jobEntryStatus">Status</label>
                        <div class="uk-form-controls">
                            <input  class="uk-form-width-large" name="jobEntryStatus" type="text" id="jobEntryStatus" placeholder="Status" readonly value="ACTIVE">
                        </div>
                    </div>
                </div>


                <div class="uk-width-1-2">
                    <div class="uk-form-row">
                        <label class="uk-form-label" for="jobEntryId">Job Order No.</label>
                        <div class="uk-form-controls">
                            <input class="uk-form-width-large" name="jobEntryId" type="text" id="jobEntryId" placeholder="" readonly>
                        </div>
                    </div>
                    <div class="uk-form-row">
                        <label class="uk-form-label" for="jobEntryDateEncoded">Encoded Date</label>
                        <div class="uk-form-controls">
                            <input class="uk-form-width-large" name="jobEntryDateEncoded" type="text" id="jobEntryDateEncoded" placeholder="" value="<?php echo date('Y-m-d'); ?>" />
                        </div>
                    </div>
                    <div class="uk-form-row">
                        <label class="uk-form-label" for="jobEntryShipmentDate">Shipment Date</label>
                        <div class="uk-form-controls">
                            <input class="uk-form-width-large" name="jobEntryShipmentDate" type="text" id="jobEntryShipmentDate" placeholder="">
                        </div>
                    </div>
                    <div class="uk-form-row">
                        <label class="uk-form-label" for="jobEntryEncodedBy">Encoded by</label>
                        <div class="uk-form-controls">
                            <input class="uk-form-width-large" name="jobEntryEncodedBy" type="text" id="jobEntryEncodedBy" value="<?php echo $this->session->userdata('username');?>" readonly/>
                        </div>
                    </div>
                </div>
            </div>

            <button class="uk-button savBtn" type="button" >Save</button>
            <button class="uk-button delBtn" type="button" style="display:none">Delete</button>
            <button class="uk-button newBtn" type="button" style="display:none">New</button>
        </form>
        <hr class="uk-article-divider">
        <div id="jobEntryItems">

        </div>
    </div>
</div>
<script>

    function getEntryDetail(dataItem)
    {
        console.log(dataItem);
        var buyerDropDownList = $("#buyerId").data("kendoDropDownList");

        $("#jobEntryItems").empty();
        //alert("coloumnName "+ this.dataItem(this.select()).coloumnName);
        var jobOrderId = dataItem.jobEntryId;

        var status = dataItem.jobEntryStatus;
        //populate data to form
        buyerDropDownList.value(dataItem.buyerId);

        $("#buyerDesc").val(dataItem.buyerDesc);
        $("#buyerAddress").val(dataItem.buyerAddress);
        $("#jobEntryStatus").val(status);
        $("#jobEntryDateEncoded").val(dataItem.jobEntryDateEncoded);
        $("#jobEntryShipmentDate").val(dataItem.jobEntryShipmentDate);
        $("#jobEntryEncodedBy").val(dataItem.jobEntryEncodedBy);
        $("#jobEntryId").val(jobOrderId);

        if(status == 'CLOSED') {
            $('.savBtn').hide();
            $('.delBtn').hide();
            $('.newBtn').show();
        } else {
            $('.savBtn').show();
            $('.delBtn').show();
            $('.newBtn').show();
        }

        if(status == 'ACTIVE') {
            $("#jobEntryItems").kendoGrid({
                dataSource: new kendo.data.DataSource({
                    transport: {
                        read: {
                            url: '<?php echo base_url("entry_details/read?format=json"); ?>',
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
                    },
                    sort: { field: "itemsCode", dir: "desc" }
                }),
                scrollable: true,
                sortable: true,
                toolbar: [{name: "create", text: "Add new item"}],
                editable: {
                    mode:"popup",
                    confirmation: "Are you sure you want to delete this record?"
                },
                edit: function(e) {
                    e.model.jobEntryId = jobOrderId;
                },

                //rowTemplate: kendo.template($("#user-editor").html()),
                columns: [{
                    command: [{name: "edit", text: ""},{name: "destroy", text: ""}],
                    title: "&nbsp;",
                    width: "85px"
                },{
                    field: "itemsId",
                    title: "Item Code",
                    template: "#= itemsCode#",
                    editor: function(container, options) {
                        console.log(options.field);
                        $('<input data-text-field="itemsCode" data-value-field="itemsId" data-bind="value:' + options.field + '"/>')
                            .appendTo(container)
                            .kendoDropDownList({
                                autoBind: false,
                                dataSource: new kendo.data.DataSource({
                                    transport: {
                                        read: {
                                            url: '<?php echo base_url("items/read?format=json"); ?>',
                                            dataType: "json"
                                        }
                                    },
                                    schema: {
                                        model: {
                                            id: "itemsId",
                                            fields: {
                                                itemsId: {editable: false, nullable: true},
                                                itemsCode: { type: "string" }
                                            }
                                        },
                                        data: "data",
                                        total: "total"
                                    },
                                    sort: { field: "itemsCode", dir: "desc" }
                                }),
                                optionLabel: "Select item...",
                                select: function(e) {
                                    var dataItem = this.dataItem(e.item.index());
                                    $(".k-edit-form-container input[name='itemsDesc']")
                                        .attr('disabled', true)
                                        .val(dataItem.itemsDesc);
                                    $(".k-edit-form-container input[name='itemsStyle']")
                                        .attr('disabled', true)
                                        .val(dataItem.itemsStyle);
                                    //console.log("event :: select (" + dataItem.text + " : " + dataItem.value + ")" );
                                }
                            });
                    },
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
                    field: "unitId",
                    title: "U.M.",
                    template: "#= unitCode#",
                    editor: function(container, options) {

                        $('<input data-text-field="unitCode" data-value-field="unitId" data-bind="value:' + options.field + '"/>')
                            .appendTo(container)
                            .kendoDropDownList({
                                autoBind: false,
                                dataSource: new kendo.data.DataSource({
                                    transport: {
                                        read: {
                                            url: '<?php echo base_url("unit/read?format=json"); ?>',
                                            dataType: "json"
                                        }
                                    },
                                    schema: {
                                        model: {
                                            id: "unitId",
                                            fields: {
                                                unitId: {editable: false, nullable: true},
                                                unitCode: { type: "string" }
                                            }
                                        },
                                        data: "data",
                                        total: "total"
                                    }
                                }),
                                optionLabel: "Select unit...",
                                select: function(e) {

                                    var dataItem = this.dataItem(e.item.index());
                                    console.log(dataItem);
                                    //console.log("event :: select (" + dataItem.text + " : " + dataItem.value + ")" );
                                }
                            });
                    },
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
                sortable: true,
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
            selectable: "row",
            scrollable: true,
            pageable: true,
            columns: [{
                field: "jobEntryId",
                title: "No.",
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
                //console.log(formData);
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