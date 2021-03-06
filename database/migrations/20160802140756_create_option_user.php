<?php

use Phinx\Migration\AbstractMigration;

class CreateOptionUser extends AbstractMigration
{
    public function change()
    {
        $table = $this->table('option_user');
        $table->addColumn('user_id', 'integer')
            ->addColumn('option_id', 'integer')
            ->addColumn('value', 'string')
            ->addForeignKey('user_id', 'users', 'id', ['delete'=> 'CASCADE', 'update'=> 'NO_ACTION'])
            ->addForeignKey('option_id', 'options', 'id', ['delete'=> 'CASCADE', 'update'=> 'NO_ACTION'])
            ->addTimestamps(null, null)
            ->create();
    }
}
