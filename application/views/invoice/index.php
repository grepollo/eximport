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

    /*#grid .k-toolbar
    {
        min-height: 27px;
        padding: 1.3em;
    }*/
    .date-label
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
<h3 class="tm-article-subtitle">Invoices</h3>
<div class="uk-grid" data-uk-grid-margin="">
    <div class="uk-width-medium-1-1">
        <div id="invoice-grid"></div>
    </div>
</div>

<script id="excel_toolbar" type="text/x-kendo-template">
    <button class="k-button" id="excelBtn">Excel View</button>
</script>

<script type="text/x-kendo-template" id="date_toolbar">
    <div class="toolbar">
        <label for="start">From date:</label><input id="start" style="width: 180px" value="" />
        <label for="end">To date:</label><input id="end" style="width: 180px" value=""/>
    </div>
</script>
<script>

$(document).ready(function(){
    var categoryData = [
        { text: "Labor Expenses", value: "1" },
        { text: "Raw Materials", value: "2" },
        { text: "Utilities", value: "3" }
    ];
    var typeData = [
        { text: "Expenses", value: "1" },
        { text: "Income", value: "2" }
    ];

    var invGrid = $("#invoice-grid").kendoGrid({
        dataSource: new kendo.data.DataSource({
            transport: {
                read: {
                    url: '<?php echo base_url("invoice/read?format=json"); ?>',
                    dataType: "json"
                },
                update: {
                    url: '<?php echo base_url("invoice/update?format=json") ;?>',
                    dataType: "json",
                    type: "POST"
                },
                destroy: {
                    url: '<?php echo base_url("invoice/delete?format=json") ;?>',
                    dataType: "json",
                    type: "POST"
                },
                create: {
                    url: '<?php echo base_url("invoice/create?format=json") ;?>',
                    dataType: "json",
                    type: "POST"
                },
                parameterMap: function(options, operation) {

                    if(operation == 'create' || operation == 'update') {
                        options.date = kendo.toString(new Date(options.date), "yyyy-MM-dd" );
                    }
                    return options;
                }
            },
            schema: {
                model: {
                    id: "invoiceId",
                    fields: {
                        invoiceId: {editable: false, nullable: true},
                        type: { type: "string" },
                        category: { type: "string" },
                        paidToName: { type: "string" },
                        date: { type: "date" },
                        description: { type: "string" },
                        checkNumber: { type: "string" },
                        amount: {type: "number"}
                    }
                },
                parse: function(response) {
                    $.each(response.data, function(idx, elem){
                        if (elem.date && typeof elem.date === "string") {
                            elem.date = kendo.parseDate(elem.date, "yyyy-MM-dd");
                        }
                    });

                    return response;
                },
                data: "data",
                total: "total"
            },
            aggregate: [
                { field: "amount", aggregate: "sum" }
            ],
            pageSize: 20,
            serverPaging: true,
            serverSorting: true,
            serverFiltering: true

        }),
        change: function(e) {
           // var dataItem = this.dataItem(this.select());
           // getEntryDetail(dataItem);
        },
        toolbar: [
            {name: "create", text: "Add new invoice"},
            {name: "", template: kendo.template($("#excel_toolbar").html())},
            {name: "", template: kendo.template($("#date_toolbar").html())}
        ],
        editable: {
            mode:"popup",
            confirmation: "Are you sure you want to delete this invoice?"
        },
        scrollable: true,
        pageable: true,
        filterable: true,
        columns: [{
            command: [{name: "edit", text: ""},{name: "destroy", text: ""}],
            title: "&nbsp;",
            width: "50px"
        },{
            field: "invoiceId",
            title: "Invoice No.",
            width: "80px"
        },{
            field: "date",
            title: "Date",
            width: "90px",
            format:"{0:yyyy-MM-dd}",
            editor: function(container, options) {
                $('<input data-text-field="' + options.field + '" data-value-field="' + options.field + '" data-bind="value:' + options.field + '" data-format="' + options.format + '"/>')
                    .appendTo(container)
                    .kendoDatePicker({
                        format: 'yyyy-MM-dd'
                    });
            },
            filterable: false
        },{
            field: "paidToName",
            title: "Name",
            width: "140px"
        },{
            field: "type",
            title: "Type",
            width: "100px",
            editor: function(container, options) {
                $('<input data-text-field="invTypeCode" data-value-field="invTypeCode" data-bind="value:' + options.field + '"/>')
                    .appendTo(container)
                    .kendoDropDownList({
                        dataSource: new kendo.data.DataSource({
                            transport: {
                                read: {
                                    url: '<?php echo base_url("inv_type/read?format=json"); ?>',
                                    dataType: "json"
                                }
                            },
                            schema: {
                                model: {
                                    id: "invTypeId",
                                    fields: {
                                        invTypeId: {editable: false, nullable: true},
                                        invTypeCode: { type: "string" }
                                    }
                                },
                                data: "data",
                                total: "total"
                            }
                        }),
                        optionLabel: "Select type..."

                    });
            }
        },{
            field: "category",
            title: "Category",
            editor: function(container, options) {
                $('<input data-text-field="invCategoryCode" data-value-field="invCategoryCode" data-bind="value:' + options.field + '"/>')
                    .appendTo(container)
                    .kendoDropDownList({
                        dataSource: new kendo.data.DataSource({
                            transport: {
                                read: {
                                    url: '<?php echo base_url("inv_category/read?format=json"); ?>',
                                    dataType: "json"
                                }
                            },
                            schema: {
                                model: {
                                    id: "invCategoryId",
                                    fields: {
                                        invCategoryId: {editable: false, nullable: true},
                                        invCategoryCode: { type: "string" }
                                    }
                                },
                                data: "data",
                                total: "total"
                            }
                        }),
                        optionLabel: "Select category..."
                    });
            },
            width: "100px"
        },{
            field: "description",
            title: "Description",
            filterable: false,
            width: "250px"
        },{
            field: "checkNumber",
            title: "Check No.",
            filterable: false,
            width: "120px"
        },{
            field: "amount",
            title: "Amount",
            width: "130px",
            filterable: false,
            footerTemplate: "<span style='color:red'>Total: #= kendo.toString(sum, 'N') #</span>",
            format: "{0:n2}"
        }]
    });

    $("#excelBtn").click(function(e) {
        var filter = invGrid.data("kendoGrid").dataSource.filter();
        $.ajax({
            url: '<?php echo base_url("invoice/excel") ;?>',
            data: {filter: filter},
            type: 'post',
            dataType: 'json',
            success: function(resp) {
                var url = '<?php echo base_url("invoice/download") ;?>';
                var win = window.open(url, '_self', '');
                win.focus();
            }
        })

    });

    function startChange() {
        var startDate = start.value(),
            endDate = end.value();

        if (startDate) {
            startDate = new Date(startDate);
            startDate.setDate(startDate.getDate());
            end.min(startDate);
        } else if (endDate) {
            start.max(new Date(endDate));
        } else {
            endDate = new Date();
            start.max(endDate);
            end.min(endDate);
        }
    }

    function endChange() {
        var endDate = end.value(),
            startDate = start.value();

        if (endDate) {
            endDate = new Date(endDate);
            endDate.setDate(endDate.getDate());
            start.max(endDate);
        } else if (startDate) {
            end.min(new Date(startDate));
        } else {
            endDate = new Date();
            start.max(endDate);
            end.min(endDate);
        }
        endDate = kendo.toString(new Date(endDate), "yyyy-MM-dd" );
        startDate = kendo.toString(new Date(startDate), "yyyy-MM-dd" );
       if(endDate && startDate) {
           invGrid.data("kendoGrid").dataSource.filter({filters:[
               { field: "date", operator: "lte", value: endDate },
               { field: "date", operator: "gte", value: startDate }
           ]});
       } else {
           invGrid.data("kendoGrid").dataSource.filter({});
       }
    }

    var start = invGrid.find("#start").kendoDatePicker({
        change: startChange,
        format: "yyyy-MM-dd"
    }).data("kendoDatePicker");

    var end = invGrid.find("#end").kendoDatePicker({
        change: endChange,
        format: "yyyy-MM-dd"
    }).data("kendoDatePicker");

    start.max(end.value());
    end.min(start.value());
});
</script>