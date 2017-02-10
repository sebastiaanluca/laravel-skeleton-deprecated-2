/**
 * Spawn a new command process
 */

const childProcess = require('child_process');

module.exports = (command, options, callback) => {
    childProcess
        .spawn(command, options, {stdio: 'inherit', env: process.env})
        .on('close', code => code !== 1 ? callback() : null)
};