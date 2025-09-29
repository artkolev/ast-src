<?php

namespace app\modules\admin\behaviors;

use dosamigos\transliterator\TransliteratorHelper;
use yii\base\Behavior;
use yii\db\ActiveRecord;
use yii\helpers\Inflector;

class UrlBehaviour extends Behavior
{
    public $attributes = [];
    public $destination = "url";

    public function events()
    {
        return [
            ActiveRecord::EVENT_BEFORE_VALIDATE => 'bindUrl',
        ];
    }

    public function bindUrl($event)
    {
        if (!empty($this->owner->{$this->destination})) {
            // исключаем запрещенные символы
            $this->owner->{$this->destination} = preg_replace('/[^a-zA-Z0-9=\s—–_]+/u', '-', TransliteratorHelper::process($this->owner->{$this->destination}, '', 'en'));
            // проверять на уникальность!
            if (!$this->isUniqueSlug($this->owner->{$this->destination})) {
                // создаем ошибку, если она была уже до нас
                $this->owner->addError($this->destination, 'Такой Url уже используется');
            }
            return true;
        }
        // генерировать новый url
        $this->owner->{$this->destination} = $this->generateUrl();
    }

    private function isUniqueSlug($slug)
    {
        // проверяем наличие записи с таким же url
        $query = $this->owner->find()->where([$this->destination => $slug]);
        // если запись не новая - исключаем из поиска
        if (!$this->owner->isNewRecord) {
            $query->andWhere(['!=', 'id', $this->owner->id]);
        }
        // если модель с иерархической структурой
        if ($this->owner->hasAttribute('parent_id')) {
            // ищем url только на том же уровне вложенности (проверить при переносе в другой каталог)
            $query->andWhere(['parent_id' => $this->owner['parent_id']]);
        }
        $pages = $query->one();
        // url уникальный, если запись не найдена
        return empty($pages);
    }

    private function generateUrl()
    {
        // получаем транслитерацию указанных аттрибутов
        $slug = Inflector::slug(TransliteratorHelper::process($this->getSlug(), '', 'en'), '-');
        // пока не получим уникальный Url продолжаем дописывать символы
        while (!$this->isUniqueSlug($slug)) {
            $bytes = random_bytes(2);
            $slug .= '_' . bin2hex($bytes);
        }
        return $slug;
    }

    private function getSlug()
    {
        $slugs = [];

        foreach ($this->attributes as $attribute) {
            $attribute_parts = explode('.', $attribute);
            switch (count($attribute_parts)) {
                case 2:
                    $relation = $attribute_parts[0];
                    $name = $attribute_parts[1];
                    if (!empty($this->owner->{$relation}->{$name})) {
                        $slugs[] = $this->owner->{$relation}->{$name};
                    }
                    break;
                case 1:
                    $name = $attribute_parts[0];
                    if (!empty($this->owner->{$name})) {
                        $slugs[] = $this->owner->{$name};
                    }
                    break;
                default:
                    $this->owner->addError($this->destination, 'Неверный формат аттрибутов формирования Url');
            }
        }
        if (!empty($slugs)) {
            return implode('_', $slugs);
        }
        $this->owner->addError($this->destination, 'Аттрибуты, образующие Url не заполнены');

    }
}
