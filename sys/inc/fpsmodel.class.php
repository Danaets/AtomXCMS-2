<?php/*---------------------------------------------\| @Author:       Andrey Brykin (Drunya)        || @Version:      1.1                           || @Project:      CMS                           || @Package       AtomX CMS                     || @subpackege    FpsModel class                || @copyright     ©Andrey Brykin 2010-2013      || @last mod      2014/10/25                    ||----------------------------------------------||											   || any partial or not partial extension         || CMS Fapos,without the consent of the         || author, is illegal                           ||----------------------------------------------|| Любое распространение                        || CMS Fapos или ее частей,                     || без согласия автора, является не законным    |\---------------------------------------------*//** * Base class FpsModel. He is parent for all models. * Also he is something like DataMapper and simple Model. */abstract class FpsModel {    /**     * @var string     */	public $Table;    /**     * @var array     */    public $Binded;    /**     * @var array     */    protected $relationsMap = array();    /**     * @var array     */	protected $has_one;    /**     * @var array     */	protected $has_many;    /**     * @var array     */    protected $BindedParams;    protected $orderParams = array(        'allowed' => array(),        'default' => '',    );		protected $errors;    const DQ = 'DIFFERENT_QUERIES';    const JQ = 'JOINED_QUERIES';    /**     * @param $module     */	public function __construct()	{	}	    public function getTable() {        return $this->Table;    }    public function getTotal($params = array())   	{   		$cnt = $this->getDbDriver()->select($this->Table, DB_COUNT, $params);   		return $cnt;   	}	/**     * @param $id     * @return bool     */	public function getById($id)	{        $query_params = $this->buildQuery();        $query_params['cond'][] = $this->Table . '.id = ' . $id;		$entity = $this->getDbDriver()->select($this->Table, DB_FIRST, $query_params);        if (!empty($entity)) $entity = $this->prepareOutputEntity($entity);	    return (!empty($entity[0])) ? $entity[0] : false;	}    public function prepareOutputEntity($records, $alias = null)    {        if (empty($records)) return array();        $records = $this->getAllAssigned($records);        $Register = Register::getInstance();        $entityClassName = $Register['ModManager']->getEntityNameFromModel(get_class($this));        $alias = (!empty($alias)) ? $alias : $this->Table . '_';        foreach ($records as $k => $record) {            if (is_object($record)) continue;            if (empty($this->Binded) || empty($this->RelatedEntities)) {                $records[$k] = new $entityClassName($record);            } else {                foreach ($this->Binded as $binded_key => $model) {                    foreach ($this->RelatedEntities as $key => $rules) {                        if ($binded_key !== $alias . $key) continue;                        if (is_string($rules['model']) && strstr($rules['model'], 'this.')) continue;					                        if (isset($record[$alias.$key]) && $rules['type'] === 'has_one') {                            $records_ = $model->prepareOutputEntity(array($record[$alias.$key]), $alias . $key . '_');                            $record[$key] = array_shift($records_);                            unset($record[$alias.$key]);                        } else if (isset($record[$alias.$key]) && $rules['type'] === 'has_many') {                            $record[$key] = $model->prepareOutputEntity($record[$alias.$key], $alias . $key . '_');                            unset($record[$alias.$key]);                        } else if (isset($record[$alias.$key]) && $rules['type'] === 'many_to_many') {							$model = $model->Binded[$binded_key . '_' . $key];                            $record[$key] = $model->prepareOutputEntity($record[$alias.$key], $alias . $key . '_');                            unset($record[$alias.$key]);						} else if (isset($record[$alias.$key]) && $rules['type'] === 'has_many_through') {                            $model = $model->Binded[$binded_key . '_' . $key];                            $record[$key] = $model->prepareOutputEntity($record[$alias.$key], $binded_key . '_' . $key . '_');                            unset($record[$alias.$key]);                        }                    }                }                $records[$k] = new $entityClassName($record);            }        }		        return array_values($records);    }	/**	 * Get a first entity with the needed conditions	 */    public function getFirst($where, $params = array())    {        $params['limit'] = 1;        $post = $this->getCollection($where, $params);        return (!empty($post[0])) ? $post[0] : false;    }    public function buildQuery(&$where = array(), &$addParams = array())    {        $this->getDbDriver()->relationsMap = $this->relationsMap;		        $params = array(            'cond' => array(),            'joins' => array(),            'alias' => $this->Table,        );        if (empty($this->Binded)) {            //unset($params['alias']);            return $params;        }        $binded_params = $this->Binded;        $aTable = $this->Table;        $relEntities = $this->RelatedEntities;        $parentAliases = array();        $parentRelEntities = array();        $parentBinded = array();        while (is_array($binded_params) && count($binded_params)) {            $key = key($binded_params);            $model = $binded_params[$key];            if (!is_object($model)) {                unset($binded_params[$key]);                continue;            }			            $bTable = $model->Table;            $foreignAlias = $key;            $key = str_replace($aTable . '_', '', $key);            $cond = array();            if (!empty($addParams) && is_array($addParams)) {                foreach ($addParams as $pk => &$pv) {                    if ('order' === $pk && strstr($pv, '.') && $tb_alias = explode('.', $pv)) {                        if ($key === $tb_alias[0])                            $pv = str_replace($key, $foreignAlias, $pv);                    }                }            }            if (!empty($where) && is_array($where)) {                foreach ($where as $pk => &$pv) {                    if (!is_numeric($pk) && strstr($pk, '.') && $col_alias = explode('.', $pk)) {                        if ($key === $col_alias[0]) {                            $where[str_replace($key, $foreignAlias, $pk)] = $pv;                            unset($where[$pk]);                        }                    }                }            }			if ('many_to_many' === $relEntities[$key]['type']) {				$cond = $aTable.'.id = ' . $foreignAlias.'.'.$relEntities[$key]['foreignKey'][0];				$params['joins'][] = array(					'cond' => $cond,					'type' => 'LEFT',					'alias' => $foreignAlias,					'table' => $bTable,				);								// second table (many to many)				$model = $model->Binded[$foreignAlias.'_'.$key];				$cond = $foreignAlias.'.'.$relEntities[$key]['foreignKey'][1]					.' = '.$foreignAlias.'_'.$key.'.id';				$params['joins'][] = array(					'cond' => $cond,					'type' => 'LEFT',					'alias' => $foreignAlias.'_'.$key,					'table' => $model->Table,				);            } else if ('has_many_through' === $relEntities[$key]['type']) {                $cond = $aTable.'.' . $relEntities[$key]['foreignKey'][0] . ' = ' . $foreignAlias.'.id';                $params['joins'][] = array(                    'cond' => $cond,                    'type' => 'LEFT',                    'alias' => $foreignAlias,                    'table' => $bTable,                );                unset($binded_params[$foreignAlias]);                // second table (many to many)                $model = $model->Binded[$foreignAlias.'_'.$key];                $cond = $foreignAlias.'.id = '.$foreignAlias.'_'.$key.'.' . $relEntities[$key]['foreignKey'][1];                $foreignAlias = $foreignAlias.'_'.$key;                $params['joins'][] = array(                    'cond' => $cond,                    'type' => 'LEFT',                    'alias' => $foreignAlias,                    'table' => $model->Table,                );			} else {				if (!empty($relEntities[$key]['foreignKey'])) {					$cond[] = $aTable.'.id = ' . $foreignAlias.'.'.$relEntities[$key]['foreignKey'];				}				if (!empty($relEntities[$key]['internalKey'])) {					$cond[] = $foreignAlias.'.id = ' . $aTable.'.'.$relEntities[$key]['internalKey'];				}				if (!empty($relEntities[$key]['rootForeignKey'])) {					$cond[] = $this->Table.'.id = ' . $foreignAlias.'.'.$relEntities[$key]['rootForeignKey'];				}				if (!empty($relEntities[$key]['additionCond'])) {					$cond = array_merge($cond, $relEntities[$key]['additionCond']);				}				$params['joins'][] = array(					'cond' => $cond,					'type' => 'LEFT',					'alias' => $foreignAlias,					'table' => $bTable,				);			}            unset($binded_params[$foreignAlias]);            if (!empty($model->Binded) && is_array($model->Binded)) {                if (!empty($binded_params)) {                    $parentBinded[] = $binded_params;                    $parentRelEntities[] = $relEntities;                    $parentAliases[] = (!$parentAliases) ? $this->Table : $foreignAlias;                }                $binded_params = $model->Binded;                $aTable = $foreignAlias;                $relEntities = $model->RelatedEntities;            } else {                if (is_array($binded_params) && count($binded_params)) continue;                else {                    if (is_array($parentBinded) && count($parentBinded)) {                        $relEntities = array_pop($parentRelEntities);                        $binded_params = array_pop($parentBinded);                        $aTable = array_pop($parentAliases);                    }                    else $binded_params = false;                }            }        }        return $params;    }    public function bindModel($attrName, $params = array())    {        if (empty($attrName)) throw new Exception('"ModelName" for bindModel() must be not empty.');        $Register = Register::getInstance();        if (empty($this->RelatedEntities) || !is_array($this->RelatedEntities)) return false;        if (strstr($attrName, '.')) {            $attrs = explode('.', $attrName);            if (empty($attrs)) return false;        } else {            $attrs = array($attrName);        }        $previously_binded = false;        $previously_alias = $this->Table;        foreach ($attrs as $attrName) {            $currModel = ($previously_binded === false) ? $this : $previously_binded;            $Table = (!empty($previously_alias)) ? $previously_alias : $this->Table;            $RelatedEntities = $currModel->RelatedEntities;            $model = $RelatedEntities[$attrName]['model'];            if ('many_to_many' === $RelatedEntities[$attrName]['type'] ||            'has_many_through' === $RelatedEntities[$attrName]['type']) {                $type = $RelatedEntities[$attrName]['type'];				if (!is_array($RelatedEntities[$attrName]['model']) ||				!is_array($RelatedEntities[$attrName]['foreignKey']))					throw new Exception('Model and foreignKey must be arrays for the "'.$type.'" relation type.');									foreach ($RelatedEntities[$attrName]['model'] as $k => $model) {					if (empty($RelatedEntities[$attrName]['foreignKey'][$k]))						throw new Exception('"'.$type.'" relation type must have two foreignKeys.');					//pr($model);						$model = $Register['ModManager']->getModelInstance($model);					$currModel->Binded[$previously_alias . '_' . $attrName] = $model;												if (!array_key_exists($previously_alias, $this->relationsMap)) $this->relationsMap[$previously_alias] = array();					$this->relationsMap[$previously_alias][$previously_alias . '_' . $attrName] = array(						'type' => $RelatedEntities[$attrName]['type'],						'foreignKey' => $RelatedEntities[$attrName]['foreignKey'][$k],						'internalKey' => null,					);					 					$previously_binded = $model;					$previously_alias = $previously_alias . '_' . $attrName;					if ($k == 1) continue(2);										$currModel = ($previously_binded === false) ? $this : $previously_binded;					$model = $RelatedEntities[$attrName]['model'][1];				}							}  else if (strstr($model, 'this.') || (                    isset($currModel->RelatedEntities[$attrName]['relationType']) &&                    $currModel->RelatedEntities[$attrName]['relationType'] === self::DQ            )) {                $currModel->Binded[$attrName] = array(                    'model' => $model,                    'Binded' => ((!empty($attrs)) ? implode('.', array_slice($attrs, 1)) : ''),                );                if (!empty($params)) $currModel->setBindParams($attrName, $params);                break;            						}						            $model = $Register['ModManager']->getModelInstance($model);            foreach ($currModel->RelatedEntities as $relKey => $relEntity) {                if ($relKey === $attrName) {                    $currModel->Binded[$previously_alias . '_' . $attrName] = $model;                }            }            if (!array_key_exists($Table, $this->relationsMap)) $this->relationsMap[$Table] = array();            $this->relationsMap[$Table][$previously_alias . '_' . $attrName] = array(                'type' => $RelatedEntities[$attrName]['type'],                'foreignKey' => (!empty($RelatedEntities[$attrName]['foreignKey']))                        ? $RelatedEntities[$attrName]['foreignKey']                        : null,                'internalKey' => (!empty($RelatedEntities[$attrName]['internalKey']))                        ? $RelatedEntities[$attrName]['internalKey']                        : null,            );            if (!empty($RelatedEntities[$attrName]['rootForeignKey']))                $this->relationsMap[$Table][$previously_alias . '_' . $attrName]['rootForeignKey'] = $RelatedEntities[$attrName]['rootForeignKey'];            if (count($attrs) > 1) {                $previously_binded = $model;                $previously_alias = $previously_alias . '_' . $attrName;            }        }        return true;    }    public function getBindParams($attrName = false)    {        if (!$attrName) return $this->BindedParams;        return ($this->BindedParams[$attrName]) ? $this->BindedParams[$attrName] : false;    }	    public function setBindParams($attrName, $params)    {        $this->BindedParams[$attrName] = $params;    }    /**     * @param array $params     * @param array $addParams     * @return array|bool     */    public function getCollection($params = array(), $addParams = array())   	{        $params = (array)$params;        $query_params = $this->buildQuery($params, $addParams);        $query_params = array_merge($query_params, $addParams);        $query_params['cond'] = array_merge($query_params['cond'], $params);   		$entities = $this->getDbDriver()->select($this->Table, DB_ALL, $query_params);		        if (!empty($entities)) $entities = $this->prepareOutputEntity($entities);        return (!empty($entities)) ? $entities : array();   	}    /**     * @return array|bool     */    public function getRelatedEntitiesParams()    {        return (!empty($this->RelatedEntities)) ? $this->RelatedEntities : false;    }		public function getOneField($field, $params)	{		$output = array();		$result = $this->getDbDriver()->select($this->Table, DB_ALL, array(			'cond' => $params,			'fields' => array($field),		));				// "DISTINCT (id)" -> "id"		$field = preg_replace('#^[a-z]+\s*\((.+)\)\s*$#i', "$1", $field);		if (!empty($result)) {			foreach($result as $key => $record) {				$output[] = $record[$field];			}		}				return $output;	}			public function getError($errors)	{		return $this->errors;	}			public function delete($where)	{		try {			$this->getDbDriver()->delete($this->getTable(), $where);		} catch (Exception $e) {			$this->errors = $e->getMessage();			return false;		}		return true;	}		public function deleteByParentId($id)	{		$Register = Register::getInstance();		$where = array(			'entity_id' => $id,		);		//$records = $Register['DB']->select($this->Table, DB_ALL, array('cond' => $where));		$records = $this->getCollection($where);						if ($records) {			foreach ($records as $k => $v) {				$v->delete();			}		}	}			public function getMaterialsAttaches($entities, $module)	{		if (empty($entities) || !is_array($entities)) return array();				$Register = Register::getInstance();		$ids = array();		$allids = array();		foreach ($entities as $entity) {			$announce = ($module === 'forum') ? $entity->getMessage() : $entity->getMain();			if (preg_match_all('#\{ATTACH(\d+)(\|(\d+))?(\|(left|right))?(\|([^\|\}]+))?\}#', $announce, $matches)) {				$ids[$entity->getId()] = array();				foreach ($matches[1] as $id) {					$ids[$entity->getId()][] = $id;					$allids[] = $id;									}			}		}		if (!empty($allids)) {			$allids = implode(', ', array_unique($allids));			$attachesModel = $Register['ModManager']->getModelInstance($module . 'Attaches');			$attaches = $attachesModel->getCollection(array("`id` IN ($allids)"));						if ($attaches) {				foreach ($entities as $entity) {					$ent_attaches = array();					foreach ($attaches as $attach) {						if (!empty($ids[$entity->getId()]) && in_array($attach->getId(), $ids[$entity->getId()])) {							$ent_attaches[] = $attach;						}					}										if (!empty($ent_attaches)) {						$exists = $entity->getAttaches();						if (!empty($exists)) $ent_attaches = array_merge($exists, $ent_attaches);						$entity->setAttaches($ent_attaches);					}				}			}		}				return $entities;	}    public function getOrderParam()    {        $orderParams = $this->orderParams;        $getDefault = function() use ($orderParams) {            return (!empty($orderParams['default'])) ? $orderParams['default'] : '';        };        $order = (!empty($_GET['order'])) ? strtolower(trim($_GET['order'])) : '';        if (empty($orderParams['allowed']) || !in_array($order, $orderParams['allowed'])) {            $order = $getDefault();        }        if (!empty($order) && strstr($order, '.') && $tb_alias = explode('.', $order)) {            if (!empty($tb_alias[0]) && is_array($this->Binded) && count($this->Binded)) {                $test = $this->Table . '_' . $tb_alias[0];                if (array_key_exists($test, $this->Binded)) $order = str_replace($tb_alias[0], $test, $order);                else $order = $getDefault();            }        }        if (empty($order)) return '';        return (!empty($_GET['asc'])) ? $order . ' ASC' : $order . ' DESC';    }    public function getIdByHluTitle($hlu_id)    {        $entities = $this->getCollection(array('clean_url_title' => $hlu_id), array('limit' => 1, 'fields' => array('id')));        return (!empty($entities[0])) ? $entities[0]->getId() : false;    }    /**     * @return mixed     */    protected function getDbDriver()    {        $Register = Register::getInstance();        return $Register['DB'];    }    /**     *     * @param $records     * @return bool     */    protected function getAllAssigned($records)    {        if (empty($records) || count($records) < 1) return false;        if (empty($this->Binded) || !is_array($this->Binded)) return $records;        $Register = Register::getInstance();        //pr($this->RelatedEntities);        // Get all IDs from records        $hasOneKeys = array();        foreach ($records as $k => $r) {            // Also we must to collect foreign keys if they exists            if (!empty($this->RelatedEntities)) {                foreach ($this->RelatedEntities as $hok => $hov) {                    if (!array_key_exists($hok, $this->Binded)) continue;                    if (!array_key_exists($hok, $hasOneKeys))                        $hasOneKeys[$hok] = array('internal' => array(), 'foreign' => array());                    if (!empty($hov['foreignKey'])) {                        $hasOneKeys[$hok]['foreign'][$k] = $r['id'];                    }                    if (!empty($hov['internalKey'])) {                        if (!empty($r[$hov['internalKey']])) $hasOneKeys[$hok]['internal'][$k] = $r[$hov['internalKey']];                    }                }            }        }        // Look through all related tables        if (!empty($this->RelatedEntities)) {            foreach ($this->RelatedEntities as $relKey => $relVal) {                // Get data only from binded tables                if (!array_key_exists($relKey, $this->Binded)) continue;                if (!empty($hasOneKeys[$relKey]['internal'])) $internalOids = implode(', ', $hasOneKeys[$relKey]['internal']);                if (!empty($hasOneKeys[$relKey]['foreign'])) $foreignOids = implode(', ', $hasOneKeys[$relKey]['foreign']);                $model = $relVal['model'];                if (false !== (strpos($relVal['model'], 'this.'))) {                    $field = str_replace('this.', '', $relVal['model']);                    $model = $r[$field];                }                // Get model instance and concat IDs                $model = $Register['ModManager']->getModelInstance($model);                if (!empty($this->Binded[$relKey]['Binded']) && is_string($this->Binded[$relKey]['Binded'])) {                    $model->bindModel($this->Binded[$relKey]['Binded']);                }                $alias = $model->Table;                $where = array();                if (!empty($internalOids)) {                    $where[] = "`$alias`.`id` IN ($internalOids)";                }                if (!empty($foreignOids)) {                    $where[] = "`$alias`.`".$relVal['foreignKey']."` IN ($foreignOids)";                }                if (!empty($relVal['additionCond'])) {                    $where = array_merge($where, $relVal['additionCond']);                }                if ($this->getBindParams($relKey)) {                    $where = array_merge($where, $this->getBindParams($relKey));                }                // has one ...                if ($relVal['type'] === 'has_one') {                    $hasOneData = $model->getCollection($where, array('alias' => $alias));                    // merge has_one data                    if (is_array($hasOneData) && count($hasOneData)) {                        foreach ($hasOneData as $ok => $ov) {                            foreach ($records as $rk => $rv) {                                if (!empty($relVal['foreignKey']) && $rv['id'] !== $ov->{'get' . ucfirst($relVal['foreignKey'])}()) {                                    continue;                                }                                if (!empty($relVal['internalKey']) && $rv[$relVal['internalKey']] !== $ov->getId()) {                                    continue;                                }                                $records[$rk][$relKey] = clone $ov;                            }                        }                    }                    // and has_many...                } else if ($relVal['type'] === 'has_many') {                    $hasManyData = $model->getCollection($where, array('alias' => $alias));                    // merge has_many data                    if (!empty($hasManyData) && is_array($hasManyData)) {                        foreach ($hasManyData as $mk => $mv) {                            foreach ($records as $rk => $rv) {                                if (!array_key_exists($relKey, $records[$rk])) $records[$rk][$relKey] = array();                                if (!empty($relVal['foreignKey']) && $rv['id'] !== $mv->{'get' . ucfirst($relVal['foreignKey'])}()) {                                    continue;                                }                                if (!empty($relVal['internalKey']) && $rv[$relVal['internalKey']] !== $mv->getId()) {                                    continue;                                }                                $records[$rk][$relKey][] = clone $mv;                            }                        }                    } else {                        foreach ($records as $rk => $rv) {                            $records[$rk][$relKey] = array();                        }                    }                }            }        }        return $records;    }}