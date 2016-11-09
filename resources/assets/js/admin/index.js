$(document).ready(function()
{
    $('.search-select').select2({
        theme: 'bootstrap',
        dropdownParent: $("#search_parameter_container")
    });

    var baseURL = $('#baseURL').html();
    var booksListURL = baseURL+'/serverSide/borrowStatistics';

    $('#books_loans_datatable').DataTable({
        'processing': true,
        'serverSide': true,
        'order': [[ 0, "desc" ]],
        'ajax': {
            url: booksListURL,
            type: 'get',
            error: function() {},
            data:function(d)
            {
                //get all the search parameters
                d.title = $('#title').val();
                d.author_id = $('#author_id').val();
                d.isbn = $('#isbn').val();
                d.status = $('#status').val();
                d.user_id = $('#user_id').val();
            }
        },
        'columns':[
            {'data':'title'},
            {'data':'author_name'},
            {'data':'isbn'},
            {'data':'status'},
            {'data':'borrow_start_date'},
            {'data':'date_returned'},
            {'data':'borrower'},
            {'data':'fine'}
        ],
        'drawCallback':function(settings)
        {
            var api = this.api();
            var rowCount = api.data().count();

            if(rowCount>0)
            {
                $('#download_borrow_report').show();
            }

            else
            {
                $('#download_borrow_report').hide();
            }
        },
        'footerCallback':function(row, data, start, end, display)
        {
            var api = this.api();

            // Remove the formatting to get integer data for summation
            var intVal = function (i) {
                return typeof i === 'string' ?
                i.replace(/[\$,]/g, '')*1 :
                    typeof i === 'number' ?
                        i : 0;
            };

            // Total over all pages
            var total = api
                .column(7)
                .data()
                .reduce(function (a, b) {
                    return intVal(a) + intVal(b);
                },0);

            //Total over this page
            var pageTotal = api
                .column(7, { page: 'current'} )
                .data()
                .reduce(function (a, b) {
                    return intVal(a) + intVal(b);
                },0);

            // Update footer
            $(api.column(7).footer()).html(pageTotal +' ( '+ total +' total)');
        }
    });

    $('#generate_report').click(function()
    {
        var reportsDataTable = $('#books_loans_datatable').DataTable();

        //remove the currently selected ordering
        //reportsDataTable.order([]);
        reportsDataTable.ajax.reload();
    });
});
