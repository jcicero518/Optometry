import React from 'react';
import PropTypes from 'prop-types';
import { withStyles } from 'material-ui/styles';
import green from 'material-ui/colors/green';
import Icon from 'material-ui/Icon';

const styles = theme => ({
	root: {
		display: 'flex',
		justifyContent: 'center',
		alignItems: 'flex-end',
		pointerEvents: 'none'
	},
	icon: {
		margin: 0,
	},
	iconHover: {
		margin: theme.spacing.unit * 2,
		'&:hover': {
			color: green[200],
		},
	}
});

const MaterialIcons = ({ classes }) => {

	return (
		<div className={classes.root}>
			<Icon className={classes.icon}>menu</Icon>
		</div>
	);
};

MaterialIcons.propTypes = {
	classes: PropTypes.object.isRequired
};

export default withStyles( styles )( MaterialIcons );