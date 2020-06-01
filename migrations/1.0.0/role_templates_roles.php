<?php

use Phalcon\Db\Column;
use Phalcon\Db\Index;
use Phalcon\Db\Reference;
use Phalcon\Mvc\Model\Migration;

/**
 * Class RoleTemplatesRolesMigration_100
 */
class RoleTemplatesRolesMigration_100 extends Migration
{
    /**
     * Define the table structure
     *
     * @return void
     */
    public function morph()
    {
        $this->morphTable('role_templates_roles', array(
                'columns' => array(
                    new Column(
                        'role_template_id',
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
                    new Index('role_template_id', array('role_template_id')),
                    new Index('role_id', array('role_id'))
                ),
                "references" => array(
                    new Reference(
                        "role_template_idfk_role_templates_roles",
                        array(
                            "referencedSchema"  => "signadens",
                            "referencedTable"   => "role_templates",
                            "columns"           => array("role_template_id"),
                            "referencedColumns" => array("id")
                        )
                    ),
                    new Reference(
                        "role_idfk_role_templates_roles",
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