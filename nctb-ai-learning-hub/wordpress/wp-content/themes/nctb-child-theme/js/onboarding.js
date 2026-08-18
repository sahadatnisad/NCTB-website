/**
 * NCTB Learning Hub — Student Onboarding Script
 *
 * Handles client-side multi-step wizard, REST API communication, validation,
 * and state persistence for resuming onboarding.
 */

(function($) {
    'use strict';

    $(document).ready(function() {
        var $app = $('#nctb-onboarding-app');
        if (!$app.length || typeof nctbData === 'undefined') {
            return;
        }

        var currentStep = parseInt($app.data('step') || 1, 10);
        if (currentStep < 1 || currentStep > 4) {
            currentStep = 1;
        }

        // Show the current active step on initial load (Resumability)
        showStep(currentStep);

        function showAlert(msg, isError) {
            var $alert = $('#nctb-alert');
            $alert.removeClass('alert-error alert-success')
                  .addClass(isError ? 'alert-error' : 'alert-success')
                  .html(msg)
                  .fadeIn();
            $('html, body').animate({ scrollTop: $app.offset().top - 20 }, 300);
        }

        function clearAlert() {
            $('#nctb-alert').hide().empty();
        }

        function showStep(stepNum) {
            clearAlert();
            $('.onboarding-step-view').hide();
            $('#step-' + stepNum + '-view').fadeIn(250);

            // Update Stepper UI
            $('.step-indicator').removeClass('active completed');
            $('.step-indicator').each(function() {
                var s = parseInt($(this).data('step'), 10);
                if (s === stepNum) {
                    $(this).addClass('active');
                } else if (s < stepNum) {
                    $(this).addClass('completed');
                }
            });

            currentStep = stepNum;
        }

        function sendStepData(stepNum, data, callback) {
            clearAlert();
            var $activeBtn = $('#step-' + stepNum + '-view button.nctb-btn-primary, #btn-step-4-complete');
            var originalText = $activeBtn.text();
            $activeBtn.prop('disabled', true).text('সংরক্ষণ হচ্ছে (Saving)...');

            $.ajax({
                url: nctbData.root + 'nctb/v1/student/onboarding/step?step=' + stepNum,
                method: 'POST',
                beforeSend: function(xhr) {
                    xhr.setRequestHeader('X-WP-Nonce', nctbData.nonce);
                },
                contentType: 'application/json',
                data: JSON.stringify(data),
                success: function(response) {
                    $activeBtn.prop('disabled', false).text(originalText);
                    if (callback) {
                        callback(null, response);
                    }
                },
                error: function(xhr) {
                    $activeBtn.prop('disabled', false).text(originalText);
                    var errMsg = 'একটি ত্রুটি ঘটেছে। অনুগ্রহ করে আবার চেষ্টা করুন।';
                    if (xhr.responseJSON && xhr.responseJSON.message) {
                        errMsg = xhr.responseJSON.message;
                    }
                    showAlert('⚠️ ' + errMsg, true);
                    if (callback) {
                        callback(xhr);
                    }
                }
            });
        }

        // --- Step 1: Education Level Next ---
        $('#btn-step-1-next').on('click', function(e) {
            e.preventDefault();
            var level = $('input[name="education_level"]:checked').val();
            var session = $('#class_session').val();

            if (!level) {
                showAlert('⚠️ অনুগ্রহ করে আপনার শ্রেণি বা শিক্ষাস্তর নির্বাচন করুন।', true);
                return;
            }

            sendStepData(1, { education_level: level, class_session: session }, function(err, res) {
                if (!err) {
                    showStep(2);
                }
            });
        });

        // --- Step 2: Subject Selection Next ---
        $('#btn-step-2-next').on('click', function(e) {
            e.preventDefault();
            var chosen = [];
            $('input[name="chosen_subjects[]"]:checked').each(function() {
                chosen.push($(this).val());
            });

            if (chosen.length === 0) {
                showAlert('⚠️ অনুগ্রহ করে অন্তত একটি বিষয় বেছে নিন।', true);
                return;
            }

            sendStepData(2, { chosen_subjects: chosen }, function(err, res) {
                if (!err) {
                    showStep(3);
                }
            });
        });

        // --- Step 3: Explanation Language Preference Next ---
        $('#btn-step-3-next').on('click', function(e) {
            e.preventDefault();
            var lang = $('input[name="explanation_language"]:checked').val();
            if (!lang) {
                lang = 'bilingual';
            }

            sendStepData(3, { explanation_language: lang }, function(err, res) {
                if (!err) {
                    showStep(4);
                }
            });
        });

        // --- Step 4: Complete Onboarding ---
        $('#btn-step-4-complete').on('click', function(e) {
            e.preventDefault();
            var targetExam = $('#target_exam_session').val();

            sendStepData(4, { target_exam_session: targetExam }, function(err, res) {
                if (!err) {
                    // Complete onboarding and redirect
                    $.ajax({
                        url: nctbData.root + 'nctb/v1/student/onboarding/complete',
                        method: 'POST',
                        beforeSend: function(xhr) {
                            xhr.setRequestHeader('X-WP-Nonce', nctbData.nonce);
                        },
                        success: function(completeRes) {
                            showAlert('🎉 অভিনন্দন! আপনার শিক্ষার্থী প্রোফাইল সম্পন্ন হয়েছে। ড্যাশবোর্ডে নেওয়া হচ্ছে...', false);
                            setTimeout(function() {
                                window.location.href = completeRes.redirect_url || nctbData.dashboardUrl;
                            }, 1000);
                        },
                        error: function() {
                            window.location.href = nctbData.dashboardUrl;
                        }
                    });
                }
            });
        });

        // --- Navigation Buttons (Previous) ---
        $('.btn-prev').on('click', function(e) {
            e.preventDefault();
            var targetStep = parseInt($(this).data('target'), 10);
            if (targetStep >= 1 && targetStep <= 4) {
                showStep(targetStep);
            }
        });
    });

})(jQuery);
