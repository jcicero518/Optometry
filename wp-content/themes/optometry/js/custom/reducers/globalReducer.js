/*
 * AppReducer
 *
 * The reducer takes care of our data. Using actions, we can change our
 * application state.
 * To add a new action, add it to the switch statement in the reducer function
 *
 * Example:
 * case YOUR_ACTION_CONSTANT:
 *   return state.set('yourStateVariable', true);
 */

import { fromJS } from 'immutable';
import ld from "lodash";

import {
	SET_MOBILE_NAV_STATUS,
	SET_OPEN_DRAWER,
	SET_PAGE_CHILDREN,
	SET_PAGE_CHILDREN_SUCCESS,
	SET_LEFT_NAV,
	SET_LEFT_NAV_SUCCESS,
	SET_LOADING,
	SET_FRONT_PAGE_ID,
	SET_HOME_DATA,
	SET_HOME_DATA_SUCCESS,
	SET_HOME_DATA_ERROR,
	SET_NAV_MENU,
	SET_NAV_MENU_SUCCESS,
	SET_NAV_MENU_ERROR,
	SET_PAGE,
	SET_PAGE_SUCCESS,
	SET_PAGE_ERROR,
	SET_MEDIA,
	SET_MEDIA_ID,
	SET_MEDIA_SIZES,
	SET_PAGE_TEMPLATE,
	SEARCH_SITE,
	SEARCH_SITE_HEADERS,
	SEARCH_SITE_SUCCESS,
	SEARCH_SITE_ERROR,
	SET_SWIPING,
	SET_SWIPED, SET_SLIDER_SLIDES
} from "../actions/actionTypes";

// The initial state of the App
const initialState = fromJS({
	loading: false,
	error: false,
	userData: {
		repositories: false,
	},
	leftNavData: false,
	pageData: false,
	pageTemplate: false,
	postId: false,
	mediaData: false,
	mediaId: false,
	mediaSizes: false,
	mobileNav: {
		isOpen: false
	},
	buttonDrawer: {
		isOpen: false
	},
	pageChildren: {
		postId: false,
		children: false
	},
	menuData: {
		menuName: false,
		navObjects: false
	},
	search: {
		term: false,
		results: false,
		headers: false,
		currPage: 1,
		numPages: false
	},
	slider: {
		slides: false
	},
	swiping: {
		deltaX: false,
		finalX: false
	}
});

let __ = ld.noConflict();

function appReducer(state = initialState, action) {

	switch (action.type) {
		case SET_LOADING:
			return state.set('loading', true);
		case SET_SWIPING:
			return state.setIn( ['swiping', 'deltaX'], action.deltaX );
		case SET_SWIPED:
			return state.setIn( ['swiping', 'finalX'], action.finalX );
		case SET_SLIDER_SLIDES:
			return state.setIn( ['slider', 'slides'], action.slides );
		case SET_MOBILE_NAV_STATUS:
			return state.setIn( ['mobileNav', 'isOpen'], !( state.getIn( ['mobileNav', 'isOpen'] ) ) );
		case SET_OPEN_DRAWER:
			return state.setIn( ['buttonDrawer', 'isOpen'], !( state.getIn( ['buttonDrawer', 'isOpen'] ) ) );
		case SET_PAGE_CHILDREN:
			return state
				.set( 'loading', true )
				.setIn( ['pageChildren', 'children'], false );
		case SET_PAGE_CHILDREN_SUCCESS:
			return state
				.set( 'loading', false )
				.setIn( ['pageChildren', 'children'], action.items );
		case SET_NAV_MENU:
			return state
			.set( 'loading', true )
			.set( 'error', false )
			.setIn( ['menuData', 'menuName'], action.menu )
			.setIn( ['menuData', 'navObjects'], false );
		case SET_NAV_MENU_SUCCESS:
			return state
			.set( 'loading', false )
			.setIn( ['menuData', 'navObjects'], action.data );
		case SET_NAV_MENU_ERROR:
			return state
			.set( 'loading', false )
			.set( 'error', action.error );
		case SET_PAGE:
			return state
			.set( 'loading', true )
			.set( 'error', false )
			.set( 'pageData', false )
			.set( 'pageTemplate', false )
			.set( 'postId', false )
			.set( 'mediaData', false )
			.set( 'mediaId', false )
			.set( 'mediaSizes', false );
		case SET_PAGE_SUCCESS:
			return state
			.set( 'loading', false )
			.set( 'pageData', action.pageData )
			.set( 'pageTemplate', action.pageTemplate )
			.set( 'postId', action.postId )
			.set( 'mediaId', action.featuredImageId );
		case SET_PAGE_ERROR:
			return state
				.set( 'loading', false )
				.set( 'error', action.error );
		case SET_MEDIA_ID:
			return state.set( 'mediaId', action.id );
		case SET_MEDIA:
			return state
			.set( 'loading', false )
			.set( 'mediaData', action.media )
			.set( 'mediaSizes', action.sizes );
		case SET_MEDIA_SIZES:
			return state.set( 'mediaSizes', action.sizes );
		case SET_PAGE_TEMPLATE:
			return state.set( 'pageTemplate', action.template );
		case SET_LEFT_NAV:
			return state
				.set( 'loading', true )
				.set( 'leftNavData', false )
				.set( 'postId', action.postId );
		case SET_LEFT_NAV_SUCCESS:
			return state
				.set( 'loading', false )
				.set( 'leftNavData', action.items );
		case SEARCH_SITE:
			return state
				.set( 'loading', true )
				.setIn( ['search', 'currPage'], action.currPage )
				.setIn( ['search', 'numPages'], action.numPages )
				.setIn( ['search', 'term'], action.term )
				.setIn( ['search', 'results'], false );
		case SEARCH_SITE_SUCCESS:
			return state
				.set( 'loading', false )
				.setIn( ['search', 'results'], action.results )
				.setIn( ['search', 'headers'], action.headers );
		case SEARCH_SITE_ERROR:
			return state
				.set( 'loading', false )
				.setIn( ['search', 'results'], false )
				.set( 'error', action.error );
		default:
			return state;
	}
}

export default appReducer;
