import React from "react";
import {
	NavLink,
	Link,
	withRouter
}  from "react-router-dom";
import DataStore from "../../store/DataStore";

import { decodeEntity } from "../../util/entityDecoder";
import { refFocusCallback } from "../../util/routerRefs";


/**
 * return (
 <ul>
 <li key={item.ID} className="page_item">
 <NavLink to={item.permalink.replace(`${location.origin}/`, '/')} activeClassName="current_page_item">{decodeEntity(item.title.rendered)}</NavLink>
 </li>
 </ul>
 )
 * @param items
 */
const dataStore = new DataStore();
function findChildren( item ) {
	let children = dataStore.getChildren( item.id );
	if ( ! children ) {
		return null;
	}

	return (
		<ul>
			<li key={item.ID} className="page_item">
                <NavLink to={item.permalink.replace(`${location.origin}/`, '/')} activeClassName="current_page_item">{decodeEntity(item.title.rendered)}</NavLink>
			</li>
		</ul>
	)
}

const WidgetMenu = ( {leftNavMenuData, loading} ) => {
	const liClasses = ['page_item'];

	if ( leftNavMenuData && leftNavMenuData.length ) {
        return (
            <div className="nav-menu js-secondary-menu widget_nav_menu">
                <ul className="menu">
                    {leftNavMenuData.map( item => (
                        <li key={item.id} className={liClasses.join( ' ' )}>
                            <NavLink data-active-class="current_page_item" to={item.link.replace(`${location.origin}/`, '/')} activeClassName="current_page_item">{decodeEntity(item.title.rendered)}</NavLink>
                        </li>
                    ))}
                </ul>
            </div>
        )
	}

	return null;

};

export default WidgetMenu;