<?php

namespace app\modules\admin\behaviors;

use app\modules\admin\components\FilestoreModel;
use Imagine\Image\ManipulatorInterface;
use Yii;
use yii\base\Behavior;
use yii\db\BaseActiveRecord;
use yii\helpers\FileHelper;
use yii\helpers\Url;
use yii\imagine\Image;
use yii\validators\Validator;
use yii\web\UploadedFile;

class SaveFilesRelation extends Behavior
{
    public $relations = [];

    public $file_path = 'files/upload/';

    public $thumb_path = 'files/thumbs/';

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
                switch ($options['type']) {
                    case "single":
                        if ($this->owner->{$relname . '_loader'} instanceof UploadedFile) {
                            $fileInstance = $this->owner->{$relname . '_loader'};
                        } else {
                            $fileInstance = UploadedFile::getInstance($this->owner, $relname . '_loader');
                        }
                        if (!empty($fileInstance)) {
                            // ищем предыдущие загруженные файлы и удаляем
                            if ($this->owner->{$relname}) {
                                $this->owner->{$relname}->delete();
                            }
                            $new_file_model = new FilestoreModel();
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
                        break;
                    case "multiple":
                        $is_instances = true;
                        if (!empty($this->owner->{$relname . '_loader'})) {
                            foreach ($this->owner->{$relname . '_loader'} as $key => $data) {
                                if (!($data instanceof UploadedFile)) {
                                    $is_instances = false;
                                }
                            }
                        }
                        if ($is_instances) {
                            $fileInstances = $this->owner->{$relname . '_loader'};
                        } else {
                            $fileInstances = UploadedFile::getInstances($this->owner, $relname . '_loader');
                        }

                        if (!empty($fileInstances)) {
                            foreach ($fileInstances as $key => $fileInstance) {
                                $new_file_model = new FilestoreModel();
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
                        break;
                    case "multirow":
                        // за основу берем multiple и добавляем сохранение id в поле multifield
                        // загрузка изображений не из админки не предусмотрено. (нельзя передать инстансы изображений)
                        // для multirow в параметрах обязательно должен быть указан аттрибут типа multifield
                        // сохранение изображений происходит в afterSave, поэтому поле multifield не может быть пустым, если переданы данные для загрузки изображений.
                        if (!empty($options['multifield']) && !empty($options['multifield_fieldname']) && !empty($this->owner->{$options['multifield']})) {
                            $field_data = $this->owner->{$options['multifield']};
                            // поле multifield может быть либо массивом, либо сериализованной строкой
                            $need_serialize = false;
                            if (!is_array($field_data)) {
                                // запаковать обратно после обработки
                                $need_serialize = true;
                                $field_data = unserialize($field_data);
                            }

                            $actual_images_list = [];
                            $existing_images = array_column($this->owner->{$relname}, null, 'id');

                            foreach ($field_data as $key_field => $field_content) {

                                // если поле непроинициализировано - делаем его пустым массивом
                                if (!is_array($field_data[$key_field][$options['multifield_fieldname']])) {
                                    $field_data[$key_field][$options['multifield_fieldname']] = [];
                                }

                                /* если первый элемент массива - загруженный файл, то и остальные должны быть такими же */
                                if ($this->owner->{$relname . '_loader'}[$key_field][0] instanceof \yii\web\UploadedFile) {
                                    $fileInstances = $this->owner->{$relname . '_loader'}[$key_field];
                                } else {
                                    // для каждой записи в массиве поля пробуем получить файлы
                                    $fileInstances = UploadedFile::getInstances($this->owner, $relname . '_loader[' . $key_field . ']');
                                }

                                // дублируем код сохранения файлов из multiple
                                if (!empty($fileInstances)) {
                                    if ($options['multifield_type'] == 'single') {
                                        // если для каждой ячейки допустимо только 1 изображение, то удалить все предыдущие
                                        foreach ($field_data[$key_field][$options['multifield_fieldname']] as $key => $id_image) {
                                            unset($field_data[$key_field][$options['multifield_fieldname']][$key]);
                                        }
                                    } else {
                                        // перед сохранением новых файлов из $field_data удалить пустые индексы (номера изображений, которых нет в базе)
                                        foreach ($field_data[$key_field][$options['multifield_fieldname']] as $key => $id_image) {
                                            if (!isset($existing_images[$id_image])) {
                                                unset($field_data[$key_field][$options['multifield_fieldname']][$key]);
                                            }
                                        }
                                    }
                                    // сбрасываем ключи массива с изображениями
                                    $data_keys = array_values($field_data[$key_field][$options['multifield_fieldname']]);
                                    $field_data[$key_field][$options['multifield_fieldname']] = $data_keys;

                                    foreach ($fileInstances as $key => $fileInstance) {
                                        $new_file_model = new FilestoreModel();
                                        $new_file_model->isMain = ($key == 0);
                                        $new_file_model->file_path = $this->file_path;
                                        $new_file_model->order = $key;
                                        $new_file_model->keeper_id = $this->owner->id;
                                        $new_file_model->keeper_class = (array_key_exists('ownerClass', $options) && $options['ownerClass'] ? $options['ownerClass'] : $this->owner->className());
                                        $new_file_model->keeper_field = $relname;
                                        $new_file_model->new_name = strtolower($this->owner->formName()) . '_' . time() . rand(10, 99) . $key;
                                        $new_file_model->file_loader = $fileInstance;
                                        $new_file_model->description = '';
                                        $this->owner->link($relname, $new_file_model);
                                        // записываем id полученной записи в поле multifield
                                        $field_data[$key_field][$options['multifield_fieldname']][] = $new_file_model->id;
                                    }
                                }
                                // добавить существующие id из ячейки мультифилда
                                $actual_images_list = array_merge($actual_images_list, $field_data[$key_field][$options['multifield_fieldname']]);
                            }
                            // после всех манипуляций удаляем физически файлы, которых нет в редактируемом multifield-поле
                            $existing_images_ids = array_column($existing_images, 'id', 'id');
                            $files_to_delete = array_diff($existing_images_ids, $actual_images_list);
                            if (!empty($files_to_delete)) {
                                foreach ($files_to_delete as $file_id) {
                                    $file = $existing_images[$file_id];
                                    $file->delete();
                                }
                            }
                            // после сохранения файлов и редактирования содержимого multifield-поля сохраняем поле методом updateAttributes
                            // проверить поведение для SaveOneVar() - скорее всего не заработает
                            $field_data_serialize = serialize($field_data);
                            // в базу сразу в сериализованном виде
                            $this->owner->updateAttributes([$options['multifield'] => $field_data_serialize]);
                            // в модель сохраняем в том виде, в котором получили
                            $this->owner->{$options['multifield']} = ($need_serialize ? $field_data_serialize : $field_data);
                        } else {
                            $this->owner->addError($relname, 'Не указаны параметры сохранения файлов');
                        }
                        break;
                }
            }
        }
    }

