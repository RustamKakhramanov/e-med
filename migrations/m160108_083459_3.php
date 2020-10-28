<?php

/**
 * справочник мкб
 */
use yii\db\Schema;
use yii\db\Migration;

class m160108_083459_3 extends Migration {

    public function up() {
        $path = Yii::$app->basePath . DIRECTORY_SEPARATOR . 'src' . DIRECTORY_SEPARATOR . 'Mkb' . DIRECTORY_SEPARATOR . 'data.csv';
        $data = [];
        $file_handle = fopen($path, "rb");
        while (!feof($file_handle)) {
            $data[] = fgetcsv($file_handle);
        }
        fclose($file_handle);

        $this->createTable('mkb', [
            'id' => Schema::TYPE_PK,
            'name' => Schema::TYPE_TEXT,
            'code' => Schema::TYPE_STRING,
            'parent_id' => Schema::TYPE_INTEGER,
            'parent_code' => Schema::TYPE_STRING,
            'node_count' => Schema::TYPE_INTEGER,
            'additional_info' => Schema::TYPE_TEXT
        ]);

        $this->addForeignKey('mkb_parent_id_fkey', 'mkb', 'parent_id', 'mkb', 'id');

        foreach ($data as $item) {
            $this->insert('mkb', array(
                'id' => $item[0],
                'name' => $item[1],
                'code' => $item[2],
                'parent_id' => ($item[3] > 0) ? $item[3] : null,
                'parent_code' => $item[4] == 'NULL' ? null : $item[4],
                'node_count' => $item[5] == 'NULL' ? null : $item[5],
                'additional_info' => $item[6] == 'NULL' ? null : $item[6]
            ));
        }
    }

    public function down() {
        echo "m160108_083459_3 cannot be reverted.\n";

        return false;
    }

}
