<?php

/*
	Классы для работы с графами (не с графиками!)
	@author Dmitry Nikiforov <axshavan@yandex.ru>
	@license http://www.gnu.org/licenses/gpl.html GNU GPL
	Вы можете использовать этот исходный код, вносить в него изменения,
	распространять его, делать с ним вообще всё, что хотите, при условии того,
	что вы будете сохранять ссылку на первоначального автора и сохранять код
	открытым. И автор этого кода не несёт за него никакой ответственности.
*/

/*
	Класс, описывающий узел графа.
*/
class CGraphNode
{
	public    $id;          // для самоидентификации
	public    $data;        // некие данные
	protected $_children;   // массив ссылок на детей
	protected $parent_node; // сылка на родителя
	
	/*
		Конструктор.
		@param mixed $id что-нибудь, чтоб узел графа знал себя по имени
	*/
	public function __construct($id, $data = false)
	{
		$this->id          = $id;
		$this->_children   = array();
		if($data)
		{
			$this->data = $data;
		}
	}
	
	/*
		Деструктор.
	*/
	public function __destruct()
	{
		$this->KillChildren();
	}
	
	/*
		Добавить ребёнка узлу.
		@param  CGraphNode $node узел, добавляемый в качестве ребёнка
		@return bool       success
	*/
	public function AppendChild(&$node)
	{
		$bAdd = true;
		// если этот узел уже есть в качестве ребёнка, то добавлять не надо
		foreach($this->_children as $k => &$child)
		{
			if($child->id == $node->id)
			{
				$bAdd = false;
				break;
			}
		}
		// проверка на отсутствие циклов
		$obj = &$this->parent_node;
		while($obj != null)
		{
			if($obj->id == $node->id)
			{
				$bAdd = false;
			}
			$obj = &$obj->parent_node;
		}
		// можно добавлять
		if($bAdd)
		{
			$this->_children[] = $node;
			$node->SetParent($this);
		}
		return $bAdd;
	}
	
	/*
		Создать узлу графа ребёнка.
		@param  mixed      $id    с каким айдишником будет ребёнок
		@param  mixed      $_data внутренние данные, которые он будет хранить
		@return CGraphNode созданный ребёнок
	*/
	public function CreateChild($id, $data = false)
	{
		$new_child = new CGraphNode($id, $data);
		$new_child->MakeAdoptedBy($this);
		return $new_child;
	}
	
	/*
		Найти среди детей графа ребёнка с нужным идентификатором.
		@param  mixed      $id идентификатор
		@return CGraphNode найденный ребёнок или false
	*/
	public function FindChildById($id)
	{
		if($id == $this->id)
		{
			// если это я, то верну себя
			return $this;
		}
		foreach($this->_children as $k => &$child)
		{
			// если это не я, то рекурсивно опрошу детей
			$result = &$child->FindChildById($id);
			if($result)
			{
				return $result;
			}
		}
		return false;
	}
	
	/*
		Получить узел и его детей в виде массива.
		@param  bool $bWithData включать в массив содержимое $this->data или нет
		@return array
	*/
	public function GetAsArray($bWithData)
	{
		$result = array(
			'id' => $this->id
		);
		if($bWithData)
		{
			$result['data'] = $this->data;
		}
		if(sizeof($this->_children))
		{
			$result['children'] = array();
			foreach($this->_children as &$child)
			{
				$result['children'][$child->id] = $child->GetAsArray($bWithData);
			}
		}
		return $result;
	}
	
	/*
		Выбрать все идентификаторы детей этого узла. Не рекурсивно, только
		одно поколение.
		@return string
	*/
	public function GetChildrenIds()
	{
		$str = '';
		foreach($this->_children as $k => &$child)
		{
			$str .= $child->id.',';
		}
		return $str;
	}
	
	/*
		Выбрать все идентификаторы все родителей этого узле рекурсивно.
		@return string
	*/
	public function GetParentsIds()
	{
		$obj = &$this->parent_node;
		$ids = '';
		while($obj != null)
		{
			$ids .= $obj->id.',';
			$obj  = &$obj->parent_node;
		}
		return $ids;
	}
	
	/*
		Рекурсивно перебить всех детей данного узла. Вызывается из деструктора.
	*/
	public function KillChildren()
	{
		foreach($this->_children as $k => &$child)
		{
			$child->Orphanize();
			$child->__destruct();
			unset($child);
			$child = null;
		}
		unset($this->_children);
		$this->_children = array();
	}
	
	/*
		Усыновить данный узел другим узлом.
		@param  CGraphNode $node новый родитель
		@return bool       success
	*/
	public function MakeAdoptedBy(&$node)
	{
		return $node->AppendChild($this);
	}
	
