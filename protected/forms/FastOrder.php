<?php
/**
 * User: tatarinov
 * Date: 20.10.12
 */
return array('class'       => 'form clearfix',
             'description' => 'Поля <span class="required">*</span> - обязательны для заполнения.',
             'elements'    => array(
               'product_id' => array('type' => 'hidden'),
               'action'     => array('type' => 'hidden'),
               'name'       => array('type' => 'text'),
               'phone'      => array('type' => 'text'),
               ),
             'buttons'     => array('submit'   => array('type'  => 'image',
                                                        'src'   => 'i/order_btn.png',
                                                        'class' => 'left'),

                                    'toBasket' => array('type'  => 'image',
                                                        'src'   => 'i/basket_btn.png',
                                                        'class' => 'right'),
                                    )
);