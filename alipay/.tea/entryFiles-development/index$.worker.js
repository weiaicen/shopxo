require('./config$');

function success() {
require('../..//app');
require('../../pages/index/index');
require('../../pages/user/user');
require('../../pages/web-view/web-view');
require('../../pages/login/login');
require('../../pages/paytips/paytips');
require('../../pages/goods-search/goods-search');
require('../../pages/goods-detail/goods-detail');
require('../../pages/goods-attribute/goods-attribute');
require('../../pages/buy/buy');
require('../../pages/user-address/user-address');
require('../../pages/user-address-save/user-address-save');
require('../../pages/user-order/user-order');
require('../../pages/user-order-detail/user-order-detail');
require('../../pages/user-faovr/user-faovr');
require('../../pages/user-answer-list/user-answer-list');
require('../../pages/answer-list/answer-list');
require('../../pages/answer-form/answer-form');
}
self.bootstrapApp ? self.bootstrapApp({ success }) : success();
