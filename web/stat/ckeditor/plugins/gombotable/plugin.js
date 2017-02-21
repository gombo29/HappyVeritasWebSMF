CKEDITOR.plugins.add('gombotable', {
    icons: 'gombotable',
    init: function (editor) {


        editor.addCommand('insertTariffTable', {
            exec: function (editor) {
                editor.insertHtml(`
                <table class="table" border="1" cellpadding="1" cellspacing="1">
                    <tbody>
                        <tr>
                            <td colspan="2" rowspan="1" class="transaction-header">Transaction limitation , MMK</td>
                            <td class="transaction-amount">Amount, MMK</td>
                        </tr>
                        <tr>
                            <td class="min-max">Min</td>
                            <td class="min-max">Max</td>
                            <td class="fee">Fee</td>
                        </tr>
                        <tr>
                            <td>&nbsp;</td>
                            <td>&nbsp;</td>
                            <td>&nbsp;</td>
                        </tr>
                        <tr>
                            <td>&nbsp;</td>
                            <td>&nbsp;</td>
                            <td>&nbsp;</td>
                        </tr>
                        <tr>
                            <td>&nbsp;</td>
                            <td>&nbsp;</td>
                            <td>&nbsp;</td>
                        </tr>
                        <tr>
                            <td>&nbsp;</td>
                            <td>&nbsp;</td>
                            <td>&nbsp;</td>
                        </tr>
                    </tbody>
                </table>
                `)
                ;
            }
        });

        editor.ui.addButton('Timestamp', {
            label: 'Insert tariff table',
            command: 'insertTariffTable',
            toolbar: 'insert',
            icon: this.path + 'images/tarifftable.png'
        });
    }
});