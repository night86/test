<?php

use Phalcon\Db\Column;
use Phalcon\Db\Index;
use Phalcon\Db\Reference;
use Phalcon\Mvc\Model\Migration;

/**
 * Class CountriesMigration_100
 */
class CountriesMigration_100 extends Migration
{
    /**
     * Define the table structure
     *
     * @return void
     */
    public function morph()
    {
        $this->morphTable('countries', array(
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
                        'code',
                        array(
                            'type' => Column::TYPE_VARCHAR,
                            'size' => 64,
                            'after' => 'name'
                        )
                    ),
                    new Column(
                        'created_at',
                        array(
                            'type' => Column::TYPE_DATETIME,
                            'after' => 'code'
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
                    new Index('created_by', array('created_by')),
                    new Index('updated_by', array('updated_by')),
                    new Index('deleted_by', array('deleted_by'))
                ),
                "references" => array(
                    new Reference(
                        "created_byfk_countries",
                        array(
                            "referencedSchema"  => "signadens",
                            "referencedTable"   => "users",
                            "columns"           => array("created_by"),
                            "referencedColumns" => array("id")
                        )
                    ),
                    new Reference(
                        "updated_byfk_countries",
                        array(
                            "referencedSchema"  => "signadens",
                            "referencedTable"   => "users",
                            "columns"           => array("updated_by"),
                            "referencedColumns" => array("id")
                        )
                    ),
                    new Reference(
                        "deleted_byfk_countries",
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