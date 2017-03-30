<?php

/**
 * Promoteapp Content helper
 */

namespace Simi\Simipromoteapp\Helper;

class Content extends \Magento\Framework\App\Helper\AbstractHelper
{
    public function getRegisterSubject()
    {
        return 'Bravo! Mobile-only promo are waiting for you.';
    }

    public function getRegisterContent()
    {
        return 'Howdy,
                <br />Welcome you to join us!<br /><br />
                But wait...<br />
                Your journey is almost done, only one more step to get a pocketful of great shopping moments with us. 
                Yah, whether you want to shop in the palm of your hand, you are golden with our mega-convenient app.
                <br /><br />
                By downloading our app, you can:
                <br />- Shop hundreds of products at your fingertips, literally.
                <br />- Never miss out the hottest products & best deals for mobile only by checking notifications.
                <br />- Order your goodies anytime and anywhere with a variety of payment methods.
                <br />- No need to keep checking your balance manually, mobile passbook will do that job.
                <br /><br /><strong>Wait no more!</strong> Enjoy shopping with our app  right NOW: {{if ios_link}}
                <a href="{{var ios_link}}" target="_blank" title="iOs app">iTune</a>
                {{/if}}  {{if ios_link}}
                <a href="{{var android_link}}" target="_blank" title="Android app">Android</a>{{/if}}
                <img style="display: none;" src="{{var log_link}}" 
                alt="" width="0" height="0" border="0" />';
    }

    public function getSubscriberSubject()
    {
        return 'Thanks for visiting! Mobile-only promo are waiting for you.';
    }

    public function getSubscriberContent()
    {
        return 'Howdy,<br />
                Thank you for stopping by!<br /><br />
                We hope you"ve had an enjoyable time on your recent visit to our website. 
                Found something you liked? Save it for later by adding it to your personal wishlist or cart.<br />
                <br />Still on the fence?<br /><br />
                Any time you want to shop with us,
                 let our app stays on your phone home screen and give you a pocketful of great shopping moments.
                 Yah, whether you want to shop in the palm of your hand, you are golden with our mega-convenient app.
                 <br />
                <br />By downloading our app, you can:
                <br />- Shop hundreds of products at your fingertips, literally.
                <br />- Never miss out the hottest products & best deals for mobile only by checking notifications.
                <br />- Order your goodies anytime and anywhere with a variety of payment methods.
                <br />- No need to keep checking your balance manually, mobile passbook will do that job.
                <br /><br /><strong>Wait no more!</strong> Enjoy shopping with our app  right NOW: {{if ios_link}}
                <a href="{{var ios_link}}" 
                target="_blank" title="iOs app">iTune</a>{{/if}}  {{if ios_link}}
                <a href="{{var android_link}}" target="_blank" title="Android app">Android</a>{{/if}}
                <img style="display: none;" src="{{var log_link}}" 
                alt="" width="0" height="0" border="0" />';
    }

    public function getPurchasingSubject()
    {
        return 'Bing! Never miss the latest items again.';
    }

    public function getPurchasingContent()
    {
        return 'Howdy,<br />
                Thanks for your interest in our products!<br /><br />
                But wait...<br />
                Is it hard for you to shop online by scrolling our website on your gadgets?
                 It must be and it can be very time consuming.
                 Yah, whether you want to shop in the palm of your hand, you are golden with our mega-convenient app.
                 <br /><br />
                By downloading our app, you can:
                <br />- Shop hundreds of products at your fingertips, literally.
                <br />- Never miss out the hottest products & best deals for mobile only by checking notifications.
                <br />- Order your goodies anytime and anywhere with a variety of payment methods.
                <br />- No need to keep checking your balance manually, mobile passbook will do that job.
                <br /><br /><strong>Wait no more!</strong> Enjoy shopping with our app  right NOW: {{if ios_link}}
                <a href="{{var ios_link}}" target="_blank" title="iOs app">iTune</a>
                {{/if}}  {{if ios_link}}
                <a href="{{var android_link}}" target="_blank" title="Android app">Android</a>{{/if}}
                <img style="display: none;" 
                src="{{var log_link}}" alt="" width="0" height="0" border="0" />';
    }
}
