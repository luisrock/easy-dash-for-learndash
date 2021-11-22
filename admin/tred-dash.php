<div class="main-content flex-1 bg-gray-100 mt-12 md:mt-2 pb-24 md:pb-5 tred-content tred-main-content tred-easydash-tab"
    id="tred-easydash-tab-dash">

    <div class="rounded-tl-3xl bg-gray-800">
        <div class="rounded-tl-3xl bg-gradient-to-r from-green-900 to-gray-800 p-4 shadow text-2xl text-white">
            <h3 class="font-bold pl-2 flex justify-between text-white">Easy Dash for LearnDash
                <span class="font-thin"><a href="#">WP TRAT</a></span>
            </h3>
        </div>
    </div>

    <div class="flex flex-wrap tred-top-banners">
    <!-- TOP-BOXES -->
        <?php foreach(TRED_TOP_BOXES as $box) {  
            if(in_array($box['widget_name'],TRED_TOP_BOXES_TO_HIDE)) {continue;}
            tred_template_mount_box($box);
        } //end foreach ?>
    </div>
    <!-- end TOB-BOXES -->


    <div class="flex flex-row flex-wrap flex-grow mt-2 tred-charts">
        <!-- CHARTS -->
        <?php foreach(TRED_CHARTS as $chart) { 
            if(in_array($chart['widget_name'],TRED_CHARTS_TO_HIDE)) {continue;}
            tred_template_mount_chart($chart);
        } //end foreach(TRED_CHARTS as $chart) ?>
     </div>

     <div class="flex flex-row flex-wrap flex-grow mt-2 tred-tables">
        <!-- TABLES -->
        <?php foreach(TRED_TABLES as $table) { 
            if(in_array($table['widget_name'],TRED_TABLES_TO_HIDE)) {continue;}
            tred_template_mount_table($table);
        } //end foreach(TRED_TABLES as $table) ?>
    </div>
    <!-- end tred-charts -->

</div>