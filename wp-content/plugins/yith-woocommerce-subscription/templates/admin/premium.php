<style>
.section{
    margin-left: -20px;
    margin-right: -20px;
    font-family: "Raleway",san-serif;
}
.section h1{
    text-align: center;
    text-transform: uppercase;
    color: #808a97;
    font-size: 35px;
    font-weight: 700;
    line-height: normal;
    display: inline-block;
    width: 100%;
    margin: 50px 0 0;
}
.section ul{
    list-style-type: disc;
    padding-left: 15px;
}
.section:nth-child(even){
    background-color: #fff;
}
.section:nth-child(odd){
    background-color: #f1f1f1;
}
.section .section-title img{
    display: table-cell;
    vertical-align: middle;
    width: auto;
    margin-right: 15px;
}
.section h2,
.section h3 {
    display: inline-block;
    vertical-align: middle;
    padding: 0;
    font-size: 24px;
    font-weight: 700;
    color: #808a97;
    text-transform: uppercase;
}

.section .section-title h2{
    display: table-cell;
    vertical-align: middle;
    line-height: 25px;
}

.section-title{
    display: table;
}

.section h3 {
    font-size: 14px;
    line-height: 28px;
    margin-bottom: 0;
    display: block;
}

.section p{
    font-size: 13px;
    margin: 25px 0;
}
.section ul li{
    margin-bottom: 4px;
}
.landing-container{
    max-width: 750px;
    margin-left: auto;
    margin-right: auto;
    padding: 50px 0 30px;
}
.landing-container:after{
    display: block;
    clear: both;
    content: '';
}
.landing-container .col-1,
.landing-container .col-2{
    float: left;
    box-sizing: border-box;
    padding: 0 15px;
}
.landing-container .col-1 img{
    width: 100%;
}
.landing-container .col-1{
    width: 55%;
}
.landing-container .col-2{
    width: 45%;
}
.premium-cta{
    background-color: #808a97;
    color: #fff;
    border-radius: 6px;
    padding: 20px 15px;
}
.premium-cta:after{
    content: '';
    display: block;
    clear: both;
}
.premium-cta p{
    margin: 7px 0;
    font-size: 14px;
    font-weight: 500;
    display: inline-block;
    width: 60%;
}
.premium-cta a.button{
    border-radius: 6px;
    height: 60px;
    float: right;
    background: url(<?php echo YITH_YWSBS_ASSETS_URL?>/images/upgrade.png) #ff643f no-repeat 13px 13px;
    border-color: #ff643f;
    box-shadow: none;
    outline: none;
    color: #fff;
    position: relative;
    padding: 9px 50px 9px 70px;
}
.premium-cta a.button:hover,
.premium-cta a.button:active,
.premium-cta a.button:focus{
    color: #fff;
    background: url(<?php echo YITH_YWSBS_ASSETS_URL?>/images/upgrade.png) #971d00 no-repeat 13px 13px;
    border-color: #971d00;
    box-shadow: none;
    outline: none;
}
.premium-cta a.button:focus{
    top: 1px;
}
.premium-cta a.button span{
    line-height: 13px;
}
.premium-cta a.button .highlight{
    display: block;
    font-size: 20px;
    font-weight: 700;
    line-height: 20px;
}
.premium-cta .highlight{
    text-transform: uppercase;
    background: none;
    font-weight: 800;
    color: #fff;
}

