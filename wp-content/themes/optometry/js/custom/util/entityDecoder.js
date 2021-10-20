export const decodeEntity = input => {
    return input.includes( '&#038;' ) ? input.replace( '&#038;', '&' ) : input;
};