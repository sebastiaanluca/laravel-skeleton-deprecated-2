/*
 * Determine the source of a required module (JS, CSS, images, ...) 
 * in your application scripts
 */
module.exports = {
    isExternalModule: function (module) {
        const userRequest = module.userRequest;
        
        if (typeof userRequest !== 'string') {
            return false;
        }
        
        return userRequest.indexOf('bower_components') >= 0 ||
            userRequest.indexOf('node_modules') >= 0 ||
            userRequest.indexOf('libraries') >= 0;
    }
}