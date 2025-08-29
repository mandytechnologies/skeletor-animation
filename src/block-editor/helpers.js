import classnames from 'classnames';

export const getClassNames = ({ animation }) => {
	return classnames({
		'has-animation': animation,
		[`animation-${animation}`]: animation,
	});
};
