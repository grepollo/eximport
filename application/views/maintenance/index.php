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
<h3 class="tm-article-subtitle">Maintenance</h3>
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
                    text: "Buyer", imageUrl: "",
                    url: '<?php echo base_url("maintenance/buyer"); ?>'

                },
                {
                    text: "Item", imageUrl: "",
                    url: '<?php echo base_url("maintenance/item"); ?>'
                },
                {
                    text: "Job Outer", imageUrl: "",
                    url: '<?php echo base_url("maintenance/job_outer"); ?>'
                },
                {
                    text: "Unit", imageUrl: "",
                    url: '<?php echo base_url("maintenance/unit"); ?>'
                },
                {
                    text: "Invoice Category", imageUrl: "",
                    url: '<?php echo base_url("maintenance/inv_category"); ?>'
                },
                {
                    text: "Invoice Type", imageUrl: "",
                    url: '<?php echo base_url("maintenance/inv_type"); ?>'
                }
            ],
            select: onSelect
        });
    });
</script>