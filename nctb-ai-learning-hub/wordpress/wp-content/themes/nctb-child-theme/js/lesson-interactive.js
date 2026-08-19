/**
 * NCTB Learning Hub — Interactive Lesson Stepper & Activities (Phase 4).
 *
 * Lightweight, vanilla JavaScript for activity stepping, progress state
 * persistence, hint reveals, audio playback speed, word counting, and timers.
 * Mobile-first, low-bandwidth friendly with zero dependencies.
 *
 * @package NCTB\Theme
 */

document.addEventListener('DOMContentLoaded', function() {
	'use strict';

	var mainLesson = document.querySelector('.nctb-lesson');
	if (!mainLesson) {
		return;
	}

	var lessonId = mainLesson.getAttribute('data-lesson-id');
	var totalSteps = parseInt(mainLesson.getAttribute('data-total-steps'), 10) || 0;
	if (totalSteps === 0) {
		return;
	}

	var currentStepNumEl = document.getElementById('nctb-current-step-num');
	var currentStepTitleEl = document.getElementById('nctb-current-step-title');
	var progressFillEl = document.getElementById('nctb-progress-fill');
	var progressTrackEl = document.querySelector('.nctb-progress-track');
	var pillsContainer = document.getElementById('nctb-step-pills');
	var pills = document.querySelectorAll('.nctb-step-pill');
	var cards = document.querySelectorAll('.nctb-activity-view-card');
	var wrapper = document.getElementById('nctb-activities-wrapper');
	var linearBtn = document.getElementById('btn-toggle-linear-view');

	var currentStep = 1;
	var isLinearMode = false;

	// Switch to a specific step
	function switchStep(stepNum, updateHash) {
		if (stepNum < 1) stepNum = 1;
		if (stepNum > totalSteps) stepNum = totalSteps;

		currentStep = stepNum;

		// Update cards
		cards.forEach(function(card) {
			var cardStep = parseInt(card.getAttribute('data-step'), 10);
			if (cardStep === currentStep) {
				card.classList.add('active');
				card.style.display = 'block';
			} else {
				card.classList.remove('active');
				if (!isLinearMode) {
					card.style.display = 'none';
				}
			}
		});

		// Update pills
		pills.forEach(function(pill) {
			var pillStep = parseInt(pill.getAttribute('data-step'), 10);
			if (pillStep === currentStep) {
				pill.classList.add('active');
				// Scroll active pill into view in the pills bar
				pill.scrollIntoView({ behavior: 'smooth', block: 'nearest', inline: 'center' });
			} else {
				pill.classList.remove('active');
			}
		});

		// Update Header text & Progress Bar
		var activeCard = document.getElementById('activity-step-' + currentStep);
		if (activeCard) {
			var titleEl = activeCard.querySelector('.activity-card-title');
			if (titleEl && currentStepTitleEl) {
				currentStepTitleEl.textContent = titleEl.textContent;
			}
		}

		if (currentStepNumEl) {
			currentStepNumEl.textContent = currentStep;
		}

		var percent = Math.round((currentStep / totalSteps) * 100);
		if (progressFillEl) {
			progressFillEl.style.width = percent + '%';
		}
		if (progressTrackEl) {
			progressTrackEl.setAttribute('aria-valuenow', currentStep);
		}

		// Persist progress position in localStorage
		try {
			if (lessonId) {
				localStorage.setItem('nctb_lesson_pos_' + lessonId, currentStep);
			}
		} catch (e) {
			// Local storage might be unavailable in private mode
		}

		// Update URL hash for linkability
		if (updateHash !== false) {
			if (history.replaceState) {
				history.replaceState(null, null, '#activity-' + currentStep);
			} else {
				window.location.hash = 'activity-' + currentStep;
			}
		}
	}

	// Restore saved step from Hash or LocalStorage
	function restoreInitialStep() {
		var hash = window.location.hash;
		var match = hash.match(/(?:activity|step)-(\d+)/i);
		if (match && match[1]) {
			var parsed = parseInt(match[1], 10);
			if (parsed >= 1 && parsed <= totalSteps) {
				switchStep(parsed, false);
				return;
			}
		}

		try {
			var saved = localStorage.getItem('nctb_lesson_pos_' + lessonId);
			if (saved) {
				var savedStep = parseInt(saved, 10);
				if (savedStep >= 1 && savedStep <= totalSteps) {
					switchStep(savedStep, false);
					return;
				}
			}
		} catch (e) {}

		switchStep(1, false);
	}

	// Step pill click
	if (pillsContainer) {
		pillsContainer.addEventListener('click', function(e) {
			var pill = e.target.closest('.nctb-step-pill');
			if (pill) {
				var targetStep = parseInt(pill.getAttribute('data-step'), 10);
				if (targetStep) {
					switchStep(targetStep, true);
				}
			}
		});
	}

	// Next / Previous buttons inside cards
	mainLesson.addEventListener('click', function(e) {
		var btnNext = e.target.closest('.btn-step-next');
		if (btnNext) {
			var target = parseInt(btnNext.getAttribute('data-target'), 10);
			if (target) {
				switchStep(target, true);
				var activeCard = document.getElementById('activity-step-' + target);
				if (activeCard) {
					activeCard.scrollIntoView({ behavior: 'smooth', block: 'start' });
				}
			}
			return;
		}

		var btnPrev = e.target.closest('.btn-step-prev');
		if (btnPrev) {
			var targetPrev = parseInt(btnPrev.getAttribute('data-target'), 10);
			if (targetPrev) {
				switchStep(targetPrev, true);
				var activeCardPrev = document.getElementById('activity-step-' + targetPrev);
				if (activeCardPrev) {
					activeCardPrev.scrollIntoView({ behavior: 'smooth', block: 'start' });
				}
			}
			return;
		}
	});

	// Linear / Step View toggle
	if (linearBtn && wrapper) {
		linearBtn.addEventListener('click', function() {
			isLinearMode = !isLinearMode;
			if (isLinearMode) {
				wrapper.classList.add('nctb-linear-view');
				cards.forEach(function(c) { c.style.display = 'block'; });
				linearBtn.innerHTML = '⚡ Step Mode';
			} else {
				wrapper.classList.remove('nctb-linear-view');
				switchStep(currentStep, true);
				linearBtn.innerHTML = '📑 Full Lesson View';
			}
		});
	}

	// Interactive Hint Toggle (Guided Practice)
	mainLesson.addEventListener('click', function(e) {
		var hintBtn = e.target.closest('.btn-toggle-hint');
		if (hintBtn) {
			var zone = hintBtn.closest('.interactive-hint-zone');
			if (zone) {
				var hintContent = zone.querySelector('.hint-content');
				if (hintContent) {
					var isHidden = hintContent.style.display === 'none' || !hintContent.style.display;
					hintContent.style.display = isHidden ? 'block' : 'none';
					hintBtn.textContent = isHidden ? '💡 Hide Hint' : '💡 Show Hint';
				}
			}
			return;
		}

		var answerBtn = e.target.closest('.btn-toggle-answer');
		if (answerBtn) {
			var zoneAns = answerBtn.closest('.interactive-hint-zone');
			if (zoneAns) {
				var ansContent = zoneAns.querySelector('.answer-content');
				if (ansContent) {
					var isAnsHidden = ansContent.style.display === 'none' || !ansContent.style.display;
					ansContent.style.display = isAnsHidden ? 'block' : 'none';
					answerBtn.textContent = isAnsHidden ? '✅ Hide Model Answer' : '✅ Reveal Model Answer';
				}
			}
			return;
		}

		var modelBtn = e.target.closest('.btn-toggle-model-answer');
		if (modelBtn) {
			var modelBox = modelBtn.nextElementSibling;
			if (modelBox) {
				var isModelHidden = modelBox.style.display === 'none' || !modelBox.style.display;
				modelBox.style.display = isModelHidden ? 'block' : 'none';
				modelBtn.textContent = isModelHidden ? '✨ Hide Model Paragraph' : '✨ View Model Paragraph (Board Standard)';
			}
			return;
		}

		var transcriptBtn = e.target.closest('.btn-toggle-transcript');
		if (transcriptBtn) {
			var transcriptBox = transcriptBtn.nextElementSibling;
			if (transcriptBox) {
				var isTransHidden = transcriptBox.style.display === 'none' || !transcriptBox.style.display;
				transcriptBox.style.display = isTransHidden ? 'block' : 'none';
				transcriptBtn.textContent = isTransHidden ? '📜 Hide Audio Transcript' : '📜 View Audio Transcript';
			}
			return;
		}
	});

	// Live word counter for writing draft textarea
	mainLesson.addEventListener('input', function(e) {
		if (e.target.classList.contains('nctb-draft-textarea')) {
			var text = e.target.value.trim();
			var count = text ? text.split(/\s+/).length : 0;
			var container = e.target.closest('.nctb-writing-draft-zone');
			if (container) {
				var counterSpan = container.querySelector('.counter-num');
				if (counterSpan) {
					counterSpan.textContent = count;
				}
			}
		}
	});

	// Speaking practice timer
	var speakingTimerInterval = null;
	var speakingSecondsLeft = 120; // 2 minutes default

	function formatTime(sec) {
		var m = Math.floor(sec / 60);
		var s = sec % 60;
		return (m < 10 ? '0' : '') + m + ':' + (s < 10 ? '0' : '') + s;
	}

	mainLesson.addEventListener('click', function(e) {
		if (e.target.id === 'btn-start-speaking-timer') {
			var timerDisplay = document.getElementById('speaking-timer');
			if (speakingTimerInterval) {
				// Pause
				clearInterval(speakingTimerInterval);
				speakingTimerInterval = null;
				e.target.textContent = '▶️ Resume Timer';
			} else {
				// Start / Resume
				e.target.textContent = '⏸️ Pause Timer';
				speakingTimerInterval = setInterval(function() {
					speakingSecondsLeft--;
					if (timerDisplay) {
						timerDisplay.textContent = formatTime(speakingSecondsLeft);
					}
					if (speakingSecondsLeft <= 0) {
						clearInterval(speakingTimerInterval);
						speakingTimerInterval = null;
						e.target.textContent = '🎉 Time Up!';
					}
				}, 1000);
			}
		}

		if (e.target.id === 'btn-reset-speaking-timer') {
			clearInterval(speakingTimerInterval);
			speakingTimerInterval = null;
			speakingSecondsLeft = 120;
			var timerDisp = document.getElementById('speaking-timer');
			if (timerDisp) timerDisp.textContent = '02:00';
			var startBtn = document.getElementById('btn-start-speaking-timer');
			if (startBtn) startBtn.textContent = '⏱️ Start 2-Min Timer';
		}
	});

	// AI Tutor Trigger Modal / Toast (Phase 9 placeholder)
	var tutorTrigger = document.getElementById('btn-tutor-trigger');
	if (tutorTrigger) {
		tutorTrigger.addEventListener('click', function() {
			alert('🤖 NCTB AI Tutor (Coming in Phase 9):\n\nIn Phase 9, this button will open a contextual AI chat tutor loaded with this lesson\'s concepts, reading text, and vocabulary, offering Socratic hints in Bangla and English without spoiling answers directly.');
		});
	}

	// Keyboard arrow navigation between steps
	document.addEventListener('keydown', function(e) {
		if (['INPUT', 'TEXTAREA'].indexOf(document.activeElement.tagName) !== -1) {
			return;
		}
		if (e.key === 'ArrowRight' && currentStep < totalSteps) {
			switchStep(currentStep + 1, true);
		} else if (e.key === 'ArrowLeft' && currentStep > 1) {
			switchStep(currentStep - 1, true);
		}
	});

	// Initialize step position
	restoreInitialStep();
});
