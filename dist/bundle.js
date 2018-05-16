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
/******/ 	return __webpack_require__(__webpack_require__.s = 0);
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
eval("__webpack_require__.r(__webpack_exports__);\nconst init_admin_select = function(){\n\tconst $ = jQuery\n\tdocument.addEventListener('DOMContentLoaded', () => {\n\t\t$(\".weglot-select\").select2();\n\t})\n}\n\n/* harmony default export */ __webpack_exports__[\"default\"] = (init_admin_select);\n\n\n\n//# sourceURL=webpack:///./app/javascripts/admin-select.js?");

/***/ }),

/***/ "./app/javascripts/index.js":
/*!**********************************!*\
  !*** ./app/javascripts/index.js ***!
  \**********************************/
/*! no exports provided */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
eval("__webpack_require__.r(__webpack_exports__);\n/* harmony import */ var _admin_select__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! ./admin-select */ \"./app/javascripts/admin-select.js\");\n\n\nObject(_admin_select__WEBPACK_IMPORTED_MODULE_0__[\"default\"])()\n\n\n//# sourceURL=webpack:///./app/javascripts/index.js?");

/***/ }),

/***/ "./app/styles/index.scss":
/*!*******************************!*\
  !*** ./app/styles/index.scss ***!
  \*******************************/
/*! no static exports found */
/***/ (function(module, exports) {

eval("// removed by extract-text-webpack-plugin\n\n//# sourceURL=webpack:///./app/styles/index.scss?");

/***/ }),

/***/ 0:
/*!****************************************************************!*\
  !*** multi ./app/javascripts/index.js ./app/styles/index.scss ***!
  \****************************************************************/
/*! no static exports found */
/***/ (function(module, exports, __webpack_require__) {

eval("__webpack_require__(/*! ./app/javascripts/index.js */\"./app/javascripts/index.js\");\nmodule.exports = __webpack_require__(/*! ./app/styles/index.scss */\"./app/styles/index.scss\");\n\n\n//# sourceURL=webpack:///multi_./app/javascripts/index.js_./app/styles/index.scss?");

/***/ })

/******/ });