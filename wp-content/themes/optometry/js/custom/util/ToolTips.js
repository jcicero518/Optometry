import React from 'react';
import PropTypes from 'prop-types';
import { withStyles } from 'material-ui/styles';
import Grid from 'material-ui/Grid';
import Button from 'material-ui/Button';
import Tooltip from 'material-ui/Tooltip';

export default function Tip( { title, placement = 'top-start', children } ) {
	const tipId = `tooltip-${placement}`;

	return (
		<Tooltip id={tipId} title={title} placement={placement}>
			{children}
		</Tooltip>
	);
}