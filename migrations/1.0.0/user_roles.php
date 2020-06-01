<?php

use Phalcon\Db\Column;
use Phalcon\Db\Index;
use Phalcon\Db\Reference;
use Phalcon\Mvc\Model\Migration;

/**
 * Class UserRolesMigration_100
 */
class UserRolesMigration_100 extends Migration
{
    /**
     * Define the table structure
     *
     * @return void
     */
    public function morph()
    {
        $this->morphTable('user_roles', array(
                'columns' => array(
                    new Column(
                        'user_id',
                        array(
                            'type' => Column::TYPE_INTEGER,
                            'unsigned' => true
                        )
                    ),
                    new Column(
                        'role_id',
                        array(
                            'type' => Column::TYPE_INTEGER,
                            'unsigned' => true
                        )
                    )
                ),
                'indexes' => array(
                    new Index('user_id', array('user_id')),
                    new Index('role_id', array('role_id'))
                ),
                "references" => array(
                    new Reference(
                        "user_idfk_user_roles",
                        array(
                            "referencedSchema"  => "signadens",
                            "referencedTable"   => "users",
                            "columns"           => array("user_id"),
                            "referencedColumns" => array("id")
                        )
                    ),
                    new Reference(
                        "role_idfk_user_roles",
                        array(
                            "referencedSchema"  => "signadens",
                            "referencedTable"   => "roles",
                            "columns"           => array("role_id"),
                            "referencedColumns" => array("id")
                        )
                    )
                ),
                'options' => array(
                    'TABLE_TYPE' => 'BASE TABLE',
                    'ENGINE' => 'InnoDB',
                    'TABLE_COLLATION' => 'utf8_general_ci'
                ),
            )
        );
    }

    /**
     * Run the migrations
     *
     * @return void
     */
    public function up()
    {

    }

    /**
     * Reverse the migrations
     *
     * @return void
     */
    public function down()
    {

    }

}