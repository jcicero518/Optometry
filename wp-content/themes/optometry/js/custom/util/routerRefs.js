
export function refFocusCallback( node ) {
    let activeClass = 'current-menu-item';

    if ( node && node instanceof Node ) {
        try {
            activeClass = node.dataset.activeClass ? node.dataset.activeClass : activeClass;
        } catch ( e ) {
            console.warn( e.message );
        }

        node.onfocusin = event => {

            if ( ! event.target.classList.contains( activeClass ) ) {
                event.target.classList.add( activeClass );
            }
        }
        node.onfocusout = event => {
            if ( event.target.classList.contains( activeClass ) ) {
                event.target.classList.remove( activeClass );
            }
        }
    }
}