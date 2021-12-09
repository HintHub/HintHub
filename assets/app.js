/*const $ = require('jquery');
// this "modifies" the jquery module: adding behavior to it
// the bootstrap module doesn't export/return anything
require('bootstrap');

// or you can include specific pieces
// require('bootstrap/js/dist/tooltip');
// require('bootstrap/js/dist/popover');

/*
$(document).ready(function() {
    $('[data-toggle="popover"]').popover();
});*/


import './styles/app.scss';

const $ = require('jquery');

const bootstrap = require('bootstrap');

var popoverTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="popover"]'));

var popoverList = popoverTriggerList.map(function (popoverTriggerEl) {

return new bootstrap.Popover(popoverTriggerEl);

});
