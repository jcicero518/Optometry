"use strict";

//export function mainMenu() {

    let menuElems = document.querySelectorAll('#primary-menu > li > a');

    let addChildren = (...items) => {
        menuElems.push(items);
    };

    let multiListener = (el, listeners, fn) => {
        if (el instanceof NodeList) {
            [...el].forEach(element => {
                listeners.split(" ").forEach(l => element.addEventListener(l, fn, false));
            });
        } else {
            listeners.split(" ").forEach(l => el.addEventListener(l, fn, false));
        }
    };

    let menuHandler = (event, handler) => {

        if (! ( event instanceof Event ) ) return false;

        let target = event.target;

        switch (event.type) {
            case 'mouseenter':
                target.parentNode.classList.add('hover');
                break;
            case 'mouseleave':
                target.parentNode.classList.remove('hover');
                break;
        }
    };

    multiListener(menuElems, 'mouseenter mouseleave', event => menuHandler(event, menuElems));
    /*primary_menu.addEventListener('mouseenter', (event) => {
        console.log(event, 'evt');
    });*/
//}
export {multiListener};