	/*
		Отобрать узел у родителя, разорвав родственную связь.
	*/
	public function Orphanize()
	{
		if($this->parent_node != null)
		{
			// надо сказать родителю, что этого ребёнка у него больше нет
			$this->parent_node->RemoveChildById($this->id);
			$this->parent_node = null;
		}
	}
	
	/*
		Убрать у узла одного из детей по выбранному идентификатору.
		@param mixed $id идентификатор
	*/
	public function RemoveChildById($id)
	{
		// тут жуткая заморочка, чтоб случайно не перебить всех детей по ссылке
		$_children = &$this->_children;
		$this->_children = array();
		foreach($_children as $k => &$child)
		{
			if($child->id != $id)
			{
				$this->_children[$k] = &$child;
			}
		}
	}
	
	/*
		Служебная функция для сброса ссылки на родительский узел и установки
		нового родителя. Лучше пользоваться функцией CGraphNode::MakeAdoptedBy,
		чтоб не нарушить связи между узлами.
		@param CGraphNode $node новый родитель
	*/
	public function SetParent(&$node)
	{
		if($this->parent_node != null)
		{
			$this->parent_node->RemoveChildById($this->id);
		}
		$this->parent_node = $node;
	}
	
	/*
		Служебная функция для отладки. Показать свой айдишник и всех детей
		рекурсивно.
		@param int  $spaces уровень вложенности текущего узла
		@param bool $bShowData показывать или нет $this->data
	*/
	public function Trace($spaces = 0, $bShowData = false)
	{
		$s = str_repeat(' ', $spaces);
		echo $s.$this->id." => (\n";
		if($this->data && $bShowData)
		{
			echo $s."  [data] =>\n".$s."  {\n";
			$out = explode("\n", print_r($this->data, true));
			foreach($out as $str)
			{
				echo $s.'    '.$str."\n";
			}
			echo $s."  }\n";
		}
		foreach($this->_children as $k => &$child)
		{
			$child->Trace($spaces + 2, $bShowData);
		}
		echo $s.")\n";
	}
}

/*
	Класс, описывающий граф
*/
class CGraph
{
	protected $root_node; // корневой узел графа
	
	/*
		Создать граф из массива.
		@param array $_array массив вида array(
			'id' => array('parent_id' => parent_id, ...)
		)
		@param  string $pid_key_name название ключа, который является идентификатором родителя
		@return true или остатки массива $_array, не вписавшиеся в граф
	*/
	public function CreateFromArray($_array, $pid_key_name = 'parent_id')
	{
		// если граф уже был, надо его грохнуть
		if($this->root_node != null)
		{
			$this->root_node->__destruct();
		}
		$this->root_node = new CGraphNode(0);
		
		// первый проход
		foreach($_array as $id => $_node)
		{
			$node = &$this->root_node->FindChildById($_node[$pid_key_name]);
			if($node)
			{
				$node->CreateChild($id, $_node);
				unset($_array[$id]);
			}
		}
		
		// второй проход. Наверное, достаточно
		foreach($_array as $id => $_node)
		{
			$node = &$this->root_node->FindChildById($_node[$pid_key_name]);
			if($node)
			{
				$node->CreateChild($id, $_node);
				unset($_array[$id]);
			}
		}
		if(count($_array))
		{
			return $_array;
		}
		else
		{
			return true;
		}
	}
	
	/*
		Достать список детей для узла с данным идентификатором.
		@param  mixed $id идентификатор
		@return string или false
	*/
	public function GetChildrenForId($id)
	{
		$obj = &$this->root_node->FindChildById($id);
		if($obj)
		{
			return trim($obj->GetChildrenIds());
		}
		else
		{
			return false;
		}
	}
	
	/*
		Вернуть список всех родителей для узла с данным идентификатором.
		@param  mixed $id идентификатор
		@return string или false
	*/
	public function GetParentsForId($id)
	{
		$obj = &$this->root_node->FindChildById($id);
		if($obj)
		{
			return trim($obj->GetParentsIds());
		}
		else
		{
			return false;
		}
	}
	
	/*
		Получить весь граф в виде массива, в котором дети узла представлены
		элементами массива, который является сам элементом массива, являющегося
		узлом.
		@param bool $bWithData включать или нет в массив данные, хранящиеся в узлах
	*/
	public function GetAsArray($bWithData = false)
	{
		return $this->root_node->GetAsArray($bWithData);
	}
	
	/*
		Показать граф для отладки.
		@param bool $bShowData включать или нет в вывод данные, хранящиеся в узлах
	*/
	public function Trace($bShowData = false)
	{
		echo '<pre>';
		$this->root_node->Trace(0, $bShowData);
		echo '</pre>';
	}
}

?>