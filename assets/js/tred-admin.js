const tredAjaxUrl = tred_js_object.ajaxurl;
const tredWpnonce = tred_js_object._wpnonce;
const tredSliceNumber = tred_js_object.sliceNumber;
const tredActions = tred_js_object.tredActions;
const tredColors = tred_js_object.tredColors;
let chartStatus;

jQuery(document).ready(function ($) {

    $('#tred-easydash-tabs a.button').click(function () {
        var target = $(this).data('target-content');

        $('.tred-easydash-tab').hide();
        $('.tred-easydash-tab#' + target).show();

        $('#tred-easydash-tabs a.button').removeClass('active');
        $(this).addClass('active');
    });

    if($.notify !== undefined && $('button.tred-table-button').length > 0) {
        $.notify.addStyle('tred', {
            html: "<div><span data-notify-html/></div>",
            classes: {
                base: {
                    "white-space": "nowrap",
                    "background-color": "#D97706",
                    "padding": "5px",
                    "color": "white",
                    "width": "auto"
                }
            }
        });
        //copy, csv, print...buttons
        $('button.tred-table-button').click(function () {
            $(this).notify("Pro feature. <a href='https://wptrat.com/easy-dash-for-learndash?from=plugin_buttons'>Click to get it!</a>",{
                style: 'tred'
            });
        }); 
    } //end notify
    

    function tredDecodeAmp(item) {
        return item.replace(/&amp;/g, '&');
    }
  
    function tredInjectTopBoxes(objTopBoxes) {
        for (var [key, value] of Object.entries(objTopBoxes)) {
            $('#' + key).text(value);
        }
    } //end function

    function tredInjectTables(objTables) {
        let table;
        let thead_cols = '';
        let tbody_rows = '';
        let dt;
        for (let i = 0; i < objTables.length; i++) {
            table = objTables[i];
            //Columns
            for (const label of Object.values(table['keys_labels'])) {
                thead_cols += '<th class="text-left text-blue-900 border">' + label + '</th>';
            } //end for columns
            
            //Rows
            for (const row of Object.values(table['data'])) {
                tbody_rows += '<tr class="text-sm">';
                for (const key of Object.keys(table['keys_labels'])) {
                    tbody_rows += '<td class="border">' + row[key] + '</td>';    
                }
                tbody_rows += '</tr>'; 
            } //end for rows
            $('#' + table['id'] + ' thead tr').html(thead_cols);
            $('#' + table['id'] + ' tbody').html(tbody_rows);
            $('#obs-' + table['id']).text(table['obs']);

            tableOptions = {
                ordering: true,
                initComplete: function(settings, json) {
                    if(typeof tredPro !== 'undefined' && tredPro && typeof tredRebuildTablePro === "function") {
                        tredRebuildTablePro(this);
                    }  
                }
            }
            dt = $('#' + table['id']).DataTable(tableOptions); //end DataTable
        } //end for loop
    } //end function

    function tredInjectChartData(objChart, max = tredSliceNumber) {
        let chart,getLast;
        Chart.helpers.each(Chart.instances, function (instance) {
            for (let i = 0; i < objChart.length; i++) {
                chart = objChart[i];
                getLast = chart.hasOwnProperty('slice') && chart['slice'] === 'last';
                //slicing lables and data. if getLast, slice negative (get last {max} array items)
                chart['labels'] = (getLast) ? chart['labels'].slice(-Math.abs(max)) : chart['labels'].slice(0, max);
                for (let z = 0; z < chart['datasets'].length; z++) {
                    chart.datasets[z]['data'] = (getLast) ? chart['datasets'][z]['data'].slice(-Math.abs(max)) : chart['datasets'][z]['data'].slice(0, max);
                } //end inner for

                if (instance.canvas.getAttribute('id') === chart['id']) {
                    instance.data.labels = chart['labels'].map(tredDecodeAmp);
                    instance.data.datasets = chart['datasets'];
                    if(instance.options.indexAxis == 'y') {
                        chart['datasets'][0]['borderColor'] = tredColors.blue;
                        chart['datasets'][0]['backgroundColor'] = tredColors.blue_t;
                    }
                    instance.update();
                    if(chart['obs']) {
                        $('span#' + chart['id'] + '-obs').text(chart['obs']);
                    } //end if
                } //end if
            } //end outter for
        }); //end Chart each
    } //end function

    let tredAction;
    for (let i = 0; i < tredActions.length; i++) {
        tredAction = tredActions[i];

        // console.log(tredAction);

        $.ajax({
            url: tredAjaxUrl,
            type: 'get',
            dataType: "json",
            data: {
                'action': tredAction,
                '_wpnonce': tredWpnonce
            },
            success: function (response) {
                if (response.result !== 'success') {
                    console.log(response.action + ' => ' + response.result);
                } else {
                    //do the magic for each chart, top-box or table...

                    //top-boxes
                    if (response['data'].hasOwnProperty('top_boxes')) {
                        tredInjectTopBoxes(response['data']['top_boxes']);
                    }

                    //Charts
                    if (response['data'].hasOwnProperty('charts')) {
                        tredInjectChartData(response['data']['charts']); 
                    }

                    //Tables
                    if (response['data'].hasOwnProperty('tables')) {
                        tredInjectTables(response['data']['tables']);
                    }
                } //end if/else success
            } //end success callback
        }); //end ajax call
    } //end for loop

}); //end jquery