.section.one{
    background: url(<?php echo YITH_YWSBS_ASSETS_URL ?>/images/01-bg.png) no-repeat #fff; background-position: 85% 75%
}
.section.two{
    background: url(<?php echo YITH_YWSBS_ASSETS_URL ?>/images/02-bg.png) no-repeat #fff; background-position: 15% 100%;
}
.section.three{
    background: url(<?php echo YITH_YWSBS_ASSETS_URL ?>/images/03-bg.png) no-repeat #fff; background-position: 85% 75%
}
.section.four{
    background: url(<?php echo YITH_YWSBS_ASSETS_URL ?>/images/04-bg.png) no-repeat #fff; background-position: 15% 100%;
}
.section.five{
    background: url(<?php echo YITH_YWSBS_ASSETS_URL ?>/images/05-bg.png) no-repeat #fff; background-position: 85% 75%
}
.section.six{
    background: url(<?php echo YITH_YWSBS_ASSETS_URL ?>/images/06-bg.png) no-repeat #fff; background-position: 15% 100%;
}
.section.seven{
    background: url(<?php echo YITH_YWSBS_ASSETS_URL ?>/images/07-bg.png) no-repeat #fff; background-position: 85% 75%;
}
.section.eight{
    background: url(<?php echo YITH_YWSBS_ASSETS_URL ?>/images/08-bg.png) no-repeat #fff; background-position: 15% 100%;
}
.section.nine{
    background: url(<?php echo YITH_YWSBS_ASSETS_URL ?>/images/09-bg.png) no-repeat #fff; background-position: 85% 75%;
}
.section.ten{
    background: url(<?php echo YITH_YWSBS_ASSETS_URL ?>/images/10-bg.png) no-repeat #fff; background-position: 15% 100%;
}
.section.eleven{
    background: url(<?php echo YITH_YWSBS_ASSETS_URL ?>/images/11-bg.png) no-repeat #fff; background-position: 85% 75%;
}
.section.twelve{
    background: url(<?php echo YITH_YWSBS_ASSETS_URL ?>/images/12-bg.png) no-repeat #fff; background-position: 15% 100%;
}
.section.thirteen{
    background: url(<?php echo YITH_YWSBS_ASSETS_URL ?>/images/13-bg.png) no-repeat #fff; background-position: 85% 75%;
}
.section.fourteen{
    background: url(<?php echo YITH_YWSBS_ASSETS_URL ?>/images/14-bg.png) no-repeat #fff; background-position: 15% 100%;
}
.section.fifteen{
    background: url(<?php echo YITH_YWSBS_ASSETS_URL ?>/images/15-bg.png) no-repeat #fff; background-position: 85% 75%;
}
.section.sixteen{
    background: url(<?php echo YITH_YWSBS_ASSETS_URL ?>/images/16-bg.png) no-repeat #fff; background-position: 15% 100%;
}


@media (max-width: 768px) {
    .section{margin: 0}
    .premium-cta p{
        width: 100%;
    }
    .premium-cta{
        text-align: center;
    }
    .premium-cta a.button{
        float: none;
    }
}

@media (max-width: 480px){
    .wrap{
        margin-right: 0;
    }
    .section{
        margin: 0;
    }
    .landing-container .col-1,
    .landing-container .col-2{
        width: 100%;
        padding: 0 15px;
    }
    .section-odd .col-1 {
        float: left;
        margin-right: -100%;
    }
    .section-odd .col-2 {
        float: right;
        margin-top: 65%;
    }
}

