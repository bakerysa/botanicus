<div class="wrap">
    <h1><?php _e('Subscriptions', 'yith-woocommerce-subscription') ?></h1>

    <div id="poststuff">
        <div id="post-body" class="metabox-holder">
            <div id="post-body-content">
                <div class="meta-box-sortables ui-sortable">
                    <form method="post">
                        <?php
                        $this->cpt_obj_subscriptions->prepare_items();
                        $this->cpt_obj_subscriptions->display(); ?>
                    </form>
                </div>
            </div>
        </div>
        <br class="clear">
    </div>
</div>