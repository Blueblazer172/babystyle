<?php
return array(
    'components'=>array(
        //Database of Magento1
        'mage1' => array(
            'connectionString' => 'mysql:host=localhost;dbname=babystyle1',
            'emulatePrepare' => true,
            'username' => 'babystyle1',
            'password' => 'PKAMFSTwcupXf3aq',
            'charset' => 'utf8',
            'tablePrefix' => '',
            'class' => 'CDbConnection'
        ),
        //Database of Magento2 beta
        'mage2' => array(
            'connectionString' => 'mysql:host=localhost;dbname=babystyle',
            'emulatePrepare' => true,
            'username' => 'babystyle',
            'password' => 'jHjGVupWz6rf93mT',
            'charset' => 'utf8',
            'tablePrefix' => '',
            'class' => 'CDbConnection'
        )
    ),

    'import'=>array(
        //This can change for your magento1 version if needed
        //'application.models.db.mage19x.*',
        'application.models.db.mage19x.*',
    )
);
