<?php

$settings = array(

    'general' => array(

            'section_general_settings'     => array(
                'name' => __( 'General settings', 'yith-woocommerce-subscription' ),
                'type' => 'title',
                'id'   => 'ywsbs_section_general'
            ),

            'enabled' => array(
                'name'    =>  __( 'Enable Subscription', 'yith-woocommerce-subscription' ),
                'desc'    => '',
                'id'      => 'ywsbs_enabled',
                'type'    => 'checkbox',
                'default' => 'yes'
            ),


            'section_end_form'=> array(
                'type'              => 'sectionend',
                'id'                => 'ywsbs_section_general_end_form'
            ),
        )

);

return apply_filters( 'yith_ywsbs_panel_settings_options', $settings );