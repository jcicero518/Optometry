import React, {Component} from 'react';
import PropTypes from 'prop-types';
import styled from "styled-components";
import { connect } from "react-redux";
import { createStructuredSelector } from "reselect";
import {
	NavLink,
	Link,
}  from "react-router-dom";

import Swipeable from "react-swipeable";
import { withStyles } from 'material-ui/styles';
import Button from 'material-ui/Button';
import green from "material-ui/colors/green";
import Icon from 'material-ui/Icon';
import MaterialIcon from "../../util/MaterialIcon";
import Tip from "../../util/ToolTips";

import LoadingIndicator from "../../components/LoadingIndicator";

import {
	setOpenDrawer,
	setPageChildren,
	setSwiping,
	setSwiped
} from "../../actions/globalActions";
import {
	selectCurrentPostId,
	selectButtonDrawer,
	selectPageChildren,
	selectSwipeDelta,
	selectSwipeFinal
} from "../../selectors/index";

const ButtonA = styled.button`
	background-color: ${ props => props.isActive ? `#0078b9` : `#404040` };
	box-shadow: 0px 1px 5px 0px rgba(0, 0, 0, 0.2), 0px 2px 2px 0px rgba(0, 0, 0, 0.14), 0px 3px 1px -2px rgba(0, 0, 0, 0.12);
	color: #ffffff;
	transition: all 0.3s cubic-bezier(0.215,0.61,0.355,1);
	min-width: auto;
	bottom: 50%;
	position: fixed;
	z-index: 1000;
	font-size: 1rem;
	display: inline-flex;
	justify-content: center;
	vertical-align: middle;
	outline: none;
	border-color: transparent;
	left: ${ props => props.isActive ? `260px` : `0` };
	margin-left: ${ props => props.delta ? `-${props.delta}px` : `0` };
`;

const Drawer = styled.div`
	padding: 0;
	width: 260px;
	min-height: 300px;
	background-color: #404040;
	box-shadow: 0 0 9px rgba(0,0,0,0.4);
	color: #ffffff;
	transition: all 0.3s cubic-bezier(0.215,0.61,0.355,1);
	display: flex;
	flex-direction: column;
	position: fixed;
	z-index: 1001;
	bottom: auto;
	left: ${ props => props.isActive ? `0` : `-260px` };
	margin-left: ${ props => props.delta ? `-${props.delta}px` : `0` };
	right: inherit;
`;

const Header = styled.header`
	padding: 10px 0;
	width: 100%;
	background-color: #000000;
`;

const UL = styled.ul`
	margin: 0;
	padding-left: 0;
	list-style-type: none;
	display: flex;
	flex-direction: column;
	align-items: center;
`;

const H3 = styled.h3`
	text-align: center;
	color: #ffffff;
`;

const Main = styled.main`
	margin: 0 auto;
	padding: 1em;
	width: 100%;
	box-shadow: 1px 1px 1px rgba(0,0,0,.004);
	color: #ffffff;