    public function beforeDelete()
    {
        foreach ($this->relations as $relname => $options) {
            if (($options['type'] == 'single') and ($this->owner->{$relname} instanceof \app\modules\admin\components\FilestoreModel)) {
                $this->owner->{$relname}->delete();
            } elseif ($options['type'] == 'multiple') {
                if (!empty($this->owner->{$relname})) {
                    foreach ($this->owner->{$relname} as $key => $document) {
                        if ($document instanceof \app\modules\admin\components\FilestoreModel) {
                            $document->delete();
                        }
                    }
                }
            }
        }
    }

    public function getWebp($relation, $thumb_name, $index = false, $id_file = false)
    {
        // получаем адрес превью
        $thumb_path = $this->getThumb($relation, $thumb_name, $index, $id_file);

        // получаем файл превью
        $thumb_file = Yii::getAlias('@webroot') . DIRECTORY_SEPARATOR . $thumb_path;

        // проверяем наличие исходного файла
        if (is_file($thumb_file)) {

            // получаем расширение файла
            $parts = explode('.', $thumb_path);
            $extension = array_pop($parts);

            // получаем имя webp файла
            $parts[] = 'webp';
            $webp_path = implode('.', $parts);

            // получаем путь к webp файлу
            $webp_file = Yii::getAlias('@webroot') . DIRECTORY_SEPARATOR . $webp_path;
            if (!is_file($webp_file)) {
                // нарезать webp можно только из jpeg или png
                if (in_array($extension, ['jpg', 'jpeg', 'png'])) {
                    // загружаем изображение
                    switch ($extension) {
                        case 'jpeg':
                        case 'jpg':
                            $image = imagecreatefromjpeg($thumb_file);
                            break;
                        case 'png':
                            $image = imagecreatefrompng($thumb_file);
                            break;
                    }
                    // если загрузилось - преобразуем в webp
                    if ($image) {
                        imagewebp($image, $webp_file);

                        // немного магии Х)
                        $fpr = fopen($webp_file, "a+");
                        fwrite($fpr, chr(0x00));
                        fwrite($fpr, chr(0x00));
                        fclose($fpr);
                    }
                }
            }
        }
        // если в конце всех концов файл существет - отдаем
        if (is_file($webp_file)) {
            return $webp_path;
        }
        // иначе - sorry :(
        return false;

    }

