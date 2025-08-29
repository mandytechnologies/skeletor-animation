import classnames from 'classnames';
import { addFilter, applyFilters } from '@wordpress/hooks';
import { createHigherOrderComponent } from '@wordpress/compose';
import { InspectorControls } from '@wordpress/block-editor';

import { animationAttributes } from './attributes';
import { BlockAnimationControl } from './control';
import { getClassNames } from './helpers';

const BLOCK_NAME = ['core/group', 'core/image'];

/**
 * This function is used to determine if the block should use this Control or not. By
 * filtering hasAnimationControl from within the theme js, you can add this to your
 * block without having to touch the plugin script.
 * @param {string} name
 */
const isBlockWithAnimation = name => {
	return applyFilters(
		'hasAnimationControl',
		BLOCK_NAME.includes(name),
		name
	);
}

addFilter(
	'blocks.registerBlockType',
	'blockAnimation.attributes',
	(settings, name) => {
		if (!isBlockWithAnimation(name)) {
			return settings;
		}

		return {
			...settings,
			attributes: {
				...settings.attributes,
				...animationAttributes,
			},
		};
	}
);

addFilter(
	'editor.BlockEdit',
	'blockAnimation.control',
	createHigherOrderComponent(
		(BlockEdit) => (props) => {
			if (!isBlockWithAnimation(props.name)) {
				return <BlockEdit {...props} />;
			}

			return (
				<>
					<BlockEdit {...props} />
					<InspectorControls>
						<BlockAnimationControl {...props} />
					</InspectorControls>
				</>
			);
		},
		'withBlockAnimationControl'
	)
);

addFilter(
	'editor.BlockListBlock',
	'blockAnimation.editorBlock',
	createHigherOrderComponent(
		(BlockListBlock) => (props) => {
			if (!isBlockWithAnimation(props.name)) {
				return <BlockListBlock {...props} />;
			}

			const blockListProps = {
				...props,
				className: classnames(
					props.className,
					getClassNames(props.attributes)
				),
			};

			return <BlockListBlock {...blockListProps} />;
		},
		'withblockAnimationClassNames'
	)
);

addFilter(
	'blocks.getSaveContent.extraProps',
	'blockAnimation.className',
	(props, blockType, attributes) => {
		if (!isBlockWithAnimation(blockType.name)) {
			return props;
		}

		return {
			...props,
			className: classnames(props.className, getClassNames(attributes)),
		};
	}
);
