/*global OPTO_REST_API */
import axios from "axios";

class DataStore {

	constructor() {
		const appUrl = `${location.protocol}//${location.host}`;
		let _ = this;
		_.restApiUrl = OPTO_REST_API.wp_rest_url;
		_.apiUrl = `${_.restApiUrl}wp/v2/`;
		_.themeApiUrl = `${_.restApiUrl}optometry/v1/`;
		_.menuApiUrl = `${appUrl}/wp-json/wp-menus/v1/menus/`;
		_.mediaApiUrl = `${_.restApiUrl}wp/v2/media/`;
		_.data = [];
		//_.allOptions = HOM_WP_API.hours_op_options;
	}

	/*getOption( option ) {
		let _ = this;
		return _.allOptions[option] ? _.allOptions[option] : false;
	}*/

	static updateLocal( response ) {
		const { responseURL, responseText } = response.request;

		let data = localStorage.getItem( responseURL );
		if ( !data ) {
			localStorage.setItem( responseURL, responseText );
		}

		return data ? JSON.parse( data ) : false;
	}

	api( endPoint, method = 'get', data = null ) {

		return new Promise( ( resolve, reject ) => {
			axios[method]( endPoint, data ? data : '' ).then( ( response ) => {
				//let localData = DataStore.updateLocal( response );
				//localData ? resolve( localData ) : resolve( response.data );
				resolve( response.data );
			}).catch( ( error ) => {
				reject( error );
			});
		});
	}

	getNavMenu( menu ) {
		return this.api( `${this.menuApiUrl}${menu}` ).then( result => {
			return result;
		}).catch( error => {
			console.warn( error );
		})
	}

	getPageBySlug( slug ) {
		return this.api( `${this.apiUrl}pages?slug=${slug}` ).then( result => {
			let payload = result[0];
			if ( payload && payload.template.length ) {
				payload.template = payload.template.replace('.php', '').replace('/', '--');
			}
			return payload;
		}).catch( error => {
			console.warn( error );
		})
	}

	searchSite( term ) {
		return this.api( `${this.apiUrl}pages?search=${term}` ).then( result => {
			return result;
		}).catch( error => {
			console.warn( error );
		})
	}

	getChildren( pageId ) {
        return this.api(`${this.themeApiUrl}children?pageId=${pageId}`).then(children => {
            if ( children.length ) {
                return children[0];
            }
            return false;
        });
	}

	getPagination( term ) {
		return this.api(`${this.themeApiUrl}search/${term}`).then(data => {
			if ( data ) {
				return data;
			}
			return false;
		});
	}

	getNavById( pageId ) {
		return this.api( `${this.themeApiUrl}ancestors?pageId=${pageId}` ).then( ancestors => {
			let parentOf = pageId;

			if ( ancestors && ancestors.length === 1 ) {
				parentOf = ancestors.pop();
			}

			return this.api( `${this.apiUrl}pages?parent=${parentOf}&context=view&orderby=menu_order&order=asc` ).then( result => {
                /*return result.map( r => {
                    let childObj = this.getChildren( r.id );
                    if ( childObj ) {
                        childObj.then( child => {
                        	let c = child.length === 1 ? child : child;
                            r.children = c;
                        });
					}
					return r;
                });*/

				return result;
			})
		})
	}

	getFeaturedImage( mediaId ) {
		return this.api( `${this.mediaApiUrl}${mediaId}`).then( result => {
			return result;
		}).catch( error => {
			console.warn( error );
		})
	}
}

export default DataStore;