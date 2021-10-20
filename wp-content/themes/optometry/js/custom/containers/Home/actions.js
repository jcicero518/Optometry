import React from "react";
import {
	SET_FRONT_PAGE_ID,
	SET_HOME_DATA,
	SET_HOME_DATA_SUCCESS,
	SET_HOME_DATA_ERROR
} from "../../actions/actionTypes";

export function setFrontPageId( postId ) {
	return {
		type: SET_FRONT_PAGE_ID,
		postId
	}
}

export function setHomeData( postId ) {
	return {
		type: SET_HOME_DATA,
		postId
	}
}

export function setHomeDataSuccess( data ) {
	return {
		type: SET_HOME_DATA_SUCCESS,
		data
	}
}

export function setHomeDataError( error ) {
	return {
		type: SET_HOME_DATA_ERROR,
		error
	}
}