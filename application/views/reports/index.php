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
</style>
<h3 class="tm-article-subtitle">Reports</h3>
<div class="uk-grid" data-uk-grid-margin="">
    <div class="uk-width-medium-1-4">
        <div id="panel">
        </div>
    </div>
    <div class="uk-width-medium-3-4" id="detail-content">
    </div>
</div>
<script>

    function onSelect(e) {
        e.preventDefault();
        var header = $(e.item).find("> .k-link").text(),
            url = $(e.item).find("> .k-link").attr('href');
        $("#detail-content").empty();

        if(url) {
            $.ajax({
                url: url,
                type: 'GET',
                dataType: 'html',
                success: function(data) {
                    $("#detail-content").append(data);
                },
                error: function(data) {
                    console.log('error');
                }
            });
        }
    }

    $(document).ready(function() {
        $("#panel").kendoPanelBar({
            dataSource: [
                {
                    text: "Unpaid Jobouter", imageUrl: "",
                    url: '<?php echo base_url("reports/unpaid"); ?>'

                },{
                    text: "Payroll", imageUrl: "",
                    url: '<?php echo base_url("reports/payroll"); ?>'
                }
                /*,{
                    text: "Deliveries", imageUrl: "",
                    url: '<?php echo base_url("reports/deliveries"); ?>'
                }*/
            ],
            select: onSelect
        });
    });
</script>