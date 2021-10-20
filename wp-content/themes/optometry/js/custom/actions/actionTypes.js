/*
 * AppConstants
 * Each action has a corresponding type, which the reducer knows and picks up on.
 * To avoid weird typos between the reducer and the actions, we save them as
 * constants here. We prefix them with 'yourproject/YourComponent' so we avoid
 * reducers accidentally picking up actions they shouldn't.
 *
 * Follow this format:
 * export const YOUR_ACTION_CONSTANT = 'yourproject/YourContainer/YOUR_ACTION_CONSTANT';
 */

export const SET_LOADING = 'optometry/SET_LOADING';
export const SET_ERROR = 'optometry/SET_ERROR';

export const SET_SWIPING = 'optometry/SET_SWIPING';
export const SET_SWIPED = 'optometry/SET_SWIPED';

export const SET_FRONT_PAGE_ID = 'optometry/SET_FRONT_PAGE_ID';

export const SET_MOBILE_NAV_STATUS = 'optometry/SET_MOBILE_NAV_STATUS';

export const SET_OPEN_DRAWER = 'optometry/SET_OPEN_DRAWER';

export const SET_PAGE_CHILDREN = 'optometry/SET_PAGE_CHILDREN';
export const SET_PAGE_CHILDREN_SUCCESS = 'optometry/SET_PAGE_CHILDREN_SUCCESS';

export const SET_HOME_DATA = 'optometry/SET_HOME_DATA';
export const SET_HOME_DATA_SUCCESS = 'optometry/SET_HOME_DATA_SUCCESS';
export const SET_HOME_DATA_ERROR = 'optometry/SET_HOME_DATA_ERROR';

export const SET_NAV_MENU = 'optometry/SET_NAV_MENU';
export const SET_NAV_MENU_SUCCESS = 'optometry/SET_NAV_MENU_SUCCESS';
export const SET_NAV_MENU_ERROR = 'optometry/SET_NAV_MENU_ERROR';

export const SET_LEFT_NAV = 'optometry/SET_LEFT_NAV';
export const SET_LEFT_NAV_SUCCESS = 'optometry/SET_LEFT_NAV_SUCCESS';

export const SET_PAGE = 'optometry/SET_PAGE';
export const SET_PAGE_SUCCESS = 'optometry/SET_PAGE_SUCCESS';
export const SET_PAGE_ERROR = 'optometry/SET_PAGE_ERROR';

export const SET_PAGE_TEMPLATE = 'optometry/SET_PAGE_TEMPLATE';

export const SET_MEDIA_ID = 'optometry/SET_MEDIA_ID';
export const SET_MEDIA = 'optometry/SET_MEDIA';
export const SET_MEDIA_SIZES = 'optometry/SET_MEDIA_SIZES';

export const SEARCH_SITE = 'optometry/SEARCH_SITE';
export const SEARCH_SITE_HEADERS = 'optometry/SEARCH_SITE_HEADERS';
export const SEARCH_SITE_SUCCESS = 'optometry/SEARCH_SITE_SUCCESS';
export const SEARCH_SITE_ERROR = 'optometry/SEARCH_SITE_ERROR';

export const SET_SLIDER_SLIDES = 'optometry/SET_SLIDER_SLIDES';

export const DEFAULT_LOCALE = 'en';
