<?php

use Phalcon\Db\Column;
use Phalcon\Db\Index;
use Phalcon\Db\Reference;
use Phalcon\Mvc\Model\Migration;

/**
 * Class UsersMigration_100
 */
class UsersMigration_100 extends Migration
{
    /**
     * Define the table structure
     *
     * @return void
     */
    public function morph()
    {
        $this->morphTable('users', array(
                'columns' => array(
                    new Column(
                        'id',
                        array(
                            'type' => Column::TYPE_INTEGER,
                            'unsigned' => true,
                            'notNull' => true,
                            'autoIncrement' => true,
                            'size' => 11,
                            'first' => true
                        )
                    ),
                    new Column(
                        'email',
                        array(
                            'type' => Column::TYPE_VARCHAR,
                            'notNull' => true,
                            'size' => 150,
                            'after' => 'id'
                        )
                    ),
                    new Column(
                        'password',
                        array(
                            'type' => Column::TYPE_VARCHAR,
                            'size' => 64,
                            'after' => 'email'
                        )
                    ),
                    new Column(
                        'last_login',
                        array(
                            'type' => Column::TYPE_DATETIME,
                            'after' => 'password'
                        )
                    ),
                    new Column(
                        'firstname',
                        array(
                            'type' => Column::TYPE_VARCHAR,
                            'size' => 64,
                            'after' => 'last_login'
                        )
                    ),
                    new Column(
                        'lastname',
                        array(
                            'type' => Column::TYPE_VARCHAR,
                            'size' => 64,
                            'after' => 'firstname'
                        )
                    ),
                    new Column(
                        'telephone',
                        array(
                            'type' => Column::TYPE_VARCHAR,
                            'size' => 32,
                            'after' => 'lastname'
                        )
                    ),
                    new Column(
                        'active',
                        array(
                            'type' => Column::TYPE_INTEGER,
                            'unsigned' => true,
                            'notNull' => true,
                            'size' => 1,
                            'default' => 0,
                            'after' => 'telephone'
                        )
                    ),
                    new Column(
                        'deleted',
                        array(
                            'type' => Column::TYPE_INTEGER,
                            'unsigned' => true,
                            'notNull' => true,
                            'size' => 1,
                            'default' => 0,
                            'after' => 'active'
                        )
                    ),
                    new Column(
                        'organisation_id',
                        array(
                            'type' => Column::TYPE_INTEGER,
                            'unsigned' => true,
                            'after' => 'deleted'
                        )
                    ),
                    new Column(
                        'role_template_id',
                        array(
                            'type' => Column::TYPE_INTEGER,
                            'unsigned' => true,
                            'after' => 'organisation_id'
                        )
                    ),
                    new Column(
                        'created_at',
                        array(
                            'type' => Column::TYPE_DATETIME,
                            'after' => 'role_template_id'
                        )
                    ),
                    new Column(
                        'created_by',
                        array(
                            'type' => Column::TYPE_INTEGER,
                            'after' => 'created_at'
                        )
                    ),
                    new Column(
                        'updated_by',
                        array(
                            'type' => Column::TYPE_INTEGER,
                            'unsigned' => true,
                            'after' => 'created_by'
                        )
                    ),
                    new Column(
                        'updated_at',
                        array(
                            'type' => Column::TYPE_DATETIME,
                            'after' => 'updated_by'
                        )
                    ),
                    new Column(
                        'deleted_by',
                        array(
                            'type' => Column::TYPE_INTEGER,
                            'unsigned' => true,
                            'after' => 'updated_at'
                        )
                    ),
                    new Column(
                        'deleted_at',
                        array(
                            'type' => Column::TYPE_DATETIME,
                            'after' => 'deleted_by'
                        )
                    )
                ),
                'indexes' => array(
                    new Index('PRIMARY', array('id')),
                    new Index('organisation_id', array('organisation_id')),
                    new Index('role_template_id', array('role_template_id')),
//                    new Index('created_by', array('created_by')),
//                    new Index('updated_by', array('updated_by')),
//                    new Index('deleted_by', array('deleted_by'))
                ),
                "references" => array(
                    new Reference(
                        "organisation_idfk_users",
                        array(
                            "referencedSchema"  => "signadens",
                            "referencedTable"   => "organisations",
                            "columns"           => array("organisation_id"),
                            "referencedColumns" => array("id")
                        )
                    ),
                    new Reference(
                        "role_template_idfk_users",
                        array(
                            "referencedSchema"  => "signadens",
                            "referencedTable"   => "role_templates",
                            "columns"           => array("role_template_id"),
                            "referencedColumns" => array("id")
                        )
                    )
//                    new Reference(
//                        "created_byfk_users",
//                        array(
//                            "referencedSchema"  => "signadens",
//                            "referencedTable"   => "users",
//                            "columns"           => array("created_by"),
//                            "referencedColumns" => array("id")
//                        )
//                    ),
//                    new Reference(
//                        "updated_byfk_users",
//                        array(
//                            "referencedSchema"  => "signadens",
//                            "referencedTable"   => "users",
//                            "columns"           => array("updated_by"),
//                            "referencedColumns" => array("id")
//                        )
//                    ),
//                    new Reference(
//                        "deleted_byfk_users",
//                        array(
//                            "referencedSchema"  => "signadens",
//                            "referencedTable"   => "users",
//                            "columns"           => array("deleted_by"),
//                            "referencedColumns" => array("id")
//                        )
//                    )
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