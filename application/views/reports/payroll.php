<form class="uk-form uk-form-horizontal" action="">
    <div class="uk-grid">
        <div class="uk-width-1-2">
            <!--  -div class="uk-form-row">
                <label class="uk-form-label" for="buyerCode">Tax Rate: (%)</label>
                <div class="uk-form-controls">
                    <input class="uk-form-width-large" name="tax" type="text" id="tax" placeholder="0.00">
                </div>
            </div-->
            <div class="uk-form-row">
                <label class="uk-form-label" for="buyerDesc">Select Date:</label>
                <div class="uk-form-controls">
                    <input class="uk-form-width-large" name="payrollDate" type="text" id="payrollDate" placeholder="" value="<?php echo date('Y-m-d');?>">
                </div>
            </div>
        </div>
        <div class="uk-width-1-2">

        </div>
    </div>

    <button class="uk-button" id="btn-generate">Generate</button>

</form>
<div class="uk-alert uk-alert-success" data-uk-alert style="display:none">
    <a href="" class="uk-alert-close uk-close"></a>
    <p id="msg-success"></p>
</div>
<div class="uk-alert uk-alert-danger" data-uk-alert style="display:none">
    <a href="" class="uk-alert-close uk-close"></a>
    <p id="msg-danger"></p>
</div>
<hr class="uk-article-divider">

<div class="uk-grid">
    <div class="uk-width-3-10">
        <div id="payroll-list">
        </div>
    </div>
    <div class="uk-width-7-10">
        <div id="payroll-grid">
        </div>
    </div>
</div>
<script id="excel_toolbar" type="text/x-kendo-template">
    <button class="k-button" id="excelBtn">Excel View</button>
</script>
<script>
    var payDate = '';
    function setDate(d) {
        payDate = d;
    };

    function getDate() {
        return payDate;
    }

    function showPayroll(date)
    {
        $("#payroll-grid").kendoGrid({
            dataSource: new kendo.data.DataSource({
                transport: {
                    read: {
                        url: '<?php echo base_url("payroll/read?format=json"); ?>',
                        dataType: "json"
                    }
                },
                schema: {
                    model: {
                        id: "payrollId",
                        fields: {
                            payrollId: {editable: false, nullable: true},
                            payrollAmount: { type: "number" },
                            taxAmount: { type: "number" },
                            payrollNet: { type: "number" }
                        }
                    },
                    data: "data",
                    total: "total"
                },
                aggregate: [
                    {field: "payrollAmount", aggregate: "sum"},
                    {field: "taxAmount", aggregate: "sum"},
                    {field: "payrollNet", aggregate: "sum"}
                ],
                serverPaging: true,
                serverSorting: true,
                serverFiltering: true,
                filter: {
                    logic: "and",
                    filters: [
                        {field: "payrollDate", operator: "eq", value: date}
                    ]
                },
                sort: {field: "jobouterCode", dir: "asc"}
            }),
            toolbar: [
                {name: "", template: kendo.template($("#excel_toolbar").html())},
            ],
            scrollable: true,
            columns: [{
                field: "jobouterFirstname",
                title: "First Name",
                width: "140px"
            },{
                field: "jobouterLastname",
                title: "Last Name",
                width: "140px"
            },{
                field: "payrollAmount",
                title: "Amount",
                width: "130px",
                format: "{0:n2}",
                footerTemplate: "<span style='color:red'>Total: #= kendo.toString(sum, 'N') #</span>"
            },{
                field: "taxAmount",
                title: "Tax (2%)",
                width: "130px",
                format: "{0:n2}",
                footerTemplate: "<span style='color:red'>Total: #= kendo.toString(sum, 'N') #</span>"
            },{
                field: "payrollNet",
                title: "Net Income",
                width: "130px",
                format: "{0:n2}",
                footerTemplate: "<span style='color:red'>Total: #= kendo.toString(sum, 'N') #</span>"
            }]
        });
    }
    $(document).ready(function(){

        showPayroll('<?php echo date("Y-m-d");?>');

        $("#payrollDate").kendoDatePicker({
            format: "yyyy-MM-dd"
        });
        var payrollList = $("#payroll-list").kendoGrid({
            dataSource: new kendo.data.DataSource({
                transport: {
                    read: {
                        url: '<?php echo base_url("payroll/list?format=json"); ?>',
                        dataType: "json"
                    }
                },
                schema: {
                    model: {
                        id: "payrollId",
                        fields: {
                            payrollId: {editable: false, nullable: true}
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
            change: function(e) {
                var dataItem = this.dataItem(this.select());
                setDate(dataItem.payrollDate);
                showPayroll(dataItem.payrollDate);
            },
            selectable: "row",
            columns: [{
                field: "payrollDate",
                title: "Select Date",
                width: "100px"
            }]
        }).data('kendoGrid');

        $("#btn-generate").click(function(e){
            e.preventDefault();
            $.ajax({
                url: '<?php echo base_url("payroll/generate"); ?>',
                data: $(".uk-form-horizontal").serialize(),
                type: 'POST',
                dataType: 'json',
                async: false,
                success: function(resp) {
                    console.log(resp);
                    $('#msg-success').html(resp.msg);
                    $('.uk-alert-success').show();
                    setTimeout(function(){
                        $('.uk-alert-success').hide();
                        $('#msg-success').empty();
                    },8000);

                    payrollList.dataSource.read();
                    payrollList.refresh();
                    $('#payroll-list').data('kendoGrid').bind('dataBound',function(e){
                        this.element.find('tbody tr:first').addClass('k-state-selected');
                    })
                    showPayroll($('#payrollDate').val());
                },
                error: function(xhr) {
                    var resp = JSON.parse(xhr.responseText);
                    $('#msg-danger').html(resp.msg);
                    $('.uk-alert-danger').show();
                    setTimeout(function(){
                        $('.uk-alert-danger').hide();
                        $('#msg-danger').empty();
                    },8000);
                }
            });
        });

        //excel
        $("#excelBtn").click(function(e) {
            var filter = $("#payroll-grid").data("kendoGrid").dataSource.filter();
            $.ajax({
                url: '<?php echo base_url("payroll/excel") ;?>',
                data: {filter: filter, sort: {field:'jobouterCode', dir: 'asc'}},
                type: 'post',
                dataType: 'json',
                success: function(resp) {
                    var url = '<?php echo base_url("payroll/download") ;?>';
                    var win = window.open(url, '_self', '');
                    win.focus();
                }
            })
        });

    });
</script>