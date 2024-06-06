/**
 * First we will load all of this project's JavaScript dependencies which
 * includes Vue and other libraries. It is a great starting point when
 * building robust, powerful web applications using Vue and Laravel.
 */

//Bootstrap
require('./bootstrap')

export const clean = obj => {
    Object.keys(obj)
        .forEach(key => (obj[key] == null || undefined) && delete obj[key])
    return obj
}

window.als = require('./simplify')

//App
require('./Main/menu')
require('./Sliders/sliders')
require('./Main/hidden-text')
require('./Main/dropdown')
require('./Tabs/tabs')
require('./Collapse/collapse')

require('./AddFavorite/add-favorite')
require('./Inputs/inputs-range')
require('./Inputs/product-selects')
require('./Product/add-to-cart')
require('./Product/change-product-card')
require('./Product/delete-review')
require('./Favorites/add-to-favorites')
require('./Product/change-cart')
require('./Registration/registration')
require('./CategorySidebar/category-sidebar')
require('./Delivery/delivery')
require('./CourierCalculate/calculate')
require('./ProductsList/productsList')
require('./RequestForm/requestForm')
require('./Checkout/checkout')
require('./Stepper/stepper')
require('./SearchParams/search-params')
require('./Search/search-bar')
require('./Product/scroll-to-description')
require('./Modals/al-modal')
require('./UploadFileInputs/upload-photos-form')
require('./ImgZoom/imgZoom')

window.utils = require('./utils')
window.cookies = require('js-cookie')