import React from "react";
import {
	SET_LEFT_NAV,
	SET_MOBILE_NAV_STATUS,
	SET_OPEN_DRAWER,
	SET_PAGE_CHILDREN,
	SET_PAGE_CHILDREN_SUCCESS,
	SET_LOADING,
	SET_NAV_MENU,
	SET_NAV_MENU_SUCCESS,
	SET_NAV_MENU_ERROR,
	SET_PAGE,
	SET_PAGE_SUCCESS,
	SET_PAGE_ERROR,
	SET_PAGE_TEMPLATE,
	SET_MEDIA,
	SET_MEDIA_ID,
	SET_MEDIA_SIZES,
	SET_LEFT_NAV_SUCCESS,
	SEARCH_SITE,
	SEARCH_SITE_SUCCESS,
	SEARCH_SITE_ERROR,
	SET_SWIPING,
	SET_SWIPED,
	SET_SLIDER_SLIDES
} from "./actionTypes";

import DataStore from "../store/DataStore";

export function setSwiping( deltaX ) {
	return {
		type: SET_SWIPING,
		deltaX
	}
}

export function setSwiped( finalX ) {
	return {
		type: SET_SWIPED,
		finalX
	}
}

export function setSliderSlides( slides ) {
	return {
		type: SET_SLIDER_SLIDES,
		slides
	};
}

export function setMobileNavStatus( isOpen ) {
	return {
		type: SET_MOBILE_NAV_STATUS,
		isOpen
	}
}

export function setOpenDrawer( isOpen ) {
	return {
		type: SET_OPEN_DRAWER,
		isOpen
	}
}

export function setPageChildren( postId ) {
	return {
		type: SET_PAGE_CHILDREN,
		postId
	}
}

export function setPageChildrenSuccess( items ) {
	return {
		type: SET_PAGE_CHILDREN_SUCCESS,
		items
	}
}

export function setNavMenu( menu ) {
	return {
		type: SET_NAV_MENU,
		menu
	}
}

export function setNavMenuSuccess( data ) {
	return {
		type: SET_NAV_MENU_SUCCESS,
		data
	}
}

export function setNavMenuError( error ) {
	return {
		type: SET_NAV_MENU_ERROR,
		error
	}
}

export function setLeftNav( postId ) {
	return {
		type: SET_LEFT_NAV,
		postId
	}
}

export function setLeftNavSuccess( items ) {
	return {
		type: SET_LEFT_NAV_SUCCESS,
		items
	}
}

export function setPage() {
	return {
		type: SET_PAGE
	}
}

export function setPageSuccess( pageData ) {
	return {
		type: SET_PAGE_SUCCESS,
		pageData,
		postId: pageData.id,
		pageTemplate: pageData.template,
		featuredImageId: pageData.featured_media
	}
}

export function setPageError( error ) {
	return {
		type: SET_PAGE_ERROR,
		error
	}
}

export function searchSite( term, currPage, numPages ) {
	return {
		type: SEARCH_SITE,
		term,
		currPage,
		numPages
	};
}

export function searchSiteSuccess( results, headers ) {
	return {
		type: SEARCH_SITE_SUCCESS,
		results,
		headers
	}
}

export function searchSiteError( error ) {
	return {
		type: SEARCH_SITE_ERROR,
		error
	}
}

export function setPageTemplate( template ) {
	return {
		type: SET_PAGE_TEMPLATE,
		template
	}
}

export function setMediaId( id ) {
	return {
		type: SET_MEDIA_ID,
		id
	}
}

export function setMedia( media ) {
	return {
		type: SET_MEDIA,
		media,
		sizes: media.media_details.sizes
	}
}

export function setMediaSizes( sizes ) {
	return {
		type: SET_MEDIA_SIZES,
		sizes
	}
}

export function isLoading() {
	return {
		type: SET_LOADING
	}
}

/**********
 * THUNKS
 **********/
export function receivedNavMenuRequest( menu ) {
	return ( dispatch, getState ) => {
		const globalState = getState().get('global').toJS();

		const menuState = globalState.menuData.navObjects;
		if ( shouldFetchNav( globalState, menuState ) ) {
			dispatch( isLoading() );

			const dataStore = new DataStore();
			dataStore.getNavMenu( menu ).then( data => {
				dispatch( setNavMenuSuccess( data ) );
				return data;
			});
		}

		return Promise.resolve();
	}
}

export function searchSiteRequest( term ) {
	return ( dispatch, getState ) => {
		dispatch( searchSite( term ) );

		const dataStore = new DataStore();
		dataStore.searchSite( term ).then( results => {
			dispatch( searchSiteSuccess( results ) );
		});
	};
}

function updateLocal( payload ) {
	console.log(payload, 'payload result');
}

export function receivedPageRequest( slug ) {
	return ( dispatch ) => {
		dispatch( isLoading() );
		dispatch( setPage() );

		const dataStore = new DataStore();
		dataStore.getPageBySlug( slug ).then( result => {
			dispatch( setPageSuccess( result ) );
			//dispatch( setLeftNav( result.id ) );
			dispatch( receivedLeftNavRequest( result.id ) );
			if ( result.hasOwnProperty( 'featured_media' ) ) {
				if ( result.featured_media > 0 ) {
					dispatch( receivedMediaRequest( result.featured_media ) );
				} else {
					// dispatch with a default for now
					//let tempMediaId = 99;
					//dispatch( setMediaId( tempMediaId ) );
					//dispatch( receivedMediaRequest( tempMediaId ) );
				}

			}
			//updateLocal( result );
			return result;
		}).catch( err => {
			dispatch( setPageError( err ) );
		});
	}
}

export function receivedLeftNavRequest( postId ) {
	return ( dispatch ) => {
		const dataStore = new DataStore();
		dataStore.getNavById( postId ).then( result => {
			dispatch( setLeftNavSuccess( result ) );
		});
	}
}

function shouldFetchNav( state, menuState ) {
	if ( ! menuState ) {
		return true;
	}
	return false;
}

/*export function getMediaId() {
	return ( dispatch, getState ) => {
		console.log(getState().get('global').toJS(), 'getState');
	}
}*/

export function receivedMediaRequest( mediaId ) {
	return ( dispatch ) => {
		dispatch( isLoading() );

		const dataStore = new DataStore();
		dataStore.getFeaturedImage( mediaId ).then( result => {
			dispatch( setMedia( result ) );
			//dispatch( setMediaSizes( result.media_details.sizes ) );
			return result;
		}).catch( err => {
			dispatch( setPageError( err ) );
		});
	}
}

export function makeApiRequest() {
	return ( dispatch ) => {

	}
}

