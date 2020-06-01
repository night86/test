<?php

use Phalcon\Db\Column;
use Phalcon\Db\Index;
use Phalcon\Db\Reference;
use Phalcon\Mvc\Model\Migration;

/**
 * Class OrganisationsMigration_100
 */
class OrganisationsMigration_100 extends Migration
{
    /**
     * Define the table structure
     *
     * @return void
     */
    public function morph()
    {
        $this->morphTable('organisations', array(
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
                        'email',
                        array(
                            'type' => Column::TYPE_VARCHAR,
                            'notNull' => true,
                            'size' => 150,
                            'after' => 'name'
                        )
                    ),
                    new Column(
                        'telephone',
                        array(
                            'type' => Column::TYPE_VARCHAR,
                            'size' => 32,
                            'after' => 'email'
                        )
                    ),
                    new Column(
                        'active',
                        array(
                            'type' => Column::TYPE_INTEGER,
                            'unsigned' => true,
                            'notNull' => true,
                            'size' => 1,
                            'after' => 'telephone'
                        )
                    ),
                    new Column(
                        'address',
                        array(
                            'type' => Column::TYPE_VARCHAR,
                            'size' => 100,
                            'after' => 'active'
                        )
                    ),
                    new Column(
                        'zipcode',
                        array(
                            'type' => Column::TYPE_VARCHAR,
                            'size' => 32,
                            'after' => 'address'
                        )
                    ),
                    new Column(
                        'city',
                        array(
                            'type' => Column::TYPE_VARCHAR,
                            'size' => 32,
                            'after' => 'zipcode'
                        )
                    ),
                    new Column(
                        'country_id',
                        array(
                            'type' => Column::TYPE_INTEGER,
                            'unsigned' => true,
                            'after' => 'city'
                        )
                    ),
                    new Column(
                        'organisation_type_id',
                        array(
                            'type' => Column::TYPE_INTEGER,
                            'unsigned' => true,
                            'after' => 'country_id'
                        )
                    ),
                    new Column(
                        'created_at',
                        array(
                            'type' => Column::TYPE_DATETIME,
                            'after' => 'organisation_type_id'
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
                        "created_byfk_organisations",
                        array(
                            "referencedSchema"  => "signadens",
                            "referencedTable"   => "users",
                            "columns"           => array("created_by"),
                            "referencedColumns" => array("id")
                        )
                    ),
                    new Reference(
                        "updated_byfk_organisations",
                        array(
                            "referencedSchema"  => "signadens",
                            "referencedTable"   => "users",
                            "columns"           => array("updated_by"),
                            "referencedColumns" => array("id")
                        )
                    ),
                    new Reference(
                        "deleted_byfk_organisations",
                        array(
                            "referencedSchema"  => "signadens",
                            "referencedTable"   => "users",
                            "columns"           => array("deleted_by"),
                            "referencedColumns" => array("id")
                        )
                    ),
                    new Reference(
                        "organisation_type_idfk_organisations",
                        array(
                            "referencedSchema"  => "signadens",
                            "referencedTable"   => "organisation_types",
                            "columns"           => array("organisation_type_id"),
                            "referencedColumns" => array("id")
                        )
                    ),
                    new Reference(
                        "country_idfk_organisations",
                        array(
                            "referencedSchema"  => "signadens",
                            "referencedTable"   => "countries",
                            "columns"           => array("country_id"),
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