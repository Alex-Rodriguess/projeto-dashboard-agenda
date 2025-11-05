var valoresDecimalNegativo = {
    digitGroupSeparator: '.',
    decimalCharacter: ',',
    currencySymbol: 'R$ ',
    maximumValue: "9999999999999.99",
    minimumValue: "-9999999999999.99"
};


$(window).on("load", function () {
    $('#dvLoading').fadeOut(500);


    $('table#listagem2').find('tr > td').on('click', function (e) {
        var soma = 0;
        var row = $(e.target).parent();
        row.toggleClass('autoSum');

        row.find('td').toggleClass('bg-selected');

        row.parent().find('.autoSum').each(function (i, tr) {
            var columnDeb;
            var columnCre;

            if ($(tr).find('td').length === 3) {
                columnDeb = $(tr).find('td:nth-child(2)').html();
                columnCre = $(tr).find('td:nth-child(3)').html();
            } else {
                columnDeb = $(tr).find('td:nth-child(3)').html();
                columnCre = $(tr).find('td:nth-child(4)').html();
            }
            soma += (parseFloat(columnDeb.replace('R$ ', '').replace(/\./g, '').replace(',', '.')) || 0);
            soma += (parseFloat(columnCre.replace('R$ ', '').replace(/\./g, '').replace(',', '.')) || 0);
        });

        row.parent().parent().find('thead > tr > th > input.soma, tfoot > tr > th > input.soma').autoNumeric('set', soma);

    });
    $('input.soma').autoNumeric('init', valoresDecimalNegativo).attr('placeholder', 'R$ 0,00');
});