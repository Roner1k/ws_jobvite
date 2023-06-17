jQuery(document).ready(function ($) {
    let $jForm = $('.ws-jv-filter-form');


    $jForm.on('submit', function (e) {
        e.preventDefault();
        let filterDepVal = $($jForm).find('#jv_departments').val();
        let filterLocVal = $($jForm).find('#jv_locations').val();
        let filterRoleVal = $($jForm).find('#jv_roles').val();


        let openPos = 0;
        $('.jv-job-item').each(function () {
            let showAllDeps = filterDepVal == 'all';
            let showDeps = $(this).attr('data-jvfilter-dep') == filterDepVal;
            let showAllLocs = filterLocVal == 'all';
            let showLocs = $(this).attr('data-jvfilter-loc') == filterLocVal;
            let showAllRoles = filterRoleVal == 'all';
            let showRoles = $(this).attr('data-jvfilter-role') == filterRoleVal;

            $(this).addClass('jv-hidden-job-item');

            if ((showDeps || showAllDeps) && (showLocs || showAllLocs) && (showRoles || showAllRoles)) {
                openPos++;
                $(this).removeClass('jv-hidden-job-item')

            }
        })
        $('.jv-jobs-count-value').text(openPos);
        $('.jv-jobvite-post-grid').attr('data-ws-jv-qty', openPos)

    })

    // ws-jv-apply-button
    $('.ws-jv-apply-button .et_pb_button').magnificPopup({
        type: 'inline',
        midClick: true // Allow opening popup on middle mouse click. Always set it to true if you don't provide alternative source in href.
    });

    document.title = $('.ws-jv-inner-banner h1').text() + ' - ' + document.title;

});