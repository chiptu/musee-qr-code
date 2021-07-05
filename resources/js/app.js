require('./bootstrap');


require('@fortawesome/fontawesome-free/js/all.js');

import QRScanner from 'qr-code-scanner';

QRScanner.initiate({
    match: /^[a-zA-Z0-9]{16,18}$/, // optional
    onResult: function (result) { console.info('DONE: ', result); },
    onError: function (err) { console.error('ERR :::: ', err); }, // optional
    onTimeout: function () { console.warn('TIMEOUT'); } // optional
})
