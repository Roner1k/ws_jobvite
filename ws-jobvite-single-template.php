<?php //get_header(); ?>
<div class="ws-jobvite-single-container">
    <?php
    
    if (!isset($pathQuery) || empty($params['jobID']) || count($cJob) == 0) {
        echo "<div class='jv-not-found-message'><h1>Sorry, this position is no longer available</h1><p>Please return to the actual job postings</p> <div><a class='et_pb_button' href='$careers_page_slug'>Current Opportunities</a></div></div>";
    } else {
        $cJob = $cJob[0]; ?>
        <div class="et_pb_section et_section_regular ws-jv-inner-banner">
            <div class="et_pb_row et_pb_row_0 jv-row1">
                <div class="et_pb_text_inner ws-jv-back-button">
                    <a class='et_pb_button simple-btn' href='<?php echo $careers_page_slug; ?>'>Back to Jobs</a>
                </div>
            </div>

            <div class="et_pb_row et_pb_row_1 jv-row2">
                <div class="et_pb_column et_pb_column_0 jv-col1">
                    <div class="et_pb_module et_pb_text et_pb_text_0">

                        <div class="et_pb_text_inner">
                            <h1><?php ws_echo($cJob->title); ?></h1>
                        </div>
                        <div class="et_pb_text_inner ws-jv-apply-button">
                            <a class='et_pb_button simple-btn open-popup-link' href='#ws-jv-apply-popup'>Apply</a>
                        </div>

                    </div>
                </div>
                <div class="et_pb_column et_pb_column_0 jv-col2">
                    <div class="et_pb_module et_pb_text et_pb_text_0">
                        <div class="et_pb_text_inner jv-stat">
                            <p class="jv-title">Location</p>
                            <p class="jv-value"><?php
                                if (ws_echo_if($cJob->location) && ws_echo_if($cJob->locationState)) {
                                    echo $cJob->location . '/' . $cJob->locationState;
                                } elseif (ws_echo_if($cJob->location)) {
                                    echo $cJob->location;
                                } elseif (ws_echo_if($cJob->locationState)) {
                                    echo $cJob->locationState;
                                } else {
                                    echo '-';
                                } ?>
                            </p>
                        </div>
                        <?php //if (ws_echo_if($cJob->department)): ?>
                        <div class="et_pb_text_inner jv-stat">
                            <p class="jv-title">Department</p>
                            <p class="jv-value"><?php echo (ws_echo_if($cJob->department)) ? $cJob->department : ws_echo($cJob->category); ?></p>
                        </div>
                        <?php //endif; ?>
                        <?php //if (ws_echo_if($cJob->jobType)): ?>
                        <div class="et_pb_text_inner jv-stat">
                            <p class="jv-title">Status</p>
                            <p class="jv-value"><?php ws_echo($cJob->jobType); ?></p>
                        </div>
                        <?php //endif; ?>


                    </div>
                </div>


            </div>


        </div>

        <div class="et_pb_section et_section_regular ws-jv-job-content">
            <div class="et_pb_row et_pb_row_0">
                <div class="et_pb_column et_pb_column_0">
                    <div class="et_pb_module et_pb_text et_pb_text_0 jv-post-content">

                        <div class="et_pb_text_inner ">
                            <?php ws_echo($cJob->description); ?>
                        </div>

                    </div>
                    <div class="et_pb_module">
                        <div class="et_pb_text_inner ws-jv-apply-button">
                            <a class='et_pb_button simple-btn open-popup-link' href='#ws-jv-apply-popup'>Apply</a>
                        </div>
                    </div>

                    <div class="et_pb_module ws-jv-social-links">
                        <?php dynamic_sidebar('ws_jobvite_share'); ?>

                    </div>
                </div>

            </div>
        </div>

        <div id="ws-jv-apply-popup" class="white-popup mfp-hide">
            <div class="ws-jv-iframe-wrap">

                <?php
                echo (!isset($cJob->applyLink) || empty($cJob->applyLink)) ?
                    "<h3>Apply form Not available at this moment<br>Please <a href='/contact-us/'>Contact Us</a></h3>" :
                    "<iframe src='$cJob->applyLink' style='width:100%;min-height:95vh;'></iframe>"; ?>
                <!--                <iframe src="-->
                <?php //echo $cJob->applyLink; ?><!--" style="width:100%;min-height:95vh; "></iframe>-->

            </div>
        </div>
        <div class="jv-hidden-job-item">
            <div id="ws-jv-page-title"></div>
        </div>

        <?php
        //        echo '<pre>';
        //        var_dump($cJob);
        //        echo '</pre>';
    } ?>
</div>
