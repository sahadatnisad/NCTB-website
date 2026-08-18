// Dashboard interactions
jQuery(document).ready(function($) {
    // Smooth scrolling for navigation
    $('.dashboard-nav a').click(function(e) {
        e.preventDefault();
        var target = $(this).attr('href');
        if (target.startsWith('#')) {
            var $target = $(target);
            if ($target.length) {
                $('html, body').animate({
                    scrollTop: $target.offset().top - 100
                }, 500);
            }
        }
    });

    // Progress bar animation
    function animateProgress() {
        $('.progress-fill').each(function() {
            var $this = $(this);
            var width = $this.data('width') || $this.width();
            $this.css('width', width + '%');
        });
    }

    animateProgress();

    // Update progress via AJAX
    window.updateProgress = function(percentage) {
        var lessonId = $('article').data('lesson-id');
        if (!lessonId) return;

        $.ajax({
            url: ajaxurl,
            type: 'POST',
            data: {
                action: 'nctb_update_lesson_progress',
                lesson_id: lessonId,
                progress: percentage,
                nonce: nctb_ajax_nonce
            },
            success: function(response) {
                if (response.success) {
                    $('#progress-fill').css('width', percentage + '%');
                    $('#progress-percentage').text(percentage + '%');
                    // Update dashboard progress
                    setTimeout(function() {
                        location.reload();
                    }, 500);
                }
            }
        });
    };

    // Language switcher
    $('#nctb_language_select').change(function() {
        var selectedLanguage = $(this).val();
        $.ajax({
            url: ajaxurl,
            type: 'POST',
            data: {
                action: 'nctb_set_language',
                language: selectedLanguage,
                nonce: nctb_ajax_nonce
            },
            success: function(response) {
                if (response.success) {
                    // Reload the page to apply the language change
                    location.reload();
                }
            }
        });
    });
});