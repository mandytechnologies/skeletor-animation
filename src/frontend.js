import { applyFilters } from '@wordpress/hooks';

/** @var float Percentage of the element that needs to be in the viewport to be "visible" */
const DEFAULT_THRESHOLD = 0.05;

/** @var int ms to wait before adding the visible class */
const INTRO_DELAY = 0;

const SELECTOR = '.has-animation';

function intersectionEvent(entries, observer) {
	entries.forEach((e) => {
		if (e.isIntersecting) {
			setTimeout(
				() => {
					e.target.classList.add('visible')
				},
				applyFilters('skeletor_animation_delay', INTRO_DELAY, e.target)
			);
		}
	});
}

function onDocumentReady() {
	const els = document.querySelectorAll(SELECTOR);
	if (!els) {
		return;
	}

	setTimeout(() => intializeAnimationElements(els), 0);
}

function intializeAnimationElements(elements) {
	const threshold = applyFilters(
		'skeletor_animation_threshold',
		DEFAULT_THRESHOLD
	);

	const observer = new IntersectionObserver(intersectionEvent, { threshold });

	elements.forEach((el) => observer.observe(el));
}

document.addEventListener('DOMContentLoaded', onDocumentReady);
