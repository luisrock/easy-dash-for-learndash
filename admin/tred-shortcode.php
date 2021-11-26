<div class="main-content flex-1 bg-gray-100 mt-12 md:mt-2 pb-24 md:pb-5 tred-main-content tred-easydash-tab flex flex-wrap"
    id="tred-easydash-tab-shortcode" style="display:none">

    <div class="wrap tred-wrap-grid flex-auto">
        <div class="tred-form-fields"> 
            <h1>Shortcode <span><code>[easydash]</code></span></h1> 
            
        <p style="font-size: 1.2em;margin-bottom: 20px;">
            Publish your dash on the frontend! Use one or more shortcodes on a post, page or custom post type. Check the parameters below: 
        </p>

            <?php if(!TRED_PRO_ACTIVATED) { ?>
            <div class="notice notice-error is-dismissible tred-pro-notice">
                <p>
                    This is a <strong>premium feature</strong> and you don't have the <a href="https://wptrat.com/easy-dash-for-learndash?from=plugin">Easy Dash for Learndash Pro</a> add-on installed and/or activated. Please click <a
                        href="https://wptrat.com/easy-dash-for-learndash?from=plugin">here</a> and get it, otherwise the shortcode will not work at all.
                </p>
            </div>
            <?php } ?>
            
            <div class="tred-shortcode-instructions mt-12 md:mt-2 pb-24 md:pb-5">
            <table class="shadow-lg bg-white">
                <tr>
                    <th class="bg-blue-100 border text-left px-8 py-4">PARAMETER</th>
                    <th class="bg-blue-100 border text-left px-8 py-4">POSSIBLE VALUES</th>
                    <th class="bg-blue-100 border text-left px-8 py-4">DEFAULT</th>
                </tr>
                <tr>
                    <td class="border px-8 py-4">
                        <strong>types</strong>
                    </td>
                    <td class="border px-8 py-4">
                        <code>'box'</code>, <code>'chart'</code> and/or <code>'table'</code> (comma separated). <br>* don't use it if you want to display all types
                    </td>
                    <td class="border px-8 py-4">
                        all types will be displayed
                    </td>
                </tr>

                <tr>
                    <td class="border px-8 py-4">
                        <strong>show</strong>
                    </td>
                    <td class="border px-8 py-4">
                        widget number (or name) or comma separated list of numbers (or names). <br>* don't use it if you want to display all widgets
                    </td>
                    <td class="border px-8 py-4">
                        all widgets will be displayed
                    </td>
                </tr>

                <tr>
                    <td class="border px-8 py-4">
                        <strong>hide</strong>
                    </td>
                    <td class="border px-8 py-4">
                        widget number (or name) or comma separated list of numbers (or names).
                    </td>
                    <td class="border px-8 py-4">
                        no widget will be hidden
                    </td>
                </tr>

                <tr>
                    <td class="border px-8 py-4">
                        <strong>table_buttons</strong>
                    </td>
                    <td class="border px-8 py-4">
                        buttons names: <code>'copy'</code>, <code>'csv'</code> ,<code>'excel'</code> ,<code>'pdf'</code> ,<code>'print'</code> (comma separated).<br>* Use <code>'all'</code> if you want to display all buttons.
                    </td>
                    <td class="border px-8 py-4">
                        no button will be displayed
                    </td>
                </tr>
                                
            </table>

            
        </div>

        <div>
            <p style="font-weight:800">Examples:</p>
            <br>
            <p><code>[easydash]</code></p>
            <p>Will display all widgets from all types</p>
            <br>
            <p>
                <code>[easydash types="table" show="301"]</code> or 
                <code>[easydash types="table" show="table_completion_course_stats"]</code>
            </p>
            <p>
                Will display only one widget (table_completion_course_stats), with no table button.
            </p>
            <br>
            <p>
                <code>[easydash hide="108,109"]</code> or
                <code>[easydash hide="box_course_enrolls,box_course_starts]</code>
            </p>
            <p>Will display all widgets from all types, except course enrolls and starts boxes </p>
            <br>
            <p>
                <code>[easydash types="box,chart"]</code>
            </p>
            <p>Will display all boxes and charts type widgets</p>
            <br>
            <p>
                <code>[easydash table_buttons="all"]</code>
            </p>
            <p>Will display all boxes, charts and tables, with all table buttons in each table</p>
        </div>

        </div>
    

    </div> <!-- end tred-wrap-grid -->

    <div class="wrap tred-wrap-grid tred-wrap-table-widget-names flex-auto">
        <div class="tred-form-fields"> 

        <h1>Available Widgets</h1>    
            
            <table class="shadow-lg bg-white">
                <tr>
                    <th class="bg-blue-100 border text-left px-8 py-4">NUMBER</th>
                    <th class="bg-blue-100 border text-left px-8 py-4">NAME</th>
                    <!-- <th class="bg-blue-100 border text-left px-8 py-4">TITLE</th> -->
                    <th class="bg-blue-100 border text-left px-8 py-4">TYPE</th>
                </tr>
                <?php foreach([TRED_TOP_BOXES,TRED_CHARTS,TRED_TABLES] as $widget_group) { ?>
                    <?php foreach($widget_group as $w) { ?>
                <tr>
                    <td class="border px-8 py-4">
                        <strong><?php echo esc_html($w['number']); ?></strong>
                    </td>
                    <td class="border px-8 py-4">
                        <?php echo esc_html($w['widget_name']); ?>
                    </td>
                    <!-- <td class="border px-8 py-4">
                        <?php //echo esc_html($w['title']); ?>
                    </td> -->
                    <td class="border px-8 py-4">
                        <?php echo esc_html($w['widget_type']); ?>
                    </td>
                </tr>
                    <?php } ?>
                <?php } ?>
                                
            </table>

            
        </div>

    </div> <!-- end tred-wrap-grid -->
    


</div>