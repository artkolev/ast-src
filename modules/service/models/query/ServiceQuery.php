<?php

namespace app\modules\service\models\query;

use app\modules\admin\components\DeepModelQuery;
use app\modules\service\models\Service;
use app\modules\users\models\UserAR;
use Yii;

class ServiceQuery extends DeepModelQuery
{
    /**
     * Поиск видимых услуг
     *
     * @return ServiceQuery $this
     */
    public function visible(): ServiceQuery
    {
        return $this->andWhere(['service.visible' => 1])
            ->andWhere(['IN', 'service.status', Service::CATALOG_VISIBLE_STATUSES]);
    }

    /**
     * Поиск видимости услуг по ролям
     *
     * @return ServiceQuery $this
     */
    public function visibleByRole($isCriteria = false): ServiceQuery
    {
        if (Yii::$app->user->isGuest) {
            /* гость приравнивается к физлицу */
            $role = 'fizusr';
        } else {
            $role = Yii::$app->user->identity->userAR->role;
        }
        switch ($role) {
            /* Эксперт */
            case 'expert':
                $this->andWhere(['service.vis_expert' => 1]);
                break;
            /* Экспертная организация */
            case 'exporg':
                $this->andWhere(['service.vis_exporg' => 1]);
                break;
            /* Юрлицо */
            case 'urusr':
                $this->andWhere(['service.vis_ur' => 1]);
                break;
            /* Для админа и МКС нет ограничений на области видимости */
            case 'admin':
            case 'mks':
                break;
            case 'finman':
                if ($isCriteria) {
                    break;
                }
                $this->andWhere(['service.vis_fiz' => 1]);
                break;
            case 'user':
                if ($isCriteria) {
                    $this->andWhere(['1=0']);
                    break;
                }
                $this->andWhere(['service.vis_fiz' => 1]);
                break;
            /* Для остальных область видимости как у Физлица */
            default:
                $this->andWhere(['service.vis_fiz' => 1]);
                break;
        }
        return $this;
    }

    /**
     * Поиск видимости услуг по автору
     *
     * @return ServiceQuery $this
     */
    public function visibleAuthor(): ServiceQuery
    {
        $this->leftJoin('user', 'user.id = service.user_id');
        /* отображать услуги от АСТ, либо от активного пользователя, не скрытого в каталогах */
        $this->andWhere(['OR', ['service.user_id' => 0], ['AND', ['user.status' => UserAR::STATUS_ACTIVE], ['user.visible' => 1]]]);
        return $this;
    }
}