    public function getThumb($relation, $thumb_name = '', $index = false, $id_file = false)
    {
        // если задано отношение
        if (isset($this->relations[$relation])) {
            // получаем имя оригинального файла из адреса
            if (!empty($this->owner->{$relation})) {
                if ($index !== false) {
                    $file_orig_path = $this->owner->{$relation}[$index]->src;
                } elseif ($id_file !== false) {
                    $images_array = array_column($this->owner->{$relation}, 'src', 'id');
                    $file_orig_path = $images_array[$id_file];
                } else {
                    $file_orig_path = $this->owner->{$relation}->src;
                }
            }
            if (empty($file_orig_path)) {
                // по умолчанию если нет оригинального файла
                return $this->relations[$relation]['default'] ?? false;
            }
            $tmp_var = explode('/', $file_orig_path);
            $orig_file_name = end($tmp_var);

            // получаем адрес превью
            $thumb_path = $this->thumb_path . $relation . '/' . $thumb_name . '/' . $orig_file_name;

            // получаем путь к файлу превью
            $thumb_file = Yii::getAlias('@webroot') . DIRECTORY_SEPARATOR . $thumb_path;

            // получаем путь до оригинального файла
            $orig_file = Yii::getAlias('@webroot') . DIRECTORY_SEPARATOR . $file_orig_path;

            // проверяем, что файл существует
            if (is_file($orig_file)) {

                // проверка на анимированный gif
                $ext = pathinfo($orig_file, PATHINFO_EXTENSION);
                if ($ext == 'gif' && $this->is_ani_gif($orig_file)) {
                    return '/' . trim($file_orig_path, '/');
                }

                // svg
                if ($ext == 'svg') {
                    return '/' . trim($file_orig_path, '/');
                }

                // если превью-файл не существует - создать превью
                if (!is_file($thumb_file)) {

                    // если описан тип превью - разбираем дальше
                    if (isset($this->relations[$relation][$thumb_name])) {

                        // смотрим параметры
                        $width = $this->relations[$relation][$thumb_name]['width'];
                        $height = $this->relations[$relation][$thumb_name]['height'];
                        $quality = $this->relations[$relation][$thumb_name]['quality'];

                        // выбираем тип превью
                        switch ($this->relations[$relation][$thumb_name]['mode']) {
                            case 'inset':
                                $mode = ManipulatorInterface::THUMBNAIL_INSET;
                                break;
                            case 'outbound':
                            default:
                                $mode = ManipulatorInterface::THUMBNAIL_OUTBOUND;
                                break;
                        }

                        // рассчет ширины и высоты
                        if (!$width || !$height) {
                            try {
                                $image = Image::getImagine()->open($orig_file);
                                $ratio = $image->getSize()->getWidth() / $image->getSize()->getHeight();
                            } catch (\Exception $e) {
                                // по умолчанию
                                return $this->relations[$relation]['default'] ?? false;
                            }
                            if ($width) {
                                $height = ceil($width / $ratio);
                            } else {
                                $width = ceil($height * $ratio);
                            }
                        }
                        // Fix error "PHP GD Allowed memory size exhausted".
                        ini_set('memory_limit', '512M');

                        // если директория для превью не существует - создаем
                        if (!FileHelper::createDirectory(dirname($thumb_file), 0700)) {
                            // по умолчанию
                            return $this->relations[$relation]['default'] ?? false;
                        }
                        try {
                            Image::thumbnail($orig_file, $width, $height, $mode)->save($thumb_file, ['quality' => $quality]);
                        } catch (\Exception $e) {
                            // по умолчанию
                            return $this->relations[$relation]['default'] ?? false;
                        }
                    } else {
                        // если тип превью не задан - возвращаем оригинальное изображение
                        return '/' . trim($file_orig_path, '/');
                    }
                }
                // возвращаем путь к превью
                return '/' . trim($thumb_path, '/');
            }
            return false;
        }

        // если отношение не задано - ничего не возвращаем
        return false;

    }

    public function is_ani_gif($filename)
    {
        if (!($fh = @fopen($filename, 'rb'))) {
            return false;
        }
        $count = 0;
        // an animated gif contains multiple "frames", with each frame having a
        // header made up of:
        // * a static 4-byte sequence (\x00\x21\xF9\x04)
        // * 4 variable bytes
        // * a static 2-byte sequence (\x00\x2C)

        // We read through the file til we reach the end of the file, or we've found
        // at least 2 frame headers
        while (!feof($fh) && $count < 2) {
            $chunk = fread($fh, 1024 * 100); // read 100kb at a time
            $count += preg_match_all('#\x00\x21\xF9\x04.{4}\x00[\x2C\x21]#s', $chunk, $matches);
        }

        fclose($fh);
        return $count > 1;
    }

    public function getFile($relation, $index = false, $id_file = false)
    {
        // если задано отношение
        if (isset($this->relations[$relation])) {

            if ($index !== false) {
                $file_orig_path = $this->owner->{$relation}[$index]->src;
                $file_id = $this->owner->{$relation}[$index]->id;
            } elseif ($id_file !== false) {
                $files_array = array_column($this->owner->{$relation}, 'src', 'id');
                $file_orig_path = $files_array[$id_file];
                $file_id = $id_file;
            } else {
                $file_orig_path = $this->owner->{$relation}->src;
                $file_id = $this->owner->{$relation}->id;
            }

            // получаем путь до оригинального файла
            if (Yii::$app instanceof \yii\console\Application) {
                $home_url = Yii::getAlias('@app') . DIRECTORY_SEPARATOR . 'web';
            } else {
                $home_url = Yii::getAlias('@webroot');
            }
            $orig_file = $home_url . DIRECTORY_SEPARATOR . $file_orig_path;

            // проверяем, что файл существует
            if (is_file($orig_file)) {

                /* возвращаем путь до скачивания */
                return Url::toRoute(['/site/prettyfile', 'file_id' => $file_id]);

            }
            /* иначе возвращаем пустую ссылку */
            return false;

        }
        // если отношение не задано - возвращаем пустую ссылку
        return false;
    }
}
