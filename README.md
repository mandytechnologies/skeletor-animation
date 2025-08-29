# Skeletor Block Animation
Add a dropdown menu to select an animation effect to play when the block enters the viewport.

## How to Use
The plugin will automatically add an **Animation** control to the InspectorControls for Group and Image blocks. That control has a dropdown menu to pick the animation you want *(ex. Fade In, Slide from Right, etc.)*. Then on the frontend, the necessary js/css files to drive those animation.

## Adding This to Other Blocks
In your Skeletor Theme’s **admin** js file, use the [WordPress Hooks api](https://developer.wordpress.org/block-editor/packages/packages-hooks/) addFilter method to filter on `hasAnimationControl`. Your filter callback will receive two arguments, the current result and a blockName. Return whether or not you want this block to have animation controls.

## CSS Custom Properties
These properties are all set on `:root` in the plugin’s css file and are referenced by the `.has-animation` class. Change them in your theme styles. Or don’t; I'm a readme file, not a cop.
```scss
--skeletor-animation--duration: 0.2s;
--skeletor-animation--timing: ease-in-out;
--skeletor-animation--delay: 0.2s;
--skeletor-animation--fill-mode: forwards;
```

**Example: Using WordPress hooks to customize the entry threshold**  
By default the IntersectionObserver uses a threshold value of `0.05`. Meaning that 5% of the element needs to be inside the viewport. By filtering on `skeletor_animation_threshold` you can change that number to something else.

Note: Internally, the IntersectionObserver is setup in a callback that occurs just _after_ DOMContentLoaded by using setTimeout(fn, 0). So you can safely add your listener before, or even _during_ your onDocumentReady function. After that it will be too late!
```js
/* Don't activate until elements are 40% of the way into the viewport */
function onDocumentReady() {
	window.wp?.hooks?.addFilter(
		'skeletor_animation_threshold',
		'mandy.skeletorAnimationThreshold',
		(threshold) => { return 0.4; }
	);
}

document.addEventListener('DOMContentLoaded', onDocumentReady);
```

**Example: Using WordPress hooks to delay the animation start**  
Instead of modifying the entry threshold, you can also add a filter for `skeletor_animation_delay` to return a number of milliseconds to delay between the IntersectionEvent trigger and the actual application of the `visible` class.
```js
/**
 * Wait 2 seconds to start the Skeletor Animation if the element has a
 * lazy-animation" class.
 **/
function onDocumentReady() {
	window.wp?.hooks?.addFilter(
		'skeletor_animation_delay',
		'mandy.skeletorAnimationDelay',
		(delay, element) => {
			if (element.classList.contains('lazy-animation')) {
				delay = 2000;
			}

			return delay;
		}
	);
}

document.addEventListener('DOMContentLoaded', onDocumentReady);
```

**Example: Adding Animation controls to “My Block”**  
```js
const { addFilter } = wp.hooks;

addFilter('hasAnimationControl', 'mandy.hasAnimationControl', (result, blockName) => {
	const blocksWithAnimation = [ 'acf/my-block' ];

	return result || blocksWithAnimation.includes(blockName);
});
```
*Skeletor Blocks are registered through ACF, so they'll always have that `acf/` prefix*

**Example: Adding Animation controls to ALL Blocks**  
```js
const { addFilter } = wp.hooks;

addFilter('hasAnimationControl', 'mandy.hasAnimationControl', (result, blockName) => true);
```

**Example: Adding new Animation Options**  
If you want to add a new custom animation, you can hook into `skeletorAnimationOptions` to modify the array passed into the dropdown.
```js
addFilter(
	'skeletorAnimationOptions',
	'mandy.skeletorAnimationOptions',
	animationOptions => [
		...animationOptions,
		{
			label: 'Spiral In',
			value: 'spiral-in',
		},
	]
);
```

Then just add your own css in the theme, following the same pattern as what’s in the plugin.

```scss
.animate.spiral-in {
	&:not(.block-editor &) {
		transform: scale(0.5) scale(0.5) rotate(359.999deg);
		opacity: 0;

		&.enter {
			animation-name: spiralin;
		}

		&.has-exit.exit {
			animation-name: spiralout;
		}
	}
}

@keyframes spiralin {
	from {
		transform: scale(0.5) rotate(359.999deg);
		opacity: 0;
	}

	to {
		transform: scale(1) rotate(0);
		opacity: 1;
	}
}

@keyframes spiralout {
	from {
		transform: scale(1) rotate(0);
		opacity: 1;
	}

	to {
		transform: scale(0.5) rotate(359.999deg);
		opacity: 0;
	}
}
```