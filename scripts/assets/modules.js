/**
 * Loads the modules found in /modules and prepares them to be merged with the webpack config.
 */

'use strict'

const fs = require('fs')
const path = require('path')
const _ = require('lodash')

//

module.exports = {
    getDirectories: function (srcpath) {
        return fs.readdirSync(srcpath).filter(file => fs.statSync(path.resolve(srcpath, file)).isDirectory())
    },
    
    fileExists: function (filePath) {
        try {
            return fs.statSync(path.resolve(process.cwd(), filePath)).isFile()
        }
        catch (err) {
            return false
        }
    },
    
    getAll: function (exclude) {
        if (! Array.isArray(exclude)) {
            exclude = []
        }
        
        // Get all modules in the root directory of /modules
        let directories = this.getDirectories(path.resolve(process.cwd(), 'modules'))
        
        // Build full path
        let modules = {}
        
        // Note the `for … of`
        // http://stackoverflow.com/questions/29285897/what-is-the-difference-between-for-in-and-for-of-in-javascript
        for (const module of directories) {
            // Prevent compiling of excluded modules
            if (exclude.indexOf(module) !== - 1) {
                console.info(`[NOTICE] Excluding module "${module}" from build process`)
                continue
            }
            
            // Concatenate the path to the module resource entry file
            modules[_.kebabCase(module).toLowerCase()] = `./modules/${module}`
        }
        
        return modules
    },
    
    getModules: function (exclude) {
        const modules = this.getAll(exclude)
        const valid = {};
        
        for (const module in modules) {
            const entryScript = modules[module] + '/resources/scripts/module.js'
            
            // Verify entry script existence
            if (! this.fileExists(entryScript)) {
                console.info(`[NOTICE] Module "${module}" does not have an entry file located at ${entryScript}`)
                continue
            }
            
            valid[module] = entryScript
        }
        
        return valid
    },
}
