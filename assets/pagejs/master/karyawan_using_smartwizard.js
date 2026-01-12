/*
 * Created by PhpStorm.
 *  * User: FIKY-PC
 *  * Date: 12/2/20, 2:32 PM
 *  * Last Modified: 12/2/20, 2:32 PM.
 *  Developed By: Fiky Ashariza Powered By PhpStorm
 *  Copyright© 2020 .All rights reserved.
 *
 */



// +++++++++++++++++++++++++++++++++++++++++++++++++++++ RANAH PERMISSION ++++++++++++++++++++++++++++++++++++++++//

$(document).ready(function() {

    // Toolbar extra buttons
    var btnFinish = $('<button></button>').text('Sudah')
        .addClass('btn btn-info')
        .on('click', function(){ alert('Finish Clicked'); });
    var btnCancel = $('<button></button>').text('Cancel')
        .addClass('btn btn-danger')
        .on('click', function(){ $('#smartwizard').smartWizard("reset"); });
    var btnPreview = $('<button></button>').text('Preview')
        .addClass('btn btn-warning')
        .on('click', function(){ $('#smartwizard').smartWizard("prev"); });
    var btnNext = $('<button></button>').text('Next')
        .addClass('btn btn-warning')
        .on('click', function(){
            var validator = $('#step-karyawan').data('bootstrapValidator');
            validator.validate();
            if (validator.isValid()) {
                console.log('VALID DATA');
                $('#smartwizard').smartWizard("next");
            }
        });

    // Step show event
    $("#smartwizard").on("showStep", function(e, anchorObject, stepNumber, stepDirection, stepPosition) {
        $("#prev-btn").removeClass('disabled');
        $("#next-btn").removeClass('disabled');
        if(stepPosition === 'first') {
            $("#prev-btn").addClass('disabled');
        } else if(stepPosition === 'last') {
            $("#next-btn").addClass('disabled');
        } else {
            $("#prev-btn").removeClass('disabled');
            $("#next-btn").removeClass('disabled');
        }
        console.log('Step Position ' + stepPosition );
        console.log('Step Number ' + stepNumber );
    });

    // Smart Wizard
    $('#smartwizard').smartWizard({
        selected: 0,
        theme: 'arrows', // default, arrows, dots, progress
        // darkMode: true,
        transition: {
            animation: 'slide-horizontal', // Effect on navigation, none/fade/slide-horizontal/slide-vertical/slide-swing
        },
        toolbarSettings: {
            toolbarPosition: 'bottom', // both bottom
            toolbarExtraButtons: [ btnPreview, btnNext, btnFinish, btnCancel]
        }
    });

    // External Button Events
    $("#reset-btn").on("click", function() {
        // Reset wizard
        $('#smartwizard').smartWizard("reset");
        return true;
    });

    $("#prev-btn").on("click", function() {
        // Navigate previous
        $('#smartwizard').smartWizard("prev");
        return true;
    });

    $("#next-btn").on("click", function() {
        // Navigate next
        $('#smartwizard').smartWizard("next");
        return true;
    });


    // Demo Button Events
    $("#got_to_step").on("change", function() {
        // Go to step
        var step_index = $(this).val() - 1;
        $('#smartwizard').smartWizard("goToStep", step_index);
        return true;
    });

    var options = {
        justified: true,
        transition: {
            animation: 'slide-vertical',
        },
        theme: 'arrows',
    };

    $('#smartwizard').smartWizard("setOptions", options);

// CUSTOM AJAX AREA
    $('#step-karyawan').bootstrapValidator({
        message: 'This value is not valid',
        feedbackIcons: {
            valid: 'glyphicon glyphicon-ok',
            invalid: 'glyphicon glyphicon-remove',
            validating: 'glyphicon glyphicon-refresh'
        },
        fields: {
            nmlengkap: {
                validators: {
                    notEmpty: {
                        message: 'The field can not be empty'
                    },
                }
            },
            nickname: {
                validators: {
                    notEmpty: {
                        message: 'The field can not be empty'
                    }
                }
            },
            jk: {
                validators: {
                    notEmpty: {
                        message: 'The field can not be empty'
                    }
                }
            }
        },
        excluded: [':disabled']
    });

    $(".sw-btn-next").hide();
    $(".sw-btn-prev").hide();


});