$(document).ready(function () {
    getResults();
});

function getResults() {
   $("#results-table").DataTable({
        "responsive": false,
        "paging" : false,
        "autoWidth": true,
        "destroy": true,
        "scrollX": true,
       
        
        "ajax": {
            "url": urlresult,
            "dataSrc": 'data'
        },
        "columns": [
            { data: "id_number" },
            {
                data: "name"},
            { data: "course" },
            { data: "raw_score_t", render: data => `<strong>${data}</strong>` },
            { data: "sai_t" },
            { data: "percentile_ranks_pba" },
            { data: "stanine_pba" },
            { data: "percentile_ranks_pbg" },
            { data: "stanine_pbg" },
            { data: "verbalComprehension_score" },
            { data: "rsc2pc_vc"
             },
            { data: "verbalReasoning_score" },
            { data: "rsc2pc_vr",

             },
            { data: "verbal_score", render: data => `<strong>${data}</strong>` },
            { data: "quantitativeReasoning_score" },
            { data: "rsc2pc_qr",
               
             },
            { data: "figuralReasoning_score" },
            { data: "rsc2pc_fr",
               
             },
            { data: "non_verbal_score", render: data => `<strong>${data}</strong>` },
            {
                data: null, render: function (data, type, row) {
                    return `<div style="text-align:center;">
                        <a href="javascript:void(0);" class="print" data-id="${row.id_number}">
                            <i class="fa fas fa-print text-primary"></i>
                        </a> 
                    </div>`;
                }
            }
        ],
     
        
    });}