@media (max-width: 320px){
    .premium-cta a.button{
        padding: 9px 20px 9px 70px;
    }

    .section .section-title img{
        display: none;
    }
}
</style>
<div class="landing">
    <div class="section section-cta section-odd">
        <div class="landing-container">
            <div class="premium-cta">
                <p>
                    <?php echo sprintf( __('Upgrade to %1$spremium version%2$s of %1$sYITH WooCommerce Subscription%2$s to benefit from all features!','yith-woocommerce-subscription'),'<span class="highlight">','</span>' );?>
                </p>
                <a href="<?php echo $this->get_premium_landing_uri() ?>" target="_blank" class="premium-cta-button button btn">
                    <span class="highlight"><?php _e('UPGRADE','yith-woocommerce-subscription');?></span>
                    <span><?php _e('to the premium version','yith-woocommerce-subscription');?></span>
                </a>
            </div>
        </div>
    </div>
    <div class="one section section-even clear">
        <h1><?php _e('Premium Features','yith-woocommerce-subscription');?></h1>
        <div class="landing-container">
            <div class="col-1">
                <img src="<?php echo YITH_YWSBS_ASSETS_URL ?>/images/01.png" alt="Feature 01" />
            </div>
            <div class="col-2">
                <div class="section-title">
                    <h2><?php _e('Support to variable products','yith-woocommerce-subscription');?></h2>
                </div>
                <p>
                    <?php echo sprintf(__('Extend subscription possibilities to every product in your shop! With the premium version of this plugin you can configure %1$sdifferent types of subscription%2$s for the same product and exploit the potential of WooCommerce variable products. Free to act as you wish!', 'yith-woocommerce-subscription'), '<b>', '</b>');?>
                </p>
            </div>
        </div>
    </div>
    <div class="two section section-odd clear">
        <div class="landing-container">
            <div class="col-2">
                <div class="section-title">
                    <h2><?php _e('Pause the subscription','yith-woocommerce-subscription');?></h2>
                </div>
                <p>
                    <?php echo sprintf(__('Users can pause their own subscription from their reserved area: easy! This way, they can benefit from contents they are paying for in the best way and will be able to suspend the subscription for the time they cannot use it. %3$s Consequently %1$ssubscription expiry date will be postponed as well%2$s.', 'yith-woocommerce-subscription'), '<b>', '</b>','<br>');?>
                </p>
            </div>
            <div class="col-1">
                <img src="<?php echo YITH_YWSBS_ASSETS_URL ?>/images/02.png" alt="feature 02" />
            </div>
        </div>
    </div>
    <div class="three section section-even clear">
        <div class="landing-container">
            <div class="col-1">
                <img src="<?php echo YITH_YWSBS_ASSETS_URL ?>/images/03.png" alt="Feature 03" />
            </div>
            <div class="col-2">
                <div class="section-title">
                    <h2><?php _e( 'Defer payment','yith-woocommerce-subscription');?></h2>
                </div>
                <p>
                    <?php echo sprintf(__('Allow payment deferment if you do not want that the subscription is deleted as soon as a fee is not paid. Whatever could be the reason for a missing payment. Set the duration of a deferment period during which users will be able to %1$scatch up missed payments%2$s: during this time, you can still allow access to content or hide it temporarily. ', 'yith-woocommerce-subscription'), '<b>', '</b>');?>
                </p>
            </div>
        </div>
    </div>
    <div class="four section section-odd clear">
        <div class="landing-container">
            <div class="col-2">
                <div class="section-title">
                    <h2><?php _e('Trial period','yith-woocommerce-subscription');?></h2>
                </div>
                <p>
                    <?php echo sprintf(__('Use the most common strategy on the market to convince your customers about what you offer.%1$s Give them the opportunity to access at no costs%2$s. When trial period ends and if users are interested in it, they will be able to subscribe.', 'yith-woocommerce-subscription'), '<b>', '</b>','<br>');?>
                </p>
            </div>
            <div class="col-1">
                <img src="<?php echo YITH_YWSBS_ASSETS_URL ?>/images/04.png" alt="Feature 04" />
            </div>
        </div>
    </div>
    <div class="five section section-even clear">
        <div class="landing-container">
            <div class="col-1">
                <img src="<?php echo YITH_YWSBS_ASSETS_URL ?>/images/05.png" alt="Feature 05" />
            </div>
            <div class="col-2">
                <div class="section-title">
                    <h2><?php _e('Sign-up fee','yith-woocommerce-subscription');?></h2>
                </div>
                <p>
                    <?php echo sprintf( __('All users willing to subscribe will pay a one-time fee when signing up in addition to periodical fees. You can choose to ask for a %1$ssign-up fee%2$s or ask only for the periodical fee.','yith-woocommerce-subscription'),'<b>','</b>'); ?>
                </p>
            </div>
        </div>
    </div>
    <div class="six section section-odd clear">
        <div class="landing-container">
            <div class="col-2">
                <div class="section-title">
                    <h2><?php _e('Cancel and renew subscription','yith-woocommerce-subscription');?></h2>
                </div>
                <p>
                    <?php echo sprintf( __('From "My Account" page, every user will be able to %1$smanage cancellation and/or renewal%2$s of the active subscription.%3$s Give your users as many tools as you can to improve services in your shop.','yith-woocommerce-subscription'),'<b>','</b>','<br>'); ?>
                </p>
            </div>
            <div class="col-1">
                <img src="<?php echo YITH_YWSBS_ASSETS_URL ?>/images/06.png" alt="Feature 06" />
            </div>
        </div>
    </div>
    <div class="seven section section-even clear">
        <div class="landing-container">
            <div class="col-1">
                <img src="<?php echo YITH_YWSBS_ASSETS_URL ?>/images/07.png" alt="Feature 07" />
            </div>
            <div class="col-2">
                <div class="section-title">
                    <h2><?php _e('Subscription detail page','yith-woocommerce-subscription');?></h2>
                </div>
                <p>
                    <?php echo sprintf( __('For each subscription plan both users and admin can access to %1$sdetailed information%2$s, such as subscription start and end date or info about products associated to the subscription.','yith-woocommerce-subscription'),'<b>','</b>'); ?>
                </p>
            </div>
        </div>
    </div>
    <div class="eight section section-odd clear">
        <div class="landing-container">
            <div class="col-2">
                <div class="section-title">
                    <h2><?php _e( 'Upgrade and downgrade','yith-woocommerce-subscription');?></h2>
                </div>
                <p>
                    <?php echo sprintf( __('Create subscription plans with different content and give users the possibility to %1$sswitch from a plan to another%2$s. They just have to click on the specific button available in "My Account" page and, starting from the following month, they will begin paying for and benefit from the new plan they have subscribed.','yith-woocommerce-subscription'),'<b>','</b>','<br>'); ?>
                </p>
            </div>
            <div class="col-1">
                <img src="<?php echo YITH_YWSBS_ASSETS_URL ?>/images/08.png" alt="Feature 08" />
            </div>
        </div>
    </div>
    <div class="nine section section-even clear">
        <div class="landing-container">
            <div class="col-1">
                <img src="<?php echo YITH_YWSBS_ASSETS_URL ?>/images/09.png" alt="Feature 09" />
            </div>
            <div class="col-2">
                <div class="section-title">
                    <h2><?php _e('Send emails automatically ','yith-woocommerce-subscription');?></h2>
                </div>
                <p>
                    <?php echo sprintf( __('%1$sGet emails each time a subscription plan is paused or cancelled%2$s. At the same time, let your customers receive emails also when the payment is made, when the subscription is going to expire or when it is stopped, cancelled or resumed.','yith-woocommerce-subscription'),'<b>','</b>'); ?>
                </p>
            </div>
        </div>
    </div>
    <div class="ten section section-odd clear">
        <div class="landing-container">
            <div class="col-2">
                <div class="section-title">
                    <h2><?php _e( 'Coupons','yith-woocommerce-subscription');?></h2>
                </div>
                <p>
                    <?php echo sprintf( __( 'Two new types of coupons have been added to the standard ones provided by WooCommerce. Configure specific coupons that give your users discounts on %1$ssign-up fee%2$s or on the %1$srecurring subscription fee.%2$s','yith-woocommerce-subscription'),'<b>','</b>','<br>'); ?>
                </p>
            </div>
            <div class="col-1">
                <img src="<?php echo YITH_YWSBS_ASSETS_URL ?>/images/10.png" alt="Feature 10" />
            </div>
        </div>
    </div>
    <div class="eleven section section-even clear">
        <div class="landing-container">
            <div class="col-1">
                <img src="<?php echo YITH_YWSBS_ASSETS_URL ?>/images/11.png" alt="Feature 11" />
            </div>
            <div class="col-2">
                <div class="section-title">
                    <h2><?php _e('Activity list','yith-woocommerce-subscription');?></h2>
                </div>
                <p>
                    <?php echo sprintf( __('All activities made on %1$ssubscriptions plans are recorded and added in a related table%2$s. You will have control of every change, tracking all subscriptions made in your shop.','yith-woocommerce-subscription'),'<b>','</b>'); ?>
                </p>
            </div>
        </div>
    </div>
    <div class="twelve section section-odd clear">
        <div class="landing-container">
            <div class="col-2">
                <div class="section-title">
                    <h2><?php _e( 'Integration with YITH WooCommerce Membership','yith-woocommerce-subscription');?></h2>
                </div>
                <p>
                    <?php echo sprintf( __( 'By combining these two powerful plugins, %1$syou will be able to create different membership plans and associate them to different subscription plans%2$s. What\'s the difference? Subscription gives temporary and periodical access to contents, membership gives access to different content elements in your site. A perfect combination!','yith-woocommerce-subscription'),'<b>','</b>','<br>'); ?>
                </p>
            </div>
            <div class="col-1">
                <img src="<?php echo YITH_YWSBS_ASSETS_URL ?>/images/12.png" alt="Feature 12" />
            </div>
        </div>
    </div>
    <div class="thirteen section section-even clear">
        <div class="landing-container">
            <div class="col-1">
                <img src="<?php echo YITH_YWSBS_ASSETS_URL ?>/images/13.png" alt="Feature 13" />
            </div>
            <div class="col-2">
                <div class="section-title">
                    <h2><?php _e('Cancel subscription','yith-woocommerce-subscription');?></h2>
                </div>
                <p>
                    <?php echo sprintf( __('The subscription is automatically removed from database in case the order associated to it is cancelled by the admin. %1$sNo management problems%2$s, the plugin does all for you.','yith-woocommerce-subscription'),'<b>','</b>'); ?>
                </p>
            </div>
        </div>
    </div>
    <div class="fourteen section section-odd clear">
        <div class="landing-container">
            <div class="col-2">
                <div class="section-title">
                    <h2><?php _e( 'Suspend subscription automatically','yith-woocommerce-subscription');?></h2>
                </div>
                <p>
                    <?php echo sprintf( __( 'Did your subscription %1$srecurring spayment fail?%2$s%3$s Consider also the possibility for this to happen and %1$senable subscription automatic suspension%2$s.%3$s As soon as the payment succeeds the active status will be automatically restored','yith-woocommerce-subscription'),'<b>','</b>','<br>'); ?>
                </p>
            </div>
            <div class="col-1">
                <img src="<?php echo YITH_YWSBS_ASSETS_URL ?>/images/14.png" alt="Feature 14" />
            </div>
        </div>
    </div>
    <div class="fifteen section section-even clear">
        <div class="landing-container">
            <div class="col-1">
                <img src="<?php echo YITH_YWSBS_ASSETS_URL ?>/images/15.png" alt="Feature 15" />
            </div>
            <div class="col-2">
                <div class="section-title">
                    <h2><?php _e('Three failed payment attempts','yith-woocommerce-subscription');?></h2>
                </div>
                <p>
                    <?php echo sprintf( __('No subscription will be set to "Pending" for too long: after three failed payment attempts, the subscription will be %1$sautomatically switched to "cancelled"%2$s and no possibility to restore it is given. This behaviour is applicable to payments made via Stripe or PayPal.','yith-woocommerce-subscription'),'<b>','</b>'); ?>
                </p>
            </div>
        </div>
    </div>
    <div class="sixteen section section-odd clear">
        <div class="landing-container">
            <div class="col-2">
                <div class="section-title">
                    <h2><?php _e( 'Postpone status switch','yith-woocommerce-subscription');?></h2>
                </div>
                <p>
                    <?php echo sprintf( __( 'Some issues related to correct %1$ssyncronisation with PayPal and Stripe service%2$s could cause delays in receiving subscription payments.%3$s To consider also this possibility, you can set a grace period (set in hours) before the current status is switched to "overdue", "suspended" or "cancelled".','yith-woocommerce-subscription'),'<b>','</b>','<br>'); ?>
                </p>
            </div>
            <div class="col-1">
                <img src="<?php echo YITH_YWSBS_ASSETS_URL ?>/images/16.png" alt="Feature 16" />
            </div>
        </div>
    </div>
    <div class="section section-cta section-odd">
        <div class="landing-container">
            <div class="premium-cta">
                <p>
                    <?php echo sprintf( __('Upgrade to %1$spremium version%2$s of %1$sYITH WooCommerce Subscription%2$s to benefit from all features!','yith-woocommerce-subscription'),'<span class="highlight">','</span>' );?>
                </p>
                <a href="<?php echo $this->get_premium_landing_uri() ?>" target="_blank" class="premium-cta-button button btn">
                    <span class="highlight"><?php _e('UPGRADE','yith-woocommerce-subscription');?></span>
                    <span><?php _e('to the premium version','yith-woocommerce-subscription');?></span>
                </a>
            </div>
        </div>
    </div>
</div>