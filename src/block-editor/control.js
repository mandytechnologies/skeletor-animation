import { animationOptions } from './select-options';

const { __ } = wp.i18n;
const { PanelBody, SelectControl } = wp.components;
const { applyFilters } = wp.hooks;

export const BlockAnimationControl = (props) => {
	const { animation } = props.attributes;

	return (
		<PanelBody className={'skeletor-inspector-control'} title={__('Animation')} initialOpen={false}>
			<SelectControl
				label={__('Animation Style')}
				value={animation}
				options={applyFilters(
					'skeletorAnimationOptions',
					animationOptions
				)}
				onChange={(animation) => {
					props.setAttributes({ animation });
				}}
			/>
		</PanelBody>
	);
};
