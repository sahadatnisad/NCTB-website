document.addEventListener('DOMContentLoaded', function() {
    // Handle lesson deletion
    document.querySelectorAll('.lesson-actions button').forEach(button => {
        button.addEventListener('click', function(e) {
            e.preventDefault();
            const lessonId = this.dataset.lessonId;
            const button = this;

            if (confirm('Are you sure you want to delete this lesson?')) {
                button.innerHTML = 'Deleting...';
                button.classList.add('deleting');

                fetch(ajaxurl, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/x-www-form-urlencoded'
                  },
                    body: new URLSearchParams({
                        action: 'nctb_delete_lesson',
                        lesson_id: lessonId,
                        nonce: nctb_ajax_nonce
                    })
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        // Remove the lesson card
                        const card = button.closest('.lesson-card');
                        card.style.opacity = '0';
                        setTimeout(() => card.remove(), 300);
                        showToast('Lesson deleted successfully');
                    } else {
                        showToast(data.data || 'Failed to delete lesson', 'error');
                        button.innerHTML = 'Delete';
                        button.classList.remove('deleting');
                    }
                })
                .catch(error => {
                    showToast('Network error', 'error');
                    button.innerHTML = 'Delete';
                    button.classList.remove('deleting');
                });
            }
        });
    });

    // Handle enrollment/unenrollment
    document.querySelectorAll('.enrollment-toggle').forEach(button => {
        button.addEventListener('click', function(e) {
            e.preventDefault();
            const lessonId = this.dataset.lessonId;
            const isEnroll = this.dataset.action === 'enroll';
            const button = this;

            button.innerHTML = isEnroll ? 'Enrolling...' : 'Unenrolling...';
            button.classList.add('processing');

            fetch(ajaxurl, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded'
                },
                body: new URLSearchParams({
                    action: isEnroll ? 'nctb_enroll_lesson' : 'nctb_unenroll_lesson',
                    lesson_id: lessonId,
                    nonce: nctb_ajax_nonce
                })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    showToast(data.message);
                    // Update enrollment count on teacher dashboard
                    const countElement = document.querySelector(`.lesson-card[data-lesson-id="${lessonId}"] .stats span:first-child`);
                    if (countElement) {
                        countElement.textContent = `📚 Enrolled: ${data.enrollment_count}`;
                    }
                    // Update button state
                    if (isEnroll) {
                        this.textContent = 'Enrolled';
                        this.classList.add('enrolled');
                        this.classList.remove('processing');
                    } else {
                        this.textContent = 'Not enrolled';
                        this.classList.remove('enrolled', 'processing');
                    }
                } else {
                    showToast(data.data || 'Failed to update enrollment', 'error');
                    button.innerHTML = isEnroll ? 'Enroll' : 'Unenroll';
                    button.classList.remove('processing');
                }
            })
            .catch(error => {
                showToast('Network error', 'error');
                button.innerHTML = isEnroll ? 'Enroll' : 'Unenroll';
                button.classList.remove('processing');
            });
        });
    });

    // Handle enroll all students
    document.querySelectorAll('.enroll-all-btn').forEach(button => {
        button.addEventListener('click', function() {
            const lessonId = this.dataset.lessonId;
            const button = this;

            button.innerHTML = 'Enrolling all...';
            button.classList.add('processing');

            fetch(ajaxurl, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded'
                  },
                body: new URLSearchParams({
                    action: 'nctb_enroll_all_students',
                    lesson_id: lessonId,
                    nonce: nctb_ajax_nonce
                })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    showToast('All students enrolled successfully');
                    // Update enrollment count
                    const countElement = document.querySelector(`.lesson-card[data-lesson-id="${lessonId}"] .stats span:first-child`);
                    if (countElement) {
                        countElement.textContent = `📚 Enrolled: ${data.enrollment_count}`;
                    }
                } else {
                    showToast(data.data || 'Failed to enroll all students', 'error');
                }
                button.innerHTML = 'Enroll All';
                button.classList.remove('processing');
            })
            .catch(error => {
                showToast('Network error', 'error');
                button.innerHTML = 'Enroll All';
                button.classList.remove('processing');
            });
        });
    });

    // Show toast notifications
    function showToast(message, type = 'success') {
        const toast = document.createElement('div');
        toast.className = `toast ${type}`;
        toast.textContent = message;
        document.body.appendChild(toast);
        setTimeout(() => {
            toast.classList.add('fade-out');
            setTimeout(() => toast.remove(), 300);
        }, 3000);
    }
});