`;

/*const DrawerButton = props => {
	return (
		<div>
			<Button isActive={props.buttonDrawerStatus}>In This Section</Button>
			<Drawer isActive={props.buttonDrawerStatus}>
				<p>More stuff</p>
			</Drawer>
		</div>
	);
};*/

class DrawerButton extends Component {

	static styles( theme ) {
		return {
			root: {
				display: 'flex',
				justifyContent: 'center',
				alignItems: 'flex-end',
			},
			icon: {
				margin: theme.spacing.unit * 2,
			},
			iconHover: {
				margin: theme.spacing.unit * 2,
				'&:hover': {
					color: green[200],
				},
			}
		};
	}

	constructor( props ) {
		super( props );
		this.swipeThreshold = 50;

		this.onGoBack = this.onGoBack.bind( this );
		this.drawerClick = this.drawerClick.bind( this );
		this.onSwiping = this.onSwiping.bind( this );
		this.onSwipedLeft = this.onSwipedLeft.bind( this );
		this.onSwipingLeft = this.onSwipingLeft.bind( this );
	}

	componentDidUpdate( prevProps ) {
		let {
			pageData: { id },
			currentChildren
		} = this.props;

		if ( prevProps.pageData.id !== id && id ) {
			this.didUpdate();
		} else {
			// something
		}
	}

	onGoBack( event ) {
		event.preventDefault();
		this.props.history.goBack();
	}

	drawerClick( event ) {
		event.preventDefault();
		this.props.setDrawer();
	}

	onSwiping( event, deltaX, deltaY, absX, absY, velocity ) {
		//console.log(`You're Swiping..., delta: ${deltaX}, abs: ${absX}, velocity: ${velocity}`);
		this.props.setSwipe( deltaX );
	}

	onSwipingLeft( event, absX ) {
		//console.log(absX, 'absx');
	}

	onSwipedLeft( event, absX ) {
		if ( absX >= this.swipeThreshold ) {
			this.props.setDrawer();
			this.props.setSwipe( 0 );
			this.props.setSwipedAmount( absX );
		} else {
			this.props.setSwipedAmount( absX );
			this.props.setSwipe( 0 );
		}
	}

	findChildren( data, item ) {
		return data.filter(d => {
			return parseInt(d.menu_item_parent) === parseInt(item.ID);
		})
	}

	topLevelData( data ) {
		return data.filter( d => {
			return parseInt( d.menu_item_parent ) === 0;
		});
	}

	didUpdate() {
		let {
			pageData: { id }
		} = this.props;

		this.props.getChildren( id );
	}

	render() {
		const {
			loading,
			buttonDrawerStatus,
			menuData,
			swipingDelta,
			swipedFinal,
			pageData,
			currentChildren
		} = this.props;


		let output = null;
		if ( buttonDrawerStatus ) {

			if ( currentChildren ) {
				output = currentChildren.map( child => {
					return (
						<li key={child.ID}><NavLink to={child.permalink.replace(`${location.origin}/`, '/')}>{child.post_title}</NavLink></li>
					);
				});
			}

			/*const top = this.topLevelData( menuData );

			if ( top ) {
				let thisone = top.filter( t => parseInt( t.object_id ) === currPageId );
				console.log(thisone, 'thisone');
				ch = menuData.find( t => parseInt( t.menu_item_parent ) === thisone[0].ID );
				console.log(ch, 'ch');
			}*/

		}

		const tipProps = {
			title: 'In This Section',
			placement: 'top'
		};

		return (
			<Swipeable
				className="drawer-container"
				onSwiping={this.onSwiping}
				onSwipedLeft={this.onSwipedLeft}
				onSwipingLeft={this.onSwipingLeft}
			>
				<Tip {...tipProps}>
					<ButtonA
						className="drawer-button-toggle menu-toggle"
						onClick={this.drawerClick}
						isActive={this.props.buttonDrawerStatus}
						delta={swipingDelta}>
						<MaterialIcon />
					</ButtonA>
				</Tip>
				<Drawer
					className="drawer-content-wrapper"
					isActive={this.props.buttonDrawerStatus}
					delta={swipingDelta}
					final={!!swipedFinal}
				>
					<article className="drawer-contents">
						<Header>
							<H3>{pageData ? pageData.title.rendered : null} Section</H3>
						</Header>
						<Main className="drawer-menu">
							<UL className="nav-menu">
								{loading ? <LoadingIndicator /> : output}
							</UL>
						</Main>
					</article>
				</Drawer>
			</Swipeable>
		);
	}
}

/**
 * <Icon>keyboard_return</Icon><a onClick={this.onGoBack}>Back</a>
 */

/**
 *
 * @param dispatch
 * @returns {{setDrawer: function(*=): *, getChildren: function(*=): *, setSwipe: function(*=): *, setSwipedAmount: function(*=): *}}
 */
function mapDispatchToProps( dispatch ) {

	return {
		setDrawer: ( isOpen ) => dispatch( setOpenDrawer( isOpen ) ),
		getChildren: ( postId ) => dispatch( setPageChildren( postId ) ),
		setSwipe: ( deltaX ) => dispatch( setSwiping( deltaX ) ),
		setSwipedAmount: ( absX ) => dispatch( setSwiped( absX ) )
	};
}

const mapStateToProps = createStructuredSelector({
	currentPostId: selectCurrentPostId(),
	buttonDrawerStatus: selectButtonDrawer(),
	currentChildren: selectPageChildren(),
	swipingDelta: selectSwipeDelta(),
	swipedFinal: selectSwipeFinal()
});

DrawerButton.propTypes = {
	pageData: PropTypes.oneOfType([
		PropTypes.object,
		PropTypes.bool
	])
};

export default connect( mapStateToProps, mapDispatchToProps )( DrawerButton );
