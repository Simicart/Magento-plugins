/**
 * Copyright © 2015 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
/*jshint browser:true jquery:true*/
/*global alert*/
define(
    [
        'ko',
        'uiComponent',
    ],
    function (ko,Component) {
        return Component.extend({
            enableReward:function () {
                return minicartRewardpoints.enableReward;
            },
            customerLogin: function () {
                if (minicartRewardpoints.customerLogin) {
                    return minicartRewardpoints.customerLogin;
                } else {
                    return false;
                }
            },
            earnPoint: function () {
                if (minicartRewardpoints.earnPoint) {
                    return  minicartRewardpoints.earnPoint;
                } else {
                    return false;
                }
            },
            urlRedirectLogin:function () {
                if (minicartRewardpoints.urlRedirectLogin) {
                    return minicartRewardpoints.urlRedirectLogin;
                } else {
                    return false;
                }
            },
            getImageHtml:function () {
                if (minicartRewardpoints.getImageHtml) {
                    return minicartRewardpoints.getImageHtml;
                } else {
                    return false;
                }
            }

        })

    }
)
