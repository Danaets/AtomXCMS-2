<?php
/*---------------------------------------------\
|											   |
| @Author:       Andrey Brykin (Drunya)        |
| @Version:      1.2                           |
| @Project:      CMS                           |
| @Package       AtomX CMS                     |
| @subpackege    Loads Model                   |
| @copyright     ©Andrey Brykin 2010-2014      |
| @last mod      2014/10/09                    |
|----------------------------------------------|
|											   |
| any partial or not partial extension         |
| CMS AtomX,without the consent of the         |
| author, is illegal                           |
|----------------------------------------------|
| Любое распространение                        |
| CMS AtomX или ее частей,                     |
| без согласия автора, является не законным    |
\---------------------------------------------*/



/**
 *
 */
class LoadsModel extends FpsModel
{
	public $Table = 'loads';

    protected $RelatedEntities = array(
        'author' => array(
            'model' => 'Users',
            'type' => 'has_one',
            'internalKey' => 'author_id',
      	),
        'category' => array(
            'model' => 'LoadsCategories',
            'type' => 'has_one',
            'internalKey' => 'category_id',
        ),
        'comments_' => array(
            'model' => 'Comments',
            'type' => 'has_many',
            'foreignKey' => 'entity_id',
			'additionCond' => array("module = 'loads'"),
        ),
        'attaches' => array(
            'model' => 'LoadsAttaches',
            'type' => 'has_many',
            'foreignKey' => 'entity_id',
        ),
    );

    protected $orderParams = array(
        'allowed' => array('views', 'date', 'comments', 'downloads'),
        'default' => 'date',
    );


    public function getById($id)
    {
        $entity = parent::getById($id);
        $entities = $this->getMaterialsAttaches(array($entity), 'loads');
        return $entities[0];
    }


    /**
     * @param array $params
     * @param array $addParams
     * @return array|bool
     */
    public function getCollection($params = array(), $addParams = array())
   	{
        $entities = parent::getCollection($params, $addParams);
		$entities = $this->getMaterialsAttaches($entities, 'loads');
		return $entities;
   	}
	
	
    /**
     * @param $user_id
     * @return array|bool
     */
    function getUserStatistic($user_id) {
        $user_id = intval($user_id);
        if ($user_id > 0) {
            $result = $this->getTotal(array('cond' => array('author_id' => $user_id)));
            if ($result) {
                $res = array(
                    'text' => __('Loads'),
                    'count' => intval($result),
                    'url' => get_url('/loads/user/' . $user_id),
                );

                return $res;
            }
        }
        return false;
    }
}