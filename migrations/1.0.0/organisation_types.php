<?php

use Phalcon\Db\Column;
use Phalcon\Db\Index;
use Phalcon\Db\Reference;
use Phalcon\Mvc\Model\Migration;

/**
 * Class OrganisationTypesMigration_100
 */
class OrganisationTypesMigration_100 extends Migration
{
    /**
     * Define the table structure
     *
     * @return void
     */
    public function morph()
    {
        $this->morphTable('organisation_types', array(
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
                        'name',
                        array(
                            'type' => Column::TYPE_VARCHAR,
                            'notNull' => true,
                            'size' => 150,
                            'after' => 'id'
                        )
                    ),
                    new Column(
                        'created_at',
                        array(
                            'type' => Column::TYPE_DATETIME,
                            'after' => 'name'
                        )
                    ),
                    new Column(
                        'created_by',
                        array(
                            'type' => Column::TYPE_INTEGER,
                            'unsigned' => true,
                            'after' => 'created_at'
                        )
                    ),
                    new Column(
                        'updated_by',
                        array(
                            'type' => Column::TYPE_INTEGER,
                            'unsigned' => true,
                            'after' => 'created_at'
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
                    new Index('created_by', array('created_by')),
                    new Index('updated_by', array('updated_by')),
                    new Index('deleted_by', array('deleted_by'))
                ),
                "references" => array(
                    new Reference(
                        "created_byfk_organisation_types",
                        array(
                            "referencedSchema"  => "signadens",
                            "referencedTable"   => "users",
                            "columns"           => array("created_by"),
                            "referencedColumns" => array("id")
                        )
                    ),
                    new Reference(
                        "updated_byfk_organisation_types",
                        array(
                            "referencedSchema"  => "signadens",
                            "referencedTable"   => "users",
                            "columns"           => array("updated_by"),
                            "referencedColumns" => array("id")
                        )
                    ),
                    new Reference(
                        "deleted_byfk_organisation_types",
                        array(
                            "referencedSchema"  => "signadens",
                            "referencedTable"   => "users",
                            "columns"           => array("deleted_by"),
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