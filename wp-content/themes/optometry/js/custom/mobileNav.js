"use strict";

export function mobileNav() {

    let container, navContainer, button, buttonListener, menu;

    container = document.querySelector('.site-actions');
    if ( !container ) {
        return;
    }

    navContainer = document.querySelector('#site-navigation');

    button = container.querySelector('.menu-toggle');

    menu = document.querySelector('.nav-menu');
    if ( 'undefined' === typeof menu ) {
        button.style.display = 'none';
    }

    menu.setAttribute( 'aria-expanded', 'false' );

    buttonListener = ( event ) => {
        if ( ! (event instanceof Event) ) {
            return false;
        }

        if ( navContainer.className.indexOf( 'toggled' ) === -1 ) {
            navContainer.classList.add( 'toggled' );
            button.setAttribute( 'aria-expanded', true );
        } else {
            navContainer.classList.remove( 'toggled' );
            menu.setAttribute( 'aria-expanded', false );
            button.setAttribute( 'aria-expanded', false );
            button.blur();
        }
    };

    button.addEventListener( 'click', buttonListener, false );
}