<?php

use Phinx\Migration\AbstractMigration;

class CreateUsers extends AbstractMigration
{
    public function change()
    {
        $table = $this->table('users');
        $table->addColumn('name', 'string')
            ->addColumn('username', 'string')
            ->addColumn('password', 'string')
            ->addColumn('email', 'string')
            ->addColumn('email_token', 'string', ['null' => true])
            ->addColumn('reset_token', 'string', ['null' => true])
            ->addColumn('role', 'integer')
            ->addColumn('state', 'integer')
            ->addTimestamps(null, null)
            ->addColumn('deleted_at', 'timestamp', ['null' => true])
            ->addIndex(['email', 'email_token', 'reset_token'], ['unique' => true])
            ->create();
    }
}
