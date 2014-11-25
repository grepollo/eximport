<script>
    var gridDiv = $("<div/>");
    $(document).ready(function(){
        $("#detail-content").append(gridDiv);
        gridDiv.kendoGrid({
            dataSource: new kendo.data.DataSource({
                transport: {
                    read: {
                        url: '<?php echo base_url("job_outer/read?format=json"); ?>',
                        dataType: "json"
                    },
                    update: {
                        url: '<?php echo base_url("job_outer/update?format=json") ;?>',
                        dataType: "json",
                        type: "POST"
                    },
                    destroy: {
                        url: '<?php echo base_url("job_outer/delete?format=json") ;?>',
                        dataType: "json",
                        type: "POST"
                    },
                    create: {
                        url: '<?php echo base_url("job_outer/create?format=json") ;?>',
                        dataType: "json",
                        type: "POST"
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
                },
                pageSize: 20,
                serverPaging: true,
                serverSorting: true,
                serverFiltering: true
            }),
            scrollable: true,
            pageable: true,
            toolbar: [{name: "create", text: "Add new jobouter"}],
            editable: {
                mode:"popup",
                confirmation: "Are you sure you want to delete this jobouter?"
            },
            columns: [{
                command: [{name: "edit", text: ""},{name: "destroy", text: ""}],
                title: "&nbsp;",
                width: "82px"
            },{
                field: "jobouterCode",
                title: "Code",
                width: "100px"
            },{
                field: "jobouterFirstname",
                title: "First Name",
                width: "120px"
            },{
                field: "jobouterLastname",
                title: "Last Name",
                width: "120px"
            },{
                field: "jobouterContactNo",
                title: "Contact No",
                width: "100px"
            },{
                field: "jobouterAddress",
                title: "Address",
                width: "150px"
            }]
        });
    });
</script>