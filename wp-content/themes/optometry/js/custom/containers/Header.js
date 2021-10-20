import React, {Fragment} from "react";
import PropTypes from "prop-types";
import { hot } from "react-hot-loader";
import {
	BrowserRouter as Router,
	Switch,
	Route,
	NavLink,
	Link,
	withRouter
}  from "react-router-dom";

import {
	compose,
	pipe,
	makeMarkup
} from "../util/functional";

import Masthead from "./Header/Masthead";
import {TransitionGroup, CSSTransitionGroup} from "react-transition-group";

function topLevelData( data ) {
	if ( ! data ) {
		return false;
	}

	return data.filter( d => {
		return parseInt( d.menu_item_parent ) === 0;
	});
}

function findChildren( data, item ) {
	return data.filter( d => {
		return parseInt( d.menu_item_parent ) === parseInt( item.ID );
	})
}

/**
 * innerRef callback to skip tabbing of submenu items
 * @param node
 */
function refTabIndexCallback( node ) {
    if ( ! node ) {
        return false;
    }
	node.setAttribute( 'tabindex', -1 );
}

const Header = ( props ) => {

	const { menuData, history, mobileNavStatus } = props;

	const liClasses = ['menu-item'];
	const menuTopLevel = topLevelData( menuData );
	let top;

	if ( menuTopLevel ) {
		top = menuTopLevel.map( item => {
			let children = findChildren( menuData, item ) ? findChildren( menuData, item ) : [];
			return (
				<li key={item.ID} className={liClasses.join( ' ' )}>
					<NavLink to={item.url.replace(`${location.origin}/`, '/')} activeClassName="current-menu-item">{item.title}</NavLink>
					<ul>
						{children.map( child => (
							<li key={child.ID}><NavLink innerRef={refTabIndexCallback} to={child.url.replace(`${location.origin}/`, '/')}>{child.title}</NavLink></li>
						))}
					</ul>
				</li>
			)
		})
	}

	const navClasses = ['main-navigation'];
	const mobileToggled = ['toggled'];
	let updatedNavClasses;
	if ( mobileNavStatus ) {
		updatedNavClasses = [ ...navClasses, ...mobileToggled ];
	} else {
		updatedNavClasses = navClasses;
	}

	return (
		<CSSTransitionGroup component="div"
		                    className="header-wrapper wrap-transition"
		                    transitionName="fade"
		                    transitionAppear={true}
		                    transitionAppearTimeout={500}
		                    transitionEnterTimeout={500}
		                    transitionLeaveTimeout={1000}>
			<Masthead history={history} />
			<nav id="site-navigation" className={updatedNavClasses.join( ' ' )} role="navigation">
				<div className="menu-main-menu-container">
					<ul className="js-primary-menu nav-menu" aria-expanded={mobileNavStatus}>
						{top ? top : ''}
					</ul>
				</div>
			</nav>
		</CSSTransitionGroup>
	);

};

export default hot( module )( Header );