$(document).ready(function()
{

    var baseURL = $('#baseURL').html();
    var booksListURL = baseURL+'/serverSide/borrowedBooksList';

    $('#borrowed_books_datatable').DataTable({
        'processing': true,
        'serverSide': true,
        'order': [[ 4, "desc" ]],
        'ajax': {
            url: booksListURL,
            type: 'get',
            error: function() {}
        },
        'columns':[
            {'data':'title'},
            {'data':'author_name'},
            {'data':'isbn'},
            {'data':'borrow_start_date'},
            {'data':'return_date'},
            {'data':'fine'}
        ],
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
                .column(5)
                .data()
                .reduce(function (a, b) {
                    return intVal(a) + intVal(b);
                },0);

            //Total over this page
            var pageTotal = api
                .column(5, { page: 'current'} )
                .data()
                .reduce(function (a, b) {
                    return intVal(a) + intVal(b);
                },0);

            // Update footer
            $(api.column(5).footer()).html(pageTotal +' ( '+ total +' total)');

            //set the total to the free-slot in the header
            var freeSlotLink = $('.free-slot-link-1');
            freeSlotLink.text('Your total fine: '+total);
            freeSlotLink.css('color','red');
        }
    });
});