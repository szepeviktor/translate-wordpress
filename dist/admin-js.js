/******/ (function(modules) { // webpackBootstrap
/******/ 	// The module cache
/******/ 	var installedModules = {};
/******/
/******/ 	// The require function
/******/ 	function __webpack_require__(moduleId) {
/******/
/******/ 		// Check if module is in cache
/******/ 		if(installedModules[moduleId]) {
/******/ 			return installedModules[moduleId].exports;
/******/ 		}
/******/ 		// Create a new module (and put it into the cache)
/******/ 		var module = installedModules[moduleId] = {
/******/ 			i: moduleId,
/******/ 			l: false,
/******/ 			exports: {}
/******/ 		};
/******/
/******/ 		// Execute the module function
/******/ 		modules[moduleId].call(module.exports, module, module.exports, __webpack_require__);
/******/
/******/ 		// Flag the module as loaded
/******/ 		module.l = true;
/******/
/******/ 		// Return the exports of the module
/******/ 		return module.exports;
/******/ 	}
/******/
/******/
/******/ 	// expose the modules object (__webpack_modules__)
/******/ 	__webpack_require__.m = modules;
/******/
/******/ 	// expose the module cache
/******/ 	__webpack_require__.c = installedModules;
/******/
/******/ 	// define getter function for harmony exports
/******/ 	__webpack_require__.d = function(exports, name, getter) {
/******/ 		if(!__webpack_require__.o(exports, name)) {
/******/ 			Object.defineProperty(exports, name, {
/******/ 				configurable: false,
/******/ 				enumerable: true,
/******/ 				get: getter
/******/ 			});
/******/ 		}
/******/ 	};
/******/
/******/ 	// define __esModule on exports
/******/ 	__webpack_require__.r = function(exports) {
/******/ 		Object.defineProperty(exports, '__esModule', { value: true });
/******/ 	};
/******/
/******/ 	// getDefaultExport function for compatibility with non-harmony modules
/******/ 	__webpack_require__.n = function(module) {
/******/ 		var getter = module && module.__esModule ?
/******/ 			function getDefault() { return module['default']; } :
/******/ 			function getModuleExports() { return module; };
/******/ 		__webpack_require__.d(getter, 'a', getter);
/******/ 		return getter;
/******/ 	};
/******/
/******/ 	// Object.prototype.hasOwnProperty.call
/******/ 	__webpack_require__.o = function(object, property) { return Object.prototype.hasOwnProperty.call(object, property); };
/******/
/******/ 	// __webpack_public_path__
/******/ 	__webpack_require__.p = "";
/******/
/******/
/******/ 	// Load entry module and return exports
/******/ 	return __webpack_require__(__webpack_require__.s = "./app/javascripts/index.js");
/******/ })
/************************************************************************/
/******/ ({

/***/ "./app/javascripts/admin-select.js":
/*!*****************************************!*\
  !*** ./app/javascripts/admin-select.js ***!
  \*****************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
eval("__webpack_require__.r(__webpack_exports__);\nconst init_admin = function(){\n\tconst $ = jQuery\n\n\tconst init_select2 = () => {\n\t\t$(\".weglot-select\").select2();\n\n\t\t$(\".weglot-select-exclusion\").select2({\n\t\t\ttags: true,\n\t\t});\n\t}\n\n\tconst init_exclude_url = () => {\n\t\tconst template_add_exclude_url = document.querySelector(\"#tpl-exclusion-url\");\n\t\tconst template_add_exclude_block = document.querySelector(\"#tpl-exclusion-block\");\n\t\tconst parent_exclude_url_append = document.querySelector(\"#container-exclude_urls\");\n\t\tconst parent_exclude_block_append = document.querySelector(\"#container-exclude_blocks\");\n\n\t\tfunction removeLineUrl(e) {\n\t\t\te.preventDefault();\n\t\t\tthis.parentNode.remove()\n\t\t}\n\n\t\tdocument\n\t\t\t.querySelector(\"#js-add-exclude-url\")\n\t\t\t.addEventListener(\"click\", (e) => {\n\t\t\t\te.preventDefault()\n\t\t\t\tparent_exclude_url_append.insertAdjacentHTML(\"beforeend\", template_add_exclude_url.innerHTML);\n\t\t\t\tdocument\n\t\t\t\t\t.querySelector(\n\t\t\t\t\t\t\"#container-exclude_url .item-exclude:last-child .js-btn-remove-exclude\"\n\t\t\t\t\t)\n\t\t\t\t\t.addEventListener(\"click\", removeLineUrl);\n\t\t\t});\n\n\t\tdocument\n\t\t\t.querySelector(\"#js-add-exclude-block\")\n\t\t\t.addEventListener(\"click\", (e) => {\n\t\t\t\te.preventDefault()\n\t\t\t\tparent_exclude_block_append.insertAdjacentHTML(\"beforeend\", template_add_exclude_block.innerHTML);\n\t\t\t\tdocument\n\t\t\t\t\t.querySelector(\n\t\t\t\t\t\t\"##container-exclude_block .item-exclude:last-child .js-btn-remove-exclude\"\n\t\t\t\t\t)\n\t\t\t\t\t.addEventListener(\"click\", removeLineUrl);\n\t\t\t});\n\n\t\tconst remove_urls = document\n\t\t\t.querySelectorAll(\".js-btn-remove-exclude-url\")\n\n\t\tremove_urls.forEach((el) => {\n\t\t\tel.addEventListener(\"click\", removeLineUrl);\n\t\t})\n\n\t}\n\n\tdocument.addEventListener('DOMContentLoaded', () => {\n\t\tinit_select2();\n\t\tinit_exclude_url();\n\t})\n}\n\n/* harmony default export */ __webpack_exports__[\"default\"] = (init_admin);\n\n\n\n//# sourceURL=webpack:///./app/javascripts/admin-select.js?");

/***/ }),

/***/ "./app/javascripts/index.js":
/*!**********************************!*\
  !*** ./app/javascripts/index.js ***!
  \**********************************/
/*! no exports provided */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
eval("__webpack_require__.r(__webpack_exports__);\n/* harmony import */ var _admin_select__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! ./admin-select */ \"./app/javascripts/admin-select.js\");\n\n\nObject(_admin_select__WEBPACK_IMPORTED_MODULE_0__[\"default\"])()\n\n\n//# sourceURL=webpack:///./app/javascripts/index.js?");

/***/ })

/******/ });