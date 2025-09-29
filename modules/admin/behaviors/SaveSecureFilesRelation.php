<?php

namespace app\modules\admin\behaviors;

use app\modules\admin\components\SecureFilestoreModel;
use Yii;
use yii\base\Behavior;
use yii\db\BaseActiveRecord;
use yii\helpers\Url;
use yii\validators\Validator;
use yii\web\UploadedFile;

class SaveSecureFilesRelation extends Behavior
{
    public $relations = [];

    public $file_path = 'secure_media/';

    public $thumb_path = 'secure_media/thumbs/';

    public function events()
    {
        return [
            BaseActiveRecord::EVENT_INIT => 'eventInit',

            BaseActiveRecord::EVENT_AFTER_INSERT => 'afterSave',

            BaseActiveRecord::EVENT_AFTER_UPDATE => 'afterSave',

            BaseActiveRecord::EVENT_BEFORE_DELETE => 'beforeDelete',
        ];
    }

    public function eventInit()
    {
        foreach ($this->relations as $relname => $options) {
            $validator = 'file';
            if (isset($options['validator'])) {
                $validator = $options['validator'];
            }
            $fileValidator = Validator::createValidator($validator, $this->owner, $relname . '_loader', $options['validate']);
            $this->owner->validators[] = $fileValidator;
        }
    }

    public function afterSave()
    {
        foreach ($this->relations as $relname => $options) {
            if ((Yii::$app instanceof \yii\web\Application) && Yii::$app->request->isPost) {
                if ($options['type'] == 'single') {
                    $fileInstance = UploadedFile::getInstance($this->owner, $relname . '_loader');
                    if (!empty($fileInstance)) {
                        // ищем предыдущие загруженные файлы и удаляем
                        if ($this->owner->{$relname}) {
                            $this->owner->{$relname}->delete();
                        }
                        $new_file_model = new SecureFilestoreModel();
                        $new_file_model->file_path = $this->file_path;
                        $new_file_model->keeper_id = $this->owner->id;
                        $new_file_model->isMain = true;
                        $new_file_model->order = 0;
                        $new_file_model->keeper_class = (array_key_exists('ownerClass', $options) && $options['ownerClass'] ? $options['ownerClass'] : $this->owner->className());
                        $new_file_model->keeper_field = $relname;
                        $new_file_model->new_name = strtolower($this->owner->formName()) . '_' . time() . rand(10, 99);
                        $new_file_model->file_loader = $fileInstance;
                        $new_file_model->description = '';
                        $res = $this->owner->link($relname, $new_file_model);

                    }
                } else {
                    $fileInstances = UploadedFile::getInstances($this->owner, $relname . '_loader');
                    if (!empty($fileInstances)) {
                        foreach ($fileInstances as $key => $fileInstance) {
                            $new_file_model = new SecureFilestoreModel();
                            $new_file_model->isMain = ($key == 0);
                            $new_file_model->file_path = $this->file_path;
                            $new_file_model->order = $key;
                            $new_file_model->keeper_id = $this->owner->id;
                            $new_file_model->keeper_class = (array_key_exists('ownerClass', $options) && $options['ownerClass'] ? $options['ownerClass'] : $this->owner->className());
                            $new_file_model->keeper_field = $relname;
                            $new_file_model->new_name = strtolower($this->owner->formName()) . '_' . time() . rand(10, 99) . $key;
                            $new_file_model->file_loader = $fileInstance;
                            $new_file_model->description = '';
                            $res = $this->owner->link($relname, $new_file_model);
                        }

                    }
                }
            }
        }
    }

    public function beforeDelete()
    {
        foreach ($this->relations as $relname => $options) {
            if (($options['type'] == 'single') and ($this->owner->{$relname} instanceof \app\modules\admin\components\SecureFilestoreModel)) {
                $this->owner->{$relname}->delete();
            } elseif ($options['type'] == 'multiple') {
                if (!empty($this->owner->{$relname})) {
                    foreach ($this->owner->{$relname} as $key => $document) {
                        if ($document instanceof \app\modules\admin\components\SecureFilestoreModel) {
                            $document->delete();
                        }
                    }
                }
            }
        }
    }

    public function getFile($relation, $index = false)
    {
        // если задано отношение
        if (isset($this->relations[$relation])) {

            if ($index !== false) {
                $file_orig_path = $this->owner->{$relation}[$index]->src;
                $file_id = $this->owner->{$relation}[$index]->id;
            } else {
                $file_orig_path = $this->owner->{$relation}->src;
                $file_id = $this->owner->{$relation}->id;
            }

            // получаем путь до оригинального файла
            $home_url = Yii::getAlias('@app');
            $orig_file = $home_url . DIRECTORY_SEPARATOR . $file_orig_path;

            // проверяем, что файл существует
            if (is_file($orig_file)) {

                /* возвращаем путь до скачивания */
                return Url::toRoute(['/site/securefile', 'file_id' => $file_id]);

            }
            /* иначе возвращаем пустую ссылку */
            return false;

        }
        // если отношение не задано - возвращаем пустую ссылку
        return false;

    }